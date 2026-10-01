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
    if (($_SESSION['autenticado'] ?? false) !== true) responderJson(['success' => false, 'message' => 'La sesión ha expirado.', 'redirect' => '/PrivateStorage-main/src/inicio_sesion/inicioSesion.html'], 401);
    if ((int) ($_SESSION['rol_id'] ?? 0) !== 2) responderJson(['success' => false, 'message' => 'No tienes permiso para acceder a este módulo.'], 403);
    $sucursalId = $_SESSION['sucursal_id'] ?? null;
    if ((!is_int($sucursalId) && !ctype_digit((string) $sucursalId)) || (int) $sucursalId <= 0) responderJson(['success' => false, 'message' => 'La sucursal del gerente no es válida.'], 403);
    return (int) $sucursalId;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') responderJson(['success' => false, 'message' => 'Solamente se permite el método POST.'], 405);
$sucursalId = obtenerSucursalSesion();
$id = $_POST['id_producto'] ?? null;
$existencias = $_POST['existencias'] ?? null;
if (!is_string($id) || !is_string($existencias) || !ctype_digit($id) || (int) $id <= 0) responderJson(['success' => false, 'message' => 'Los datos del producto no son válidos.'], 400);
if (!ctype_digit($existencias) || (int) $existencias < 0 || (int) $existencias > 1000000) responderJson(['success' => false, 'message' => 'Las existencias no son válidas.'], 400);

try {
    require_once __DIR__ . '/../../../config/database.php';
    $pertenece = $conexion->prepare('SELECT id FROM inventario_sucursal WHERE producto_id = :id AND sucursal_id = :sucursal_id LIMIT 1');
    $pertenece->execute(['id' => (int) $id, 'sucursal_id' => $sucursalId]);
    if ($pertenece->fetchColumn() === false) responderJson(['success' => false, 'message' => 'El producto no pertenece a tu sucursal.'], 404);
    $actualizar = $conexion->prepare('UPDATE inventario_sucursal SET existencias = :existencias WHERE producto_id = :id AND sucursal_id = :sucursal_id');
    $actualizar->execute(['existencias' => (int) $existencias, 'id' => (int) $id, 'sucursal_id' => $sucursalId]);
    responderJson(['success' => true, 'message' => 'Existencias actualizadas correctamente.']);
} catch (Throwable $e) {
    responderJson(['success' => false, 'message' => 'No se pudieron actualizar las existencias.'], 500);
}
