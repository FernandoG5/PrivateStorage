<?php
// Cambio obligatorio de la contraseña temporal en el primer inicio de sesión.

ob_start();
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

const LONGITUD_MINIMA_PASSWORD = 8;
const MINUTOS_SESION_CAMBIO_PASSWORD = 30;

function responderJson(array $respuesta, int $codigoHttp = 200): never
{
    ini_set('display_errors', '0');

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    http_response_code($codigoHttp);
    header('Content-Type: application/json; charset=utf-8');

    $json = json_encode($respuesta, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
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

header('Content-Type: application/json; charset=utf-8');

try {
    session_start();

    $rutaProyecto = rtrim(
        dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/auth/guardar_password_nueva.php')),
        '/\\'
    );
    $urlInicioSesion = $rutaProyecto . '/src/inicio_sesion/inicioSesion.html';

    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        responderJson(['success' => false, 'message' => 'Solamente se permite el método POST.'], 405);
    }

    $pendiente = $_SESSION['cambio_password_pendiente'] ?? null;
    if (!is_array($pendiente)
        || !isset($pendiente['user_id'], $pendiente['desde'])
        || time() - (int) $pendiente['desde'] > MINUTOS_SESION_CAMBIO_PASSWORD * 60
    ) {
        $_SESSION = [];
        responderJson([
            'success' => false,
            'message' => 'Tu sesión expiró. Inicia sesión de nuevo con tu contraseña temporal.',
            'redirect' => $urlInicioSesion
        ], 401);
    }

    $csrf = $_POST['csrf'] ?? '';
    if (!is_string($csrf)
        || !is_string($_SESSION['csrf_cambio_password'] ?? null)
        || !hash_equals($_SESSION['csrf_cambio_password'], $csrf)
    ) {
        responderJson(['success' => false, 'message' => 'La solicitud no es válida. Recarga la página.'], 400);
    }

    $password = $_POST['password'] ?? null;
    $confirmacion = $_POST['password_confirmacion'] ?? null;
    $aceptaTerminos = $_POST['acepta_terminos'] ?? '';

    if (!is_string($password) || !is_string($confirmacion)) {
        responderJson(['success' => false, 'message' => 'Los datos enviados no son válidos.'], 400);
    }
    if (mb_strlen($password) < LONGITUD_MINIMA_PASSWORD) {
        responderJson([
            'success' => false,
            'message' => 'La contraseña debe tener al menos ' . LONGITUD_MINIMA_PASSWORD . ' caracteres.'
        ], 400);
    }
    if (!hash_equals($password, $confirmacion)) {
        responderJson(['success' => false, 'message' => 'Las contraseñas no coinciden.'], 400);
    }
    if ($aceptaTerminos !== '1') {
        responderJson([
            'success' => false,
            'message' => 'Debes aceptar los términos de uso y el aviso de privacidad para continuar.'
        ], 400);
    }

    require_once __DIR__ . '/../config/database.php';

    $conexion->beginTransaction();
    $consulta = $conexion->prepare(
        'SELECT id, nombre, usuario, password_hash, rol_id, sucursal_id, activo, debe_cambiar_password,
                (password_expira IS NOT NULL AND password_expira < NOW()) AS temporal_vencida
         FROM usuarios
         WHERE id = :id
         LIMIT 1
         FOR UPDATE'
    );
    $consulta->execute(['id' => (int) $pendiente['user_id']]);
    $usuario = $consulta->fetch();

    if ($usuario === false
        || (int) $usuario['activo'] !== 1
        || (int) $usuario['debe_cambiar_password'] !== 1
        || (int) $usuario['temporal_vencida'] === 1
    ) {
        $conexion->rollBack();
        $_SESSION = [];
        responderJson([
            'success' => false,
            'message' => 'Tu contraseña temporal ya no es válida. Inicia sesión de nuevo o pide que te reenvíen el acceso.',
            'redirect' => $urlInicioSesion
        ], 403);
    }

    if (password_verify($password, (string) $usuario['password_hash'])) {
        $conexion->rollBack();
        responderJson([
            'success' => false,
            'message' => 'La nueva contraseña debe ser distinta a la contraseña temporal.'
        ], 400);
    }

    $conexion->prepare(
        'UPDATE usuarios
         SET password_hash = :password_hash,
             debe_cambiar_password = 0,
             password_expira = NULL,
             aviso_aceptado_en = NOW()
         WHERE id = :id'
    )->execute([
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'id' => (int) $usuario['id']
    ]);
    $conexion->commit();

    $redirecciones = [
        2 => $rutaProyecto . '/src/gerente/gerente.html',
        3 => $rutaProyecto . '/src/cajero/cajero.html'
    ];
    $rolId = (int) $usuario['rol_id'];

    if (!isset($redirecciones[$rolId])) {
        $_SESSION = [];
        responderJson([
            'success' => true,
            'message' => 'Tu contraseña se actualizó. Inicia sesión de nuevo.',
            'redirect' => $urlInicioSesion
        ]);
    }

    // Misma sesión que crea auth/login.php.
    session_regenerate_id(true);
    $_SESSION = [
        'user_id' => (int) $usuario['id'],
        'nombre' => $usuario['nombre'],
        'usuario' => $usuario['usuario'],
        'rol_id' => $rolId,
        'sucursal_id' => $usuario['sucursal_id'] === null ? null : (int) $usuario['sucursal_id'],
        'autenticado' => true
    ];

    responderJson([
        'success' => true,
        'message' => '¡Listo! Tu contraseña se guardó correctamente.',
        'redirect' => $redirecciones[$rolId]
    ]);
} catch (Throwable $e) {
    if (isset($conexion) && $conexion instanceof PDO && $conexion->inTransaction()) {
        $conexion->rollBack();
    }

    error_log(sprintf(
        'Error en auth/guardar_password_nueva.php: %s en %s:%d',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));
    responderJson(['success' => false, 'message' => 'Ocurrió un error interno del servidor.'], 500);
}
