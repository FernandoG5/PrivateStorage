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

    responderJson([
        'success' => true,
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
