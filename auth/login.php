<?php

ob_start();
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

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
            'Error fatal en auth/login.php: %s en %s:%d',
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
    session_start();

    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        responderJson([
            'success' => false,
            'message' => 'Solamente se permite el método POST.'
        ], 405);
    }

    $usuario = $_POST['usuario'] ?? null;
    $password = $_POST['password'] ?? null;

    if (
        !is_string($usuario)
        || !is_string($password)
        || trim($usuario) === ''
        || $password === ''
    ) {
        responderJson([
            'success' => false,
            'message' => 'El usuario y la contraseña son obligatorios.'
        ], 400);
    }

    require_once __DIR__ . '/../config/database.php';

    $consulta = $conexion->prepare(
        'SELECT id, nombre, usuario, password_hash, rol_id, sucursal_id, activo
         FROM usuarios
         WHERE usuario = :usuario
         LIMIT 1'
    );
    $consulta->execute(['usuario' => $usuario]);
    $usuarioEncontrado = $consulta->fetch();

    if ($usuarioEncontrado === false) {
        responderJson([
            'success' => false,
            'message' => 'Usuario o contraseña incorrectos.'
        ], 401);
    }

    if ((int) $usuarioEncontrado['activo'] !== 1) {
        responderJson([
            'success' => false,
            'message' => 'La cuenta está desactivada.'
        ], 403);
    }

    $hash = $usuarioEncontrado['password_hash'];
    $informacionHash = is_string($hash) ? password_get_info($hash) : ['algoName' => 'unknown'];
    $passwordCorrecta = $informacionHash['algoName'] !== 'unknown'
        && password_verify($password, $hash);

    if (!$passwordCorrecta) {
        responderJson([
            'success' => false,
            'message' => 'Usuario o contraseña incorrectos.'
        ], 401);
    }

    $rutaProyecto = rtrim(
        dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/auth/login.php')),
        '/\\'
    );
    $redirecciones = [
        1 => $rutaProyecto . '/src/admin/admin.html',
        2 => $rutaProyecto . '/src/gerente/gerente.html',
        3 => $rutaProyecto . '/src/cajero/cajero.html'
    ];
    $rolId = (int) $usuarioEncontrado['rol_id'];

    if (!isset($redirecciones[$rolId])) {
        responderJson([
            'success' => false,
            'message' => 'No se pudo completar la autenticación.'
        ], 500);
    }

    session_regenerate_id(true);
    $_SESSION = [
        'user_id' => (int) $usuarioEncontrado['id'],
        'nombre' => $usuarioEncontrado['nombre'],
        'usuario' => $usuarioEncontrado['usuario'],
        'rol_id' => $rolId,
        'sucursal_id' => $usuarioEncontrado['sucursal_id'] === null
            ? null
            : (int) $usuarioEncontrado['sucursal_id'],
        'autenticado' => true
    ];

    responderJson([
        'success' => true,
        'redirect' => $redirecciones[$rolId]
    ]);
} catch (Throwable $e) {
    error_log(sprintf(
        'Error en auth/login.php: %s en %s:%d',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));
    responderJson([
        'success' => false,
        'message' => 'Ocurrió un error interno del servidor.'
    ], 500);
}
