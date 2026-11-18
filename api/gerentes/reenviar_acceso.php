<?php
// Genera una nueva contraseña temporal para un gerente que aún no activa su cuenta
// y le reenvía el correo de bienvenida. Solo para el administrador.

require_once __DIR__ . '/../../config/app.php';

session_start();
header('Content-Type: application/json; charset=utf-8');

function responderJson(array $respuesta, int $codigoHttp = 200): never
{
    http_response_code($codigoHttp);
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}

if (($_SESSION['autenticado'] ?? false) !== true) {
    responderJson([
        'success' => false,
        'message' => 'La sesión ha expirado.',
        'redirect' => privateStorageUrl('/src/inicio_sesion/inicioSesion.html')
    ], 401);
}

if ((int) ($_SESSION['rol_id'] ?? 0) !== 1) {
    responderJson(['success' => false, 'message' => 'No tienes permiso para acceder a este módulo.'], 403);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    responderJson(['success' => false, 'message' => 'Solamente se permite el método POST.'], 405);
}

$idRecibido = $_POST['id'] ?? '';
if (!is_string($idRecibido) || !ctype_digit($idRecibido) || (int) $idRecibido <= 0) {
    responderJson(['success' => false, 'message' => 'El identificador del gerente no es válido.'], 400);
}
$id = (int) $idRecibido;

try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../auth/acceso_temporal.php';

    $consulta = $conexion->prepare(
        'SELECT id, nombre, usuario, correo, rol_id, activo, debe_cambiar_password
         FROM usuarios
         WHERE id = :id AND rol_id = 2
         LIMIT 1'
    );
    $consulta->execute(['id' => $id]);
    $gerente = $consulta->fetch();

    if ($gerente === false) {
        responderJson(['success' => false, 'message' => 'El gerente no existe.'], 404);
    }
    if ((int) $gerente['activo'] !== 1) {
        responderJson(['success' => false, 'message' => 'El gerente está inactivo. Actívalo antes de reenviar el acceso.'], 409);
    }
    if ((int) $gerente['debe_cambiar_password'] !== 1) {
        responderJson([
            'success' => false,
            'message' => 'Este gerente ya activó su cuenta. Si olvidó su contraseña, puede usar "¿Olvidaste tu contraseña?".'
        ], 409);
    }
    if (!is_string($gerente['correo']) || !filter_var($gerente['correo'], FILTER_VALIDATE_EMAIL)) {
        responderJson(['success' => false, 'message' => 'El gerente no tiene un correo electrónico válido.'], 409);
    }

    $passwordTemporal = asignarPasswordTemporal($conexion, $id);
    $correoEnviado = enviarCorreoBienvenida($gerente, $passwordTemporal);
    unset($passwordTemporal);

    if (!$correoEnviado) {
        responderJson([
            'success' => false,
            'correo_enviado' => false,
            'message' => 'No se pudo enviar el correo. Intenta de nuevo en unos minutos.'
        ], 502);
    }

    responderJson([
        'success' => true,
        'correo_enviado' => true,
        'message' => 'Correo reenviado con una nueva contraseña temporal.'
    ]);
} catch (Throwable $e) {
    error_log('Error en api/gerentes/reenviar_acceso.php: ' . $e->getMessage());
    responderJson(['success' => false, 'message' => 'No se pudo reenviar el acceso.'], 500);
}
