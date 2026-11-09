<?php

ob_start();
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

const LONGITUD_MINIMA_PASSWORD = 8;

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
            'Error fatal en auth/restablecer_password.php: %s en %s:%d',
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

    $token = $_POST['token'] ?? null;
    $password = $_POST['password'] ?? null;
    $confirmacion = $_POST['password_confirmacion'] ?? null;

    if (!is_string($token) || !is_string($password) || !is_string($confirmacion)) {
        responderJson([
            'success' => false,
            'message' => 'Los datos enviados no son válidos.'
        ], 400);
    }

    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        responderJson([
            'success' => false,
            'message' => 'El enlace no es válido o ya expiró. Solicita uno nuevo.'
        ], 400);
    }

    if (mb_strlen($password) < LONGITUD_MINIMA_PASSWORD) {
        responderJson([
            'success' => false,
            'message' => 'La contraseña debe tener al menos ' . LONGITUD_MINIMA_PASSWORD . ' caracteres.'
        ], 400);
    }

    if (!hash_equals($password, $confirmacion)) {
        responderJson([
            'success' => false,
            'message' => 'Las contraseñas no coinciden.'
        ], 400);
    }

    require_once __DIR__ . '/../config/database.php';

    $conexion->beginTransaction();

    // FOR UPDATE evita que dos peticiones simultáneas usen el mismo token.
    $consulta = $conexion->prepare(
        'SELECT pr.id, pr.usuario_id
         FROM password_resets pr
         INNER JOIN usuarios u ON u.id = pr.usuario_id
         WHERE pr.token_hash = :token_hash
           AND pr.usado_en IS NULL
           AND pr.expira_en > NOW()
           AND u.activo = 1
           AND u.rol_id IN (2, 3)
         LIMIT 1
         FOR UPDATE'
    );
    $consulta->execute(['token_hash' => hash('sha256', $token)]);
    $reset = $consulta->fetch();

    if ($reset === false) {
        $conexion->rollBack();
        responderJson([
            'success' => false,
            'message' => 'El enlace no es válido o ya expiró. Solicita uno nuevo.'
        ], 400);
    }

    $conexion->prepare(
        'UPDATE usuarios SET password_hash = :password_hash WHERE id = :id'
    )->execute([
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'id' => (int) $reset['usuario_id']
    ]);
    $conexion->prepare(
        'UPDATE password_resets
         SET usado_en = NOW()
         WHERE usuario_id = :usuario_id
           AND usado_en IS NULL'
    )->execute(['usuario_id' => (int) $reset['usuario_id']]);
    $conexion->commit();

    $rutaProyecto = rtrim(
        dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/auth/restablecer_password.php')),
        '/\\'
    );

    responderJson([
        'success' => true,
        'message' => 'Tu contraseña se actualizó correctamente.',
        'redirect' => $rutaProyecto . '/src/inicio_sesion/inicioSesion.html'
    ]);
} catch (Throwable $e) {
    if (isset($conexion) && $conexion instanceof PDO && $conexion->inTransaction()) {
        $conexion->rollBack();
    }

    error_log(sprintf(
        'Error en auth/restablecer_password.php: %s en %s:%d',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));
    responderJson([
        'success' => false,
        'message' => 'Ocurrió un error interno del servidor.'
    ], 500);
}
