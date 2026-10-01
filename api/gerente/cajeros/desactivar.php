<?php

session_start();
header('Content-Type: application/json; charset=utf-8');

function responderJson(array $respuesta, int $codigoHttp = 200): never
{
    http_response_code($codigoHttp);
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}

function obtenerSucursalSesion(): int
{
    if (($_SESSION['autenticado'] ?? false) !== true) {
        responderJson([
            'success' => false,
            'message' => 'La sesión ha expirado.',
            'redirect' => '/PrivateStorage-main/src/inicio_sesion/inicioSesion.html'
        ], 401);
    }
    if ((int) ($_SESSION['rol_id'] ?? 0) !== 2) {
        responderJson(['success' => false, 'message' => 'No tienes permiso para acceder a este módulo.'], 403);
    }
    $sucursalId = $_SESSION['sucursal_id'] ?? null;
    if ((!is_int($sucursalId) && !ctype_digit((string) $sucursalId)) || (int) $sucursalId <= 0) {
        responderJson(['success' => false, 'message' => 'La sucursal del gerente no es válida.'], 403);
    }
    return (int) $sucursalId;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJson(['success' => false, 'message' => 'Solamente se permite el método POST.'], 405);
}

$sucursalId = obtenerSucursalSesion();
$id = $_POST['id'] ?? null;
if (!is_string($id) || !ctype_digit($id) || (int) $id <= 0) {
    responderJson(['success' => false, 'message' => 'El identificador del cajero no es válido.'], 400);
}

try {
    require_once __DIR__ . '/../../../config/database.php';
    $consulta = $conexion->prepare(
        'UPDATE usuarios
         SET activo = 0
         WHERE id = :id AND rol_id = 3 AND sucursal_id = :sucursal_id'
    );
    $consulta->execute(['id' => (int) $id, 'sucursal_id' => $sucursalId]);

    if ($consulta->rowCount() === 0) {
        responderJson(['success' => false, 'message' => 'No se encontró el cajero.'], 404);
    }

    responderJson(['success' => true, 'message' => 'Cajero desactivado correctamente.']);
} catch (Throwable $e) {
    responderJson(['success' => false, 'message' => 'No se pudo desactivar el cajero.'], 500);
}
