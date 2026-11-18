<?php

require_once __DIR__ . '/../../../config/app.php';

session_start();
header('Content-Type: application/json; charset=utf-8');

function responderJsonRendimiento(array $respuesta, int $codigoHttp = 200): never
{
    http_response_code($codigoHttp);
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}

if (($_SESSION['autenticado'] ?? false) !== true) {
    responderJsonRendimiento([
        'success' => false,
        'message' => 'La sesión ha expirado.',
        'redirect' => privateStorageUrl('/src/inicio_sesion/inicioSesion.html')
    ], 401);
}

if ((int) ($_SESSION['rol_id'] ?? 0) !== 2) {
    responderJsonRendimiento(['success' => false, 'message' => 'No tienes permiso para acceder a este módulo.'], 403);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    responderJsonRendimiento(['success' => false, 'message' => 'Solamente se permite el método GET.'], 405);
}

$sucursalId = $_SESSION['sucursal_id'] ?? null;
if ((!is_int($sucursalId) && !ctype_digit((string) $sucursalId)) || (int) $sucursalId <= 0) {
    responderJsonRendimiento(['success' => false, 'message' => 'La sucursal del gerente no es válida.'], 403);
}

$periodo = $_GET['periodo'] ?? 'hoy';
if (!is_string($periodo) || !in_array($periodo, ['hoy', 'semana', 'mes'], true)) {
    responderJsonRendimiento(['success' => false, 'message' => 'El periodo seleccionado no es válido.'], 400);
}

try {
    require_once __DIR__ . '/../../../config/database.php';
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

    $consulta = $conexion->prepare(
        'SELECT ' . ($periodo === 'hoy'
            ? "DATE_FORMAT(fecha_venta, '%H')"
            : "DATE_FORMAT(fecha_venta, '%Y-%m-%d')") . ' AS intervalo,
                COUNT(*) AS cantidad_ventas, COALESCE(SUM(total), 0) AS total
         FROM ventas
         WHERE sucursal_id = :sucursal_id
           AND fecha_venta >= :fecha_inicio
           AND fecha_venta < :fecha_fin
         GROUP BY intervalo
         ORDER BY intervalo ASC'
    );
    $consulta->execute([
        'sucursal_id' => (int) $sucursalId,
        'fecha_inicio' => $inicio->format('Y-m-d'),
        'fecha_fin' => $fin->format('Y-m-d')
    ]);

    $consultaMetodosPago = $conexion->prepare(
        'SELECT metodo_pago, COUNT(*) AS cantidad, COALESCE(SUM(total), 0) AS total
         FROM ventas
         WHERE sucursal_id = :sucursal_id
           AND fecha_venta >= :fecha_inicio
           AND fecha_venta < :fecha_fin
         GROUP BY metodo_pago
         ORDER BY total DESC, metodo_pago ASC'
    );
    $consultaMetodosPago->execute([
        'sucursal_id' => (int) $sucursalId,
        'fecha_inicio' => $inicio->format('Y-m-d'),
        'fecha_fin' => $fin->format('Y-m-d')
    ]);

    $resultados = [];
    foreach ($consulta->fetchAll() as $fila) {
        $resultados[$fila['intervalo']] = [
            'ventas' => (int) $fila['cantidad_ventas'],
            'total' => (float) $fila['total']
        ];
    }

    $ventas = [];
    $ventasPorMetodo = array_map(
        static fn (array $fila): array => [
            'metodo' => $fila['metodo_pago'],
            'cantidad' => (int) $fila['cantidad'],
            'total' => (float) $fila['total']
        ],
        $consultaMetodosPago->fetchAll()
    );
    $totalVentas = 0;
    $cantidadVentas = 0;
    for ($indice = 0; $indice < $cantidadIntervalos; $indice++) {
        $fechaIntervalo = $inicio->modify("+{$indice} " . ($periodo === 'hoy' ? 'hours' : 'days'));
        $clave = $periodo === 'hoy' ? $fechaIntervalo->format('H') : $fechaIntervalo->format('Y-m-d');
        $valores = $resultados[$clave] ?? ['ventas' => 0, 'total' => 0.0];

        if ($periodo === 'hoy') {
            $etiqueta = $fechaIntervalo->format('H:00');
        } elseif ($periodo === 'semana') {
            $etiqueta = ucfirst($fechaIntervalo->format('D'));
        } else {
            $etiqueta = $fechaIntervalo->format('j');
        }

        $ventas[] = [
            'fecha' => $clave,
            'etiqueta' => $etiqueta,
            'cantidad' => $valores['ventas'],
            'total' => $valores['total']
        ];
        $cantidadVentas += $valores['ventas'];
        $totalVentas += $valores['total'];
    }

    responderJsonRendimiento([
        'success' => true,
        'periodo' => $periodo,
        'desde' => $inicio->format('Y-m-d'),
        'hasta' => $fin->modify('-1 day')->format('Y-m-d'),
        'resumen' => [
            'cantidad' => $cantidadVentas,
            'total' => round($totalVentas, 2),
            'promedio' => $cantidadVentas === 0 ? 0 : round($totalVentas / $cantidadVentas, 2)
        ],
        'intervalos' => $ventas,
        'metodos_pago' => $ventasPorMetodo
    ]);
} catch (Throwable $e) {
    error_log(sprintf(
        'Error en api/gerente/ventas/rendimiento.php: %s en %s:%d',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));
    responderJsonRendimiento(['success' => false, 'message' => 'No se pudo cargar el rendimiento de ventas.'], 500);
}
