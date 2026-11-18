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

try {
    require_once __DIR__ . '/../../../config/database.php';
    $consulta = $conexion->prepare(
        'SELECT v.id, v.folio, v.fecha_venta, v.metodo_pago, v.total,
                u.nombre AS cajero,
                COALESCE(SUM(d.cantidad), 0) AS articulos
         FROM ventas v
         INNER JOIN usuarios u ON u.id = v.cajero_id
         LEFT JOIN detalle_venta d ON d.venta_id = v.id
         WHERE v.sucursal_id = :sucursal_id
         GROUP BY v.id, v.folio, v.fecha_venta, v.metodo_pago, v.total, u.nombre
         ORDER BY v.fecha_venta DESC, v.id DESC
         LIMIT 20'
    );
    $consulta->execute(['sucursal_id' => (int) $sucursalId]);
    $ventas = array_map(
        static fn (array $venta): array => [
            'id' => (int) $venta['id'],
            'folio' => $venta['folio'],
            'fecha_venta' => $venta['fecha_venta'],
            'cajero' => $venta['cajero'],
            'metodo_pago' => $venta['metodo_pago'],
            'total' => (float) $venta['total'],
            'articulos' => (int) $venta['articulos']
        ],
        $consulta->fetchAll()
    );

    responderJson(['success' => true, 'ventas' => $ventas]);
} catch (Throwable $e) {
    error_log(sprintf(
        'Error en api/gerente/ventas/listar.php: %s en %s:%d',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));
    responderJson(['success' => false, 'message' => 'No se pudieron cargar las ventas.'], 500);
}
