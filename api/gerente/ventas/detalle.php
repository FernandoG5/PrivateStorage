<?php

require_once __DIR__ . '/../../../config/app.php';

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

if ((int) ($_SESSION['rol_id'] ?? 0) !== 2) {
    responderJson(['success' => false, 'message' => 'No tienes permiso para acceder a este módulo.'], 403);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    responderJson(['success' => false, 'message' => 'Solamente se permite el método GET.'], 405);
}

$sucursalId = $_SESSION['sucursal_id'] ?? null;
if ((!is_int($sucursalId) && !ctype_digit((string) $sucursalId)) || (int) $sucursalId <= 0) {
    responderJson(['success' => false, 'message' => 'La sucursal del gerente no es válida.'], 403);
}

$id = $_GET['id'] ?? null;
if (!is_string($id) || !ctype_digit($id) || (int) $id <= 0) {
    responderJson(['success' => false, 'message' => 'El identificador de la venta no es válido.'], 400);
}

try {
    require_once __DIR__ . '/../../../config/database.php';
    $consultaVenta = $conexion->prepare(
        'SELECT v.id, v.folio, v.fecha_venta, v.metodo_pago, v.total,
                s.nombre AS sucursal, u.nombre AS cajero
         FROM ventas v
         INNER JOIN sucursales s ON s.id = v.sucursal_id
         INNER JOIN usuarios u ON u.id = v.cajero_id
         WHERE v.id = :id AND v.sucursal_id = :sucursal_id
         LIMIT 1'
    );
    $consultaVenta->execute([
        'id' => (int) $id,
        'sucursal_id' => (int) $sucursalId
    ]);
    $venta = $consultaVenta->fetch();
    if ($venta === false) {
        responderJson(['success' => false, 'message' => 'No se encontró la venta solicitada.'], 404);
    }

    $consultaDetalle = $conexion->prepare(
        'SELECT p.nombre AS producto, p.codigo, d.cantidad,
                d.precio_unitario, d.importe AS subtotal
         FROM detalle_venta d
         INNER JOIN productos p ON p.id = d.producto_id
         WHERE d.venta_id = :venta_id
         ORDER BY d.id ASC'
    );
    $consultaDetalle->execute(['venta_id' => (int) $venta['id']]);
    $productos = array_map(
        static fn (array $producto): array => [
            'producto' => $producto['producto'],
            'codigo' => $producto['codigo'],
            'cantidad' => (int) $producto['cantidad'],
            'precio_unitario' => (float) $producto['precio_unitario'],
            'subtotal' => (float) $producto['subtotal']
        ],
        $consultaDetalle->fetchAll()
    );

    responderJson([
        'success' => true,
        'venta' => [
            'id' => (int) $venta['id'],
            'folio' => $venta['folio'],
            'fecha_venta' => $venta['fecha_venta'],
            'sucursal' => $venta['sucursal'],
            'cajero' => $venta['cajero'],
            'metodo_pago' => $venta['metodo_pago'],
            'total' => (float) $venta['total'],
            'productos' => $productos
        ]
    ]);
} catch (Throwable $e) {
    error_log(sprintf(
        'Error en api/gerente/ventas/detalle.php: %s en %s:%d',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));
    responderJson(['success' => false, 'message' => 'No se pudo cargar el detalle de la venta.'], 500);
}
