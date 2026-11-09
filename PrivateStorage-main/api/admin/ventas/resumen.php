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

    $filtros = obtenerFiltrosVentas();
    [$where, $parametros] = condicionesVentas($filtros);
    require_once __DIR__ . '/../../../config/database.php';

    $consultaResumen = ejecutarConsultaVentas(
        $conexion,
        'SELECT COUNT(*) AS numero_ventas,
                COALESCE(SUM(v.total), 0) AS total_vendido,
                COALESCE(AVG(v.total), 0) AS ticket_promedio
         FROM ventas v
         INNER JOIN usuarios u ON u.id = v.cajero_id'
            . $where,
        $parametros
    );
    $resumen = $consultaResumen->fetch();

    $consultaProducto = ejecutarConsultaVentas(
        $conexion,
        'SELECT p.nombre AS producto, SUM(d.cantidad) AS cantidad_vendida
         FROM detalle_venta d
         INNER JOIN ventas v ON v.id = d.venta_id
         INNER JOIN usuarios u ON u.id = v.cajero_id
         INNER JOIN productos p ON p.id = d.producto_id'
            . $where
            . ' GROUP BY p.id, p.nombre
               ORDER BY cantidad_vendida DESC, p.nombre ASC
               LIMIT 1',
        $parametros
    );
    $producto = $consultaProducto->fetch();

    responderJsonVentas([
        'success' => true,
        'resumen' => [
            'total_vendido' => (float) $resumen['total_vendido'],
            'numero_ventas' => (int) $resumen['numero_ventas'],
            'ticket_promedio' => (float) $resumen['ticket_promedio'],
            'producto_mas_vendido' => $producto === false
                ? null
                : [
                    'nombre' => $producto['producto'],
                    'cantidad' => (int) $producto['cantidad_vendida']
                ]
        ]
    ]);
} catch (Throwable $e) {
    error_log(sprintf(
        'Error en api/admin/ventas/resumen.php: %s en %s:%d',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));
    responderJsonVentas([
        'success' => false,
        'message' => 'No se pudo cargar el resumen de ventas.'
    ], 500);
}
