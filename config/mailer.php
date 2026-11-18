<?php

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';

const ARCHIVO_LOG_CORREO = __DIR__ . '/../logs/correo.log';

function obtenerConfiguracionApp(): array
{
    $configFile = __DIR__ . '/config.php';

    if (!is_file($configFile)) {
        registrarErrorCorreo('configuracion', 'No existe config/config.php en el servidor.');
        throw new RuntimeException(
            'Falta config/config.php. Créalo con los datos de la BD, app_url y el bloque mail.'
        );
    }

    $config = require $configFile;
    if (!is_array($config)) {
        registrarErrorCorreo('configuracion', 'config/config.php no devuelve un arreglo.');
        throw new RuntimeException('La configuración de la aplicación está incompleta.');
    }

    return $config;
}

function obtenerUrlAplicacion(): string
{
    $appUrl = obtenerConfiguracionApp()['app_url'] ?? null;

    if (!is_string($appUrl) || filter_var($appUrl, FILTER_VALIDATE_URL) === false) {
        registrarErrorCorreo('configuracion', 'Falta app_url o no es una URL válida en config/config.php.');
        throw new RuntimeException('Falta app_url en config/config.php.');
    }

    return rtrim($appUrl, '/');
}

// Oculta parte del correo para no dejar direcciones completas en el log: ju***@gmail.com
function ocultarCorreo(string $correo): string
{
    $partes = explode('@', $correo, 2);
    if (count($partes) !== 2) {
        return '[correo no válido]';
    }

    return mb_substr($partes[0], 0, 2) . '***@' . $partes[1];
}

// Escribe en logs/correo.log. Nunca debe lanzar errores ni recibir contraseñas.
function registrarErrorCorreo(string $contexto, string $detalle, ?string $destinatario = null): void
{
    try {
        $carpeta = dirname(ARCHIVO_LOG_CORREO);
        if (!is_dir($carpeta)) {
            @mkdir($carpeta, 0750, true);
        }

        $linea = sprintf(
            "[%s] [%s]%s %s\n",
            date('Y-m-d H:i:s'),
            $contexto,
            $destinatario === null ? '' : ' para=' . ocultarCorreo($destinatario),
            str_replace(["\r\n", "\r", "\n"], ' | ', trim($detalle))
        );

        @file_put_contents(ARCHIVO_LOG_CORREO, $linea, FILE_APPEND | LOCK_EX);
    } catch (Throwable $e) {
        // El registro es auxiliar; si falla no se interrumpe la respuesta al usuario.
    }
}

// Lanza PHPMailer\PHPMailer\Exception si el correo no se pudo enviar.
function enviarCorreo(string $para, string $nombre, string $asunto, string $html, string $texto): void
{
    $mail = obtenerConfiguracionApp()['mail'] ?? null;
    $clavesRequeridas = ['host', 'port', 'user', 'pass', 'from', 'from_name'];

    if (!is_array($mail)) {
        registrarErrorCorreo('configuracion', 'Falta el bloque mail en config/config.php.', $para);
        throw new RuntimeException('Falta la configuración de correo en config/config.php.');
    }
    foreach ($clavesRequeridas as $clave) {
        if (!isset($mail[$clave]) || $mail[$clave] === '') {
            registrarErrorCorreo('configuracion', "Falta mail.{$clave} en config/config.php.", $para);
            throw new RuntimeException('La configuración de correo está incompleta.');
        }
    }

    // Se guardan solo las respuestas del servidor; las líneas CLIENT -> SERVER
    // llevan el usuario y la contraseña codificados en base64.
    $respuestasServidor = [];

    $mailer = new PHPMailer(true);
    $mailer->isSMTP();
    $mailer->Host = $mail['host'];
    $mailer->Port = (int) $mail['port'];
    $mailer->SMTPAuth = true;
    $mailer->Username = $mail['user'];
    $mailer->Password = $mail['pass'];
    $mailer->SMTPSecure = (int) $mail['port'] === 465
        ? PHPMailer::ENCRYPTION_SMTPS
        : PHPMailer::ENCRYPTION_STARTTLS;
    $mailer->CharSet = PHPMailer::CHARSET_UTF8;
    $mailer->Timeout = 15;
    $mailer->SMTPDebug = SMTP::DEBUG_SERVER;
    $mailer->Debugoutput = static function (string $linea) use (&$respuestasServidor): void {
        if (str_starts_with(ltrim($linea), 'CLIENT -> SERVER')) {
            return;
        }
        $respuestasServidor[] = trim($linea);
    };

    try {
        $mailer->setFrom($mail['from'], $mail['from_name']);
        $mailer->addAddress($para, $nombre);
        $mailer->isHTML(true);
        $mailer->Subject = $asunto;
        $mailer->Body = $html;
        $mailer->AltBody = $texto;

        $mailer->send();
    } catch (PHPMailerException $e) {
        $ultimasRespuestas = array_slice(array_filter($respuestasServidor), -6);
        registrarErrorCorreo(
            'smtp ' . $mail['host'] . ':' . (int) $mail['port'],
            'ErrorInfo: ' . $mailer->ErrorInfo
                . ($ultimasRespuestas === [] ? '' : ' | Servidor: ' . implode(' | ', $ultimasRespuestas)),
            $para
        );
        throw $e;
    }
}
