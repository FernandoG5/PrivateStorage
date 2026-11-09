<?php

require_once __DIR__ . '/comun.php';
prepararApiVentas();

try {
    validarAdministradorVentas();

    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
        responderJsonVentas([
            'success' => false,
            'message' => 'Solamente se permite el método GET.'
        ], 405);
    }

    $idTexto = valorFiltroVentas('id');
    if (!ctype_digit($idTexto) || (int) $idTexto < 1) {
        responderJsonVentas([
            'success' => false,
            'message' => 'El identificador de la venta no es válido.'
        ], 400);
    }

    require_once __DIR__ . '/../../../config/database.php';
    $parametros = ['id' => (int) $idTexto];
    $consultaVenta = ejecutarConsultaVentas(
        $conexion,
        'SELECT v.id, v.folio, v.fecha_venta, v.total, v.metodo_pago,
                s.nombre AS sucursal, u.nombre AS cajero
         FROM ventas v
         INNER JOIN sucursales s ON s.id = v.sucursal_id
         INNER JOIN usuarios u ON u.id = v.cajero_id
         WHERE v.id = :id
         LIMIT 1',
        $parametros
    );
    $venta = $consultaVenta->fetch();

    if ($venta === false) {
        responderJsonVentas([
            'success' => false,
            'message' => 'No se encontró la venta solicitada.'
        ], 404);
    }

    $consultaDetalle = ejecutarConsultaVentas(
        $conexion,
        'SELECT p.nombre AS producto, p.codigo,
                d.cantidad, d.precio_unitario, d.importe AS subtotal
         FROM detalle_venta d
         INNER JOIN productos p ON p.id = d.producto_id
         WHERE d.venta_id = :venta_id
         ORDER BY p.nombre ASC, d.id ASC',
        ['venta_id' => (int) $venta['id']]
    );
    $productos = array_map(
        static fn (array $fila): array => [
            'producto' => $fila['producto'],
            'codigo' => $fila['codigo'],
            'cantidad' => (int) $fila['cantidad'],
            'precio_unitario' => (float) $fila['precio_unitario'],
            'subtotal' => (float) $fila['subtotal']
        ],
        $consultaDetalle->fetchAll()
    );

    responderJsonVentas([
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
        'Error en api/admin/ventas/detalle.php: %s en %s:%d',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));
    responderJsonVentas([
        'success' => false,
        'message' => 'No se pudo cargar el detalle de la venta.'
    ], 500);
}
