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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responderJson(['success' => false, 'message' => 'Solamente se permite el método GET.'], 405);
}

$sucursalId = obtenerSucursalSesion();

try {
    require_once __DIR__ . '/../../../config/database.php';
    $consulta = $conexion->prepare(
        'SELECT p.id, p.codigo, p.nombre, p.precio, p.activo,
                i.existencias, i.stock_minimo, i.fecha_actualizacion
         FROM inventario_sucursal i
         INNER JOIN productos p ON p.id = i.producto_id
         WHERE i.sucursal_id = :sucursal_id
           AND p.activo = 1
         ORDER BY p.nombre ASC'
    );
    $consulta->execute(['sucursal_id' => $sucursalId]);
    responderJson(['success' => true, 'productos' => $consulta->fetchAll()]);
} catch (Throwable $e) {
    responderJson(['success' => false, 'message' => 'No se pudieron cargar los productos.'], 500);
}
