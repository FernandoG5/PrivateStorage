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
    $paginaTexto = valorFiltroVentas('pagina');
    $porPaginaTexto = valorFiltroVentas('por_pagina');
    $pagina = $paginaTexto === '' ? 1 : (ctype_digit($paginaTexto) ? (int) $paginaTexto : 0);
    $porPagina = $porPaginaTexto === '' ? 10 : (ctype_digit($porPaginaTexto) ? (int) $porPaginaTexto : 0);

    if ($pagina < 1 || !in_array($porPagina, [5, 10, 20, 30], true)) {
        responderJsonVentas([
            'success' => false,
            'message' => 'La paginación solicitada no es válida.'
        ], 400);
    }

    [$where, $parametros] = condicionesVentas($filtros);
    require_once __DIR__ . '/../../../config/database.php';

    $consultaConteo = ejecutarConsultaVentas(
        $conexion,
        'SELECT COUNT(*)
         FROM ventas v
         INNER JOIN usuarios u ON u.id = v.cajero_id'
            . $where,
        $parametros
    );
    $total = (int) $consultaConteo->fetchColumn();
    $totalPaginas = (int) ceil($total / $porPagina);
    $pagina = $totalPaginas === 0 ? 1 : min($pagina, $totalPaginas);
    $offset = ($pagina - 1) * $porPagina;

    $parametrosListado = $parametros;
    $parametrosListado['limite'] = $porPagina;
    $parametrosListado['offset'] = $offset;
    $consultaVentas = ejecutarConsultaVentas(
        $conexion,
        'SELECT v.id, v.folio, v.fecha_venta,
                s.nombre AS sucursal, u.nombre AS cajero,
                v.metodo_pago, v.total,
                COALESCE((
                    SELECT SUM(d.cantidad)
                    FROM detalle_venta d
                    WHERE d.venta_id = v.id
                ), 0) AS articulos
         FROM ventas v
         INNER JOIN sucursales s ON s.id = v.sucursal_id
         INNER JOIN usuarios u ON u.id = v.cajero_id'
            . $where
            . ' ORDER BY v.fecha_venta DESC, v.id DESC
               LIMIT :limite OFFSET :offset',
        $parametrosListado
    );
    $ventas = array_map(
        static fn (array $fila): array => [
            'id' => (int) $fila['id'],
            'folio' => $fila['folio'],
            'fecha_venta' => $fila['fecha_venta'],
            'sucursal' => $fila['sucursal'],
            'cajero' => $fila['cajero'],
            'articulos' => (int) $fila['articulos'],
            'metodo_pago' => $fila['metodo_pago'],
            'total' => (float) $fila['total']
        ],
        $consultaVentas->fetchAll()
    );

    $consultaSucursales = ejecutarConsultaVentas(
        $conexion,
        'SELECT id, nombre, estado
         FROM sucursales
         ORDER BY nombre ASC',
        []
    );

    responderJsonVentas([
        'success' => true,
        'ventas' => $ventas,
        'sucursales' => array_map(
            static fn (array $sucursal): array => [
                'id' => (int) $sucursal['id'],
                'nombre' => $sucursal['nombre'],
                'estado' => $sucursal['estado']
            ],
            $consultaSucursales->fetchAll()
        ),
        'paginacion' => [
            'pagina' => $pagina,
            'por_pagina' => $porPagina,
            'total' => $total,
            'total_paginas' => $totalPaginas
        ]
    ]);
} catch (Throwable $e) {
    error_log(sprintf(
        'Error en api/admin/ventas/listar.php: %s en %s:%d',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));
    responderJsonVentas([
        'success' => false,
        'message' => 'No se pudieron cargar las ventas.'
    ], 500);
}
