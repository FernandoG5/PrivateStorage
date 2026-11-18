<?php

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

if (!in_array((int) ($_SESSION['rol_id'] ?? 0), [1, 2], true)) {
    responderJson(['success' => false, 'message' => 'No tienes permiso para acceder a este módulo.'], 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responderJson(['success' => false, 'message' => 'Solamente se permite el método GET.'], 405);
}

$rolId = (int) $_SESSION['rol_id'];
if ($rolId === 2) {
    $sucursalId = $_SESSION['sucursal_id'] ?? null;
    if ((!is_int($sucursalId) && !ctype_digit((string) $sucursalId)) || (int) $sucursalId <= 0) {
        responderJson(['success' => false, 'message' => 'La sucursal del gerente no es válida.'], 403);
    }
    $sucursalId = (int) $sucursalId;
} else {
    $sucursalId = $_GET['sucursal_id'] ?? null;
    if (!is_string($sucursalId) || !ctype_digit($sucursalId) || (int) $sucursalId <= 0) {
        responderJson(['success' => false, 'message' => 'La sucursal seleccionada no es válida.'], 400);
    }
    $sucursalId = (int) $sucursalId;
}

try {
    require_once __DIR__ . '/../../config/database.php';

    $consultaSucursal = $conexion->prepare('SELECT id FROM sucursales WHERE id = :id LIMIT 1');
    $consultaSucursal->execute(['id' => $sucursalId]);
    if ($consultaSucursal->fetchColumn() === false) {
        responderJson(['success' => false, 'message' => 'La sucursal no existe.'], 404);
    }

    $consultaCajas = $conexion->prepare(
        'SELECT c.id, c.nombre, c.cajero_id, c.estado, c.fecha_creacion, c.fecha_actualizacion,
                u.nombre AS cajero_nombre, u.usuario AS cajero_usuario, u.activo AS cajero_activo
         FROM cajas c
         LEFT JOIN usuarios u ON u.id = c.cajero_id
         WHERE c.sucursal_id = :sucursal_id
         ORDER BY c.nombre ASC'
    );
    $consultaCajas->execute(['sucursal_id' => $sucursalId]);

    $consultaCajeros = $conexion->prepare(
        'SELECT id, nombre, usuario
         FROM usuarios
         WHERE rol_id = 3 AND sucursal_id = :sucursal_id AND activo = 1
         ORDER BY nombre ASC'
    );
    $consultaCajeros->execute(['sucursal_id' => $sucursalId]);

    responderJson([
        'success' => true,
        'cajas' => $consultaCajas->fetchAll(),
        'cajeros' => $consultaCajeros->fetchAll()
    ]);
} catch (Throwable $e) {
    responderJson(['success' => false, 'message' => 'No se pudieron cargar las cajas.'], 500);
}
