<?php

ob_start();
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);
require_once __DIR__ . '/../../config/app.php';

function responderJson(array $respuesta, int $codigoHttp = 200): never
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    http_response_code($codigoHttp);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(
        $respuesta,
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );
    exit;
}

const UMBRAL_STOCK_DASHBOARD = 20;
const UMBRAL_STOCK_MUY_BAJO = 5;
const UMBRAL_STOCK_BAJO = 10;

$periodo = $_GET['periodo'] ?? 'mes';
if (!is_string($periodo) || !in_array($periodo, ['hoy', 'semana', 'mes'], true)) {
    responderJson([
        'success' => false,
        'message' => 'El periodo seleccionado no es válido.'
    ], 400);
}

set_error_handler(
    static function (int $severity, string $message, string $file, int $line): bool {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        throw new ErrorException($message, 0, $severity, $file, $line);
    }
);

register_shutdown_function(
    static function (): void {
        $error = error_get_last();
        $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];

        if ($error === null || !in_array($error['type'], $fatalTypes, true)) {
            return;
        }

        error_log(sprintf(
            'Error fatal en api/admin/dashboard.php: %s en %s:%d',
            $error['message'],
            $error['file'],
            $error['line']
        ));
        responderJson([
            'success' => false,
            'message' => 'No se pudo cargar el dashboard.'
        ], 500);
    }
);

try {
    if (session_status() !== PHP_SESSION_ACTIVE && !session_start()) {
        throw new RuntimeException('No se pudo iniciar la sesión.');
    }

    if (($_SESSION['autenticado'] ?? false) !== true) {
        responderJson([
            'success' => false,
            'message' => 'La sesión ha expirado.',
            'redirect' => privateStorageUrl('/src/inicio_sesion/inicioSesion.html')
        ], 401);
    }

    if ((int) ($_SESSION['rol_id'] ?? 0) !== 1) {
        responderJson([
            'success' => false,
            'message' => 'No tienes permiso para acceder a este módulo.'
        ], 403);
    }

    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
        responderJson([
            'success' => false,
            'message' => 'Solamente se permite el método GET.'
        ], 405);
    }

    require_once __DIR__ . '/../../config/database.php';

    $consultaProductos = $conexion->prepare(
        'SELECT COUNT(*)
         FROM productos
         WHERE activo = :activo'
    );
    $consultaProductos->execute(['activo' => 1]);
    $totalProductos = (int) $consultaProductos->fetchColumn();

    $consultaConteoStock = $conexion->prepare(
        'SELECT COUNT(*)
         FROM inventario_sucursal i
         INNER JOIN productos p ON p.id = i.producto_id
         WHERE p.activo = :activo
           AND i.existencias <= :umbral'
    );
    $consultaConteoStock->execute([
        'activo' => 1,
        'umbral' => UMBRAL_STOCK_DASHBOARD
    ]);
    $cantidadStockBajo = (int) $consultaConteoStock->fetchColumn();

    $consultaStock = $conexion->prepare(
        'SELECT p.id AS producto_id, p.nombre AS producto, s.nombre AS sucursal,
                i.existencias AS restante
         FROM inventario_sucursal i
         INNER JOIN productos p ON p.id = i.producto_id
         INNER JOIN sucursales s ON s.id = i.sucursal_id
         WHERE p.activo = :activo
           AND i.existencias <= :umbral
         ORDER BY i.existencias ASC, p.nombre ASC, s.nombre ASC
         LIMIT 6'
    );
    $consultaStock->execute([
        'activo' => 1,
        'umbral' => UMBRAL_STOCK_DASHBOARD
    ]);
    $stockBajo = array_map(
        static function (array $fila): array {
            $restante = (int) $fila['restante'];
            $gravedad = $restante <= UMBRAL_STOCK_MUY_BAJO
                ? 'rojo'
                : ($restante <= UMBRAL_STOCK_BAJO ? 'naranja' : 'amarillo');

            return [
                'producto_id' => (int) $fila['producto_id'],
                'producto' => $fila['producto'],
                'sucursal' => $fila['sucursal'],
                'restante' => $restante,
                'gravedad' => $gravedad
            ];
        },
        $consultaStock->fetchAll()
    );

    $consultaVentasSucursales = $conexion->prepare(
        'SELECT s.id AS sucursal_id, s.nombre AS sucursal,
                SUM(v.total) AS total_vendido
         FROM ventas v
         INNER JOIN sucursales s ON s.id = v.sucursal_id
         GROUP BY s.id, s.nombre
         ORDER BY total_vendido DESC, s.nombre ASC
         LIMIT 6'
    );
    $consultaVentasSucursales->execute();
    $ventasSucursales = array_map(
        static fn (array $fila): array => [
            'sucursal_id' => (int) $fila['sucursal_id'],
            'sucursal' => $fila['sucursal'],
            'total_vendido' => (float) $fila['total_vendido']
        ],
        $consultaVentasSucursales->fetchAll()
    );

    $consultaUltimasVentas = $conexion->prepare(
        'SELECT v.id, v.folio, s.nombre AS sucursal, v.fecha_venta,
                v.total, v.metodo_pago
         FROM ventas v
         INNER JOIN sucursales s ON s.id = v.sucursal_id
         ORDER BY v.fecha_venta DESC, v.id DESC
         LIMIT 6'
    );
    $consultaUltimasVentas->execute();
    $ultimasVentas = array_map(
        static fn (array $fila): array => [
            'id' => (int) $fila['id'],
            'folio' => $fila['folio'],
            'sucursal' => $fila['sucursal'],
            'fecha_venta' => $fila['fecha_venta'],
            'total' => (float) $fila['total'],
            'metodo_pago' => $fila['metodo_pago']
        ],
        $consultaUltimasVentas->fetchAll()
    );

    $fechaBase = $conexion->query('SELECT CURDATE()')->fetchColumn();
    $zonaUtc = new DateTimeZone('UTC');
    $fechaHoy = DateTimeImmutable::createFromFormat('!Y-m-d', (string) $fechaBase, $zonaUtc);
    if ($fechaHoy === false) {
        throw new RuntimeException('MySQL devolvió una fecha no válida para el rendimiento de ventas.');
    }

    if ($periodo === 'hoy') {
        $inicio = $fechaHoy;
        $fin = $inicio->modify('+1 day');
        $cantidadIntervalos = 24;
    } elseif ($periodo === 'semana') {
        $inicio = $fechaHoy->modify('monday this week');
        $fin = $inicio->modify('+1 week');
        $cantidadIntervalos = 7;
    } else {
        $inicio = $fechaHoy->modify('first day of this month');
        $fin = $fechaHoy->modify('+1 day');
        $cantidadIntervalos = (int) $inicio->diff($fin)->format('%a');
    }

    $consultaVentasPeriodo = $conexion->prepare(
        'SELECT ' . ($periodo === 'hoy'
            ? "DATE_FORMAT(fecha_venta, '%H')"
            : "DATE_FORMAT(fecha_venta, '%Y-%m-%d')") . ' AS intervalo,
                COUNT(*) AS cantidad, COALESCE(SUM(total), 0) AS total
         FROM ventas
         WHERE fecha_venta >= :fecha_inicio
           AND fecha_venta < :fecha_fin
         GROUP BY intervalo
         ORDER BY intervalo ASC'
    );
    $consultaVentasPeriodo->execute([
        'fecha_inicio' => $inicio->format('Y-m-d'),
        'fecha_fin' => $fin->format('Y-m-d')
    ]);

    $resultadosPeriodo = [];
    foreach ($consultaVentasPeriodo->fetchAll() as $fila) {
        $resultadosPeriodo[$fila['intervalo']] = [
            'cantidad' => (int) $fila['cantidad'],
            'total' => (float) $fila['total']
        ];
    }

    $intervalosVentas = [];
    $totalPeriodo = 0.0;
    $cantidadPeriodo = 0;
    for ($indice = 0; $indice < $cantidadIntervalos; $indice++) {
        $fechaIntervalo = $inicio->modify("+{$indice} " . ($periodo === 'hoy' ? 'hours' : 'days'));
        $clave = $periodo === 'hoy' ? $fechaIntervalo->format('H') : $fechaIntervalo->format('Y-m-d');
        $valores = $resultadosPeriodo[$clave] ?? ['cantidad' => 0, 'total' => 0.0];
        $etiqueta = $periodo === 'hoy'
            ? $fechaIntervalo->format('H:00')
            : ($periodo === 'semana' ? ucfirst($fechaIntervalo->format('D')) : $fechaIntervalo->format('j'));

        $intervalosVentas[] = [
            'fecha' => $clave,
            'etiqueta' => $etiqueta,
            'cantidad' => $valores['cantidad'],
            'total' => $valores['total']
        ];
        $cantidadPeriodo += $valores['cantidad'];
        $totalPeriodo += $valores['total'];
    }

    $consultaMetodosPago = $conexion->prepare(
        'SELECT metodo_pago, COUNT(*) AS cantidad, COALESCE(SUM(total), 0) AS total
         FROM ventas
         WHERE fecha_venta >= :fecha_inicio
           AND fecha_venta < :fecha_fin
         GROUP BY metodo_pago
         ORDER BY total DESC, metodo_pago ASC'
    );
    $consultaMetodosPago->execute([
        'fecha_inicio' => $inicio->format('Y-m-d'),
        'fecha_fin' => $fin->format('Y-m-d')
    ]);
    $metodosPago = array_map(
        static fn (array $fila): array => [
            'metodo' => $fila['metodo_pago'],
            'cantidad' => (int) $fila['cantidad'],
            'total' => (float) $fila['total']
        ],
        $consultaMetodosPago->fetchAll()
    );

    responderJson([
        'success' => true,
        'periodo' => $periodo,
        'desde' => $inicio->format('Y-m-d'),
        'hasta' => $fin->modify('-1 day')->format('Y-m-d'),
        'resumen_ventas' => [
            'cantidad' => $cantidadPeriodo,
            'total' => round($totalPeriodo, 2),
            'promedio' => $cantidadPeriodo === 0 ? 0 : round($totalPeriodo / $cantidadPeriodo, 2)
        ],
        'intervalos_ventas' => $intervalosVentas,
        'metodos_pago' => $metodosPago,
        'umbral_stock' => UMBRAL_STOCK_DASHBOARD,
        'total_productos_activos' => $totalProductos,
        'cantidad_stock_bajo' => $cantidadStockBajo,
        'stock_bajo' => $stockBajo,
        'ventas_por_sucursal' => $ventasSucursales,
        'ultimas_ventas' => $ultimasVentas
    ]);
} catch (Throwable $e) {
    error_log(sprintf(
        'Error en api/admin/dashboard.php: %s en %s:%d',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));
    responderJson([
        'success' => false,
        'message' => 'No se pudo cargar el dashboard.'
    ], 500);
}
