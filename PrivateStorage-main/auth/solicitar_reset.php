<?php

ob_start();
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

const MINUTOS_EXPIRACION_RESET = 30;
const MINUTOS_ESPERA_RESET = 2;
const MENSAJE_RESET_ENVIADO = 'Si el correo está registrado, recibirás un enlace para restablecer tu contraseña.';

function responderJson(array $respuesta, int $codigoHttp = 200): never
{
    ini_set('display_errors', '0');

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    http_response_code($codigoHttp);
    header('Content-Type: application/json; charset=utf-8');

    $json = json_encode(
        $respuesta,
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );

    echo $json === false
        ? '{"success":false,"message":"Ocurrió un error interno del servidor."}'
        : $json;
    exit;
}

set_error_handler(
    static function (int $severity, string $message, string $file, int $line): bool {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        throw new ErrorException($message, 0, $severity, $file, $line);
    }
);

register_shutdown_function(
    static function (): void {
        $error = error_get_last();
        $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];

        if ($error === null || !in_array($error['type'], $fatalTypes, true)) {
            return;
        }

        error_log(sprintf(
            'Error fatal en auth/solicitar_reset.php: %s en %s:%d',
            $error['message'],
            $error['file'],
            $error['line']
        ));
        responderJson([
            'success' => false,
            'message' => 'Ocurrió un error interno del servidor.'
        ], 500);
    }
);

header('Content-Type: application/json; charset=utf-8');

try {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        responderJson([
            'success' => false,
            'message' => 'Solamente se permite el método POST.'
        ], 405);
    }

    $correo = $_POST['correo'] ?? null;

    if (!is_string($correo)) {
        responderJson([
            'success' => false,
            'message' => 'El correo electrónico es obligatorio.'
        ], 400);
    }

    $correo = mb_strtolower(trim($correo));

    if ($correo === '' || mb_strlen($correo) > 150 || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        responderJson([
            'success' => false,
            'message' => 'El correo electrónico no es válido.'
        ], 400);
    }

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../config/mailer.php';

    // El administrador general queda fuera del restablecimiento por correo.
    $consulta = $conexion->prepare(
        'SELECT id, nombre, correo
         FROM usuarios
         WHERE correo = :correo
           AND activo = 1
           AND rol_id IN (2, 3)
         LIMIT 1'
    );
    $consulta->execute(['correo' => $correo]);
    $usuarioEncontrado = $consulta->fetch();

    // Se responde lo mismo exista o no el correo para no revelar qué cuentas están registradas.
    if ($usuarioEncontrado === false) {
        responderJson(['success' => true, 'message' => MENSAJE_RESET_ENVIADO]);
    }

    $usuarioId = (int) $usuarioEncontrado['id'];

    $consultaReciente = $conexion->prepare(
        'SELECT id FROM password_resets
         WHERE usuario_id = :usuario_id
           AND creado_en > DATE_SUB(NOW(), INTERVAL ' . MINUTOS_ESPERA_RESET . ' MINUTE)
         LIMIT 1'
    );
    $consultaReciente->execute(['usuario_id' => $usuarioId]);
    if ($consultaReciente->fetchColumn() !== false) {
        responderJson(['success' => true, 'message' => MENSAJE_RESET_ENVIADO]);
    }

    $token = bin2hex(random_bytes(32));

    $conexion->beginTransaction();
    $conexion->prepare(
        'UPDATE password_resets
         SET usado_en = NOW()
         WHERE usuario_id = :usuario_id
           AND usado_en IS NULL'
    )->execute(['usuario_id' => $usuarioId]);
    $conexion->prepare(
        'INSERT INTO password_resets (usuario_id, token_hash, expira_en)
         VALUES (:usuario_id, :token_hash, DATE_ADD(NOW(), INTERVAL ' . MINUTOS_EXPIRACION_RESET . ' MINUTE))'
    )->execute([
        'usuario_id' => $usuarioId,
        'token_hash' => hash('sha256', $token)
    ]);
    $conexion->commit();

    $enlace = obtenerUrlAplicacion() . '/src/inicio_sesion/restablecer.html?token=' . $token;
    $nombre = (string) $usuarioEncontrado['nombre'];
    $nombreHtml = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
    $enlaceHtml = htmlspecialchars($enlace, ENT_QUOTES, 'UTF-8');

    try {
        enviarCorreo(
            $usuarioEncontrado['correo'],
            $nombre,
            'Restablece tu contraseña de PrivateStorage',
            '<p>Hola ' . $nombreHtml . ',</p>'
                . '<p>Recibimos una solicitud para restablecer tu contraseña de PrivateStorage.</p>'
                . '<p><a href="' . $enlaceHtml . '">Restablecer contraseña</a></p>'
                . '<p>El enlace vence en ' . MINUTOS_EXPIRACION_RESET . ' minutos y solo puede usarse una vez. '
                . 'Si no solicitaste este cambio, ignora este correo.</p>',
            "Hola {$nombre},\n\n"
                . "Recibimos una solicitud para restablecer tu contraseña de PrivateStorage.\n"
                . "Abre este enlace para elegir una nueva: {$enlace}\n\n"
                . 'El enlace vence en ' . MINUTOS_EXPIRACION_RESET . " minutos y solo puede usarse una vez.\n"
                . 'Si no solicitaste este cambio, ignora este correo.'
        );
    } catch (Throwable $e) {
        // El token no sirve si el correo nunca llegó; se elimina para permitir otro intento.
        $conexion->prepare('DELETE FROM password_resets WHERE token_hash = :token_hash')
            ->execute(['token_hash' => hash('sha256', $token)]);
        throw $e;
    }

    responderJson(['success' => true, 'message' => MENSAJE_RESET_ENVIADO]);
} catch (Throwable $e) {
    if (isset($conexion) && $conexion instanceof PDO && $conexion->inTransaction()) {
        $conexion->rollBack();
    }

    error_log(sprintf(
        'Error en auth/solicitar_reset.php: %s en %s:%d',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));
    responderJson([
        'success' => false,
        'message' => 'No se pudo procesar la solicitud. Intenta más tarde.'
    ], 500);
}
