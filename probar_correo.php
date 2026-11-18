<?php
// SCRIPT TEMPORAL DE DIAGNÓSTICO. Bórralo del servidor cuando termines.
// Uso: https://hiddenstage.io/probar_correo.php?clave=TU_CLAVE

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// 1) Cambia estas dos constantes antes de subirlo.
const CLAVE_PRUEBA = 'aisaisoasjosafmsnasffkapoo';
const DESTINO_PRUEBA = 'adiaz106@ucol.mx';

ini_set('display_errors', '1');
error_reporting(E_ALL);

$claveRecibida = $_GET['clave'] ?? '';
if (CLAVE_PRUEBA === 'aisaisoasjosafmsnasffkapoo'
    || !is_string($claveRecibida)
    || !hash_equals(CLAVE_PRUEBA, $claveRecibida)
) {
    http_response_code(404);
    exit;
}

header('Content-Type: text/html; charset=utf-8');
header('X-Robots-Tag: noindex');
set_time_limit(90);

function linea(string $texto): void
{
    echo htmlspecialchars($texto, ENT_QUOTES, 'UTF-8') . "\n";
    @ob_flush();
    flush();
}

function siNo(bool $valor): string
{
    return $valor ? 'SÍ' : 'NO';
}

echo '<!doctype html><meta charset="utf-8"><title>Prueba de correo</title><pre style="white-space:pre-wrap">';

linea('=== 1. Entorno ===');
linea('PHP: ' . PHP_VERSION);
linea('Extensión openssl cargada: ' . siNo(extension_loaded('openssl')));
linea('OpenSSL: ' . (defined('OPENSSL_VERSION_TEXT') ? OPENSSL_VERSION_TEXT : 'no disponible'));
linea('Fecha del servidor: ' . date('Y-m-d H:i:s T'));
linea('');

linea('=== 2. Configuración (config/config.php) ===');
$archivoConfig = __DIR__ . '/config/config.php';
linea('Existe config/config.php: ' . siNo(is_file($archivoConfig)));
if (!is_file($archivoConfig)) {
    linea('>> CAUSA: falta config/config.php en el servidor.');
    exit('</pre>');
}

require_once __DIR__ . '/config/mailer.php';

$config = obtenerConfiguracionApp();
$appUrl = $config['app_url'] ?? null;
linea('app_url definida: ' . siNo(is_string($appUrl) && $appUrl !== '')
    . (is_string($appUrl) ? '  (' . $appUrl . ')' : ''));
if (is_string($appUrl) && str_contains($appUrl, 'localhost')) {
    linea('>> AVISO: app_url apunta a localhost; los enlaces del correo no funcionarán en producción.');
}

$mail = $config['mail'] ?? null;
linea('Bloque mail definido: ' . siNo(is_array($mail)));
if (!is_array($mail)) {
    linea('>> CAUSA: falta el bloque mail en config/config.php.');
    exit('</pre>');
}

$faltantes = [];
foreach (['host', 'port', 'user', 'pass', 'from', 'from_name'] as $clave) {
    $presente = isset($mail[$clave]) && $mail[$clave] !== '';
    if (!$presente) {
        $faltantes[] = $clave;
    }
    $detalle = in_array($clave, ['host', 'port'], true) && $presente ? '  (' . $mail[$clave] . ')' : '';
    linea("mail.{$clave}: " . siNo($presente) . $detalle);
}
if ($faltantes !== []) {
    linea('>> CAUSA: faltan claves en mail: ' . implode(', ', $faltantes));
    exit('</pre>');
}
linea('Longitud de mail.pass: ' . strlen((string) $mail['pass'])
    . ' caracteres (una contraseña de aplicación de Google tiene 16, o 19 con espacios)');
linea('');

linea('=== 3. Red ===');
$host = (string) $mail['host'];
$ip = gethostbyname($host);
linea("DNS {$host}: " . ($ip === $host ? 'NO RESUELVE' : $ip));
foreach ([587, 465] as $puerto) {
    $inicio = microtime(true);
    $socket = @fsockopen(($puerto === 465 ? 'ssl://' : '') . $host, $puerto, $errno, $errstr, 10);
    $ms = (int) ((microtime(true) - $inicio) * 1000);
    if ($socket) {
        linea("Puerto {$puerto}: ABIERTO ({$ms} ms)");
        fclose($socket);
    } else {
        linea("Puerto {$puerto}: FALLÓ ({$ms} ms) -> [{$errno}] {$errstr}");
    }
}
linea('');

linea('=== 4. Conversación SMTP ===');
$usuario = (string) $mail['user'];
$password = (string) $mail['pass'];
$secretos = array_filter([
    $password,
    str_replace(' ', '', $password),
    base64_encode($password),
    base64_encode($usuario),
    base64_encode("\0" . $usuario . "\0" . $password),
]);

$mailer = new PHPMailer(true);
$mailer->isSMTP();
$mailer->Host = $host;
$mailer->Port = (int) $mail['port'];
$mailer->SMTPAuth = true;
$mailer->Username = $usuario;
$mailer->Password = $password;
$mailer->SMTPSecure = (int) $mail['port'] === 465
    ? PHPMailer::ENCRYPTION_SMTPS
    : PHPMailer::ENCRYPTION_STARTTLS;
$mailer->CharSet = PHPMailer::CHARSET_UTF8;
$mailer->Timeout = 15;
$mailer->SMTPDebug = SMTP::DEBUG_CONNECTION;
$mailer->Debugoutput = static function (string $texto) use ($secretos): void {
    linea(rtrim(str_replace($secretos, '[OCULTO]', $texto)));
};

try {
    $mailer->setFrom((string) $mail['from'], (string) $mail['from_name']);
    $mailer->addAddress(DESTINO_PRUEBA);
    $mailer->isHTML(true);
    $mailer->Subject = 'Prueba de correo PrivateStorage ' . date('H:i:s');
    $mailer->Body = '<p>Si recibes este correo, el SMTP de PrivateStorage funciona.</p>';
    $mailer->AltBody = 'Si recibes este correo, el SMTP de PrivateStorage funciona.';
    $mailer->send();
    linea('');
    linea('=== 5. Resultado: OK. Revisa la bandeja de ' . DESTINO_PRUEBA . ' (y la carpeta de spam). ===');
} catch (Throwable $e) {
    linea('');
    linea('=== 5. Resultado: ERROR ===');
    linea('ErrorInfo: ' . str_replace($secretos, '[OCULTO]', $mailer->ErrorInfo));
    linea('Excepción: ' . str_replace($secretos, '[OCULTO]', $e->getMessage()));
}

echo '</pre>';
