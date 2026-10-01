<?php

session_start();
header('Content-Type: application/json; charset=utf-8');

function responderJson(array $respuesta, int $codigoHttp = 200): never
{
    http_response_code($codigoHttp);
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}

function validarSesionCajero(): array
{
    if (($_SESSION['autenticado'] ?? false) !== true) {
        responderJson([
            'success' => false,
            'message' => 'La sesión ha expirado.',
            'redirect' => '/PrivateStorage-main/src/inicio_sesion/inicioSesion.html'
        ], 401);
    }

    $cajeroId = $_SESSION['user_id'] ?? null;
    $sucursalId = $_SESSION['sucursal_id'] ?? null;

    if ((int) ($_SESSION['rol_id'] ?? 0) !== 3) {
        responderJson([
            'success' => false,
            'message' => 'No tienes permiso para acceder a este módulo.'
        ], 403);
    }

    if ((!is_int($cajeroId) && !ctype_digit((string) $cajeroId)) || (int) $cajeroId <= 0) {
        responderJson([
            'success' => false,
            'message' => 'La sesión del cajero no es válida.'
        ], 403);
    }

    if ((!is_int($sucursalId) && !ctype_digit((string) $sucursalId)) || (int) $sucursalId <= 0) {
        responderJson([
            'success' => false,
            'message' => 'La sucursal del cajero no es válida.'
        ], 403);
    }

    return [
        'cajero_id' => (int) $cajeroId,
        'sucursal_id' => (int) $sucursalId
    ];
}

function centavosDesdeDecimal(string $valor): int
{
    if (!preg_match('/^\d{1,8}(\.\d{1,2})?$/', $valor)) {
        throw new RuntimeException('Precio inválido.');
    }

    [$enteros, $decimales] = array_pad(explode('.', $valor, 2), 2, '0');
    $decimales = str_pad(substr($decimales, 0, 2), 2, '0');

    return ((int) $enteros * 100) + (int) $decimales;
}

function decimalDesdeCentavos(int $centavos): string
{
    return number_format($centavos / 100, 2, '.', '');
}

function generarFolio(): string
{
    return 'V-' . date('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(3)));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJson([
        'success' => false,
        'message' => 'Solamente se permite el método POST.'
    ], 405);
}

$sesion = validarSesionCajero();
$cajeroId = $sesion['cajero_id'];
$sucursalId = $sesion['sucursal_id'];

$entrada = json_decode(file_get_contents('php://input'), true);
if (!is_array($entrada)) {
    responderJson([
        'success' => false,
        'message' => 'El payload de la venta no es válido.'
    ], 400);
}

$metodoPago = $entrada['metodo_pago'] ?? null;
$productos = $entrada['productos'] ?? null;
$metodosPermitidos = ['Efectivo', 'Tarjeta', 'Transferencia'];

if (!is_string($metodoPago) || !in_array($metodoPago, $metodosPermitidos, true)) {
    responderJson([
        'success' => false,
        'message' => 'El método de pago no es válido.'
    ], 400);
}

if (!is_array($productos) || count($productos) === 0) {
    responderJson([
        'success' => false,
        'message' => 'Agrega al menos un producto a la venta.'
    ], 400);
}

$productosSolicitados = [];
foreach ($productos as $item) {
    if (!is_array($item)) {
        responderJson([
            'success' => false,
            'message' => 'Los productos de la venta no son válidos.'
        ], 400);
    }

    $productoId = $item['id_producto'] ?? null;
    $cantidad = $item['cantidad'] ?? null;

    if (!is_int($productoId) && !ctype_digit((string) $productoId)) {
        responderJson([
            'success' => false,
            'message' => 'Uno o más productos no son válidos.'
        ], 400);
    }

    if (!is_int($cantidad) && !ctype_digit((string) $cantidad)) {
        responderJson([
            'success' => false,
            'message' => 'Una o más cantidades no son válidas.'
        ], 400);
    }

    $productoId = (int) $productoId;
    $cantidad = (int) $cantidad;

    if ($productoId <= 0 || $cantidad <= 0 || $cantidad > 1000000) {
        responderJson([
            'success' => false,
            'message' => 'Una o más cantidades no son válidas.'
        ], 400);
    }

    if (isset($productosSolicitados[$productoId])) {
        responderJson([
            'success' => false,
            'message' => 'El carrito contiene productos duplicados.'
        ], 400);
    }

    $productosSolicitados[$productoId] = $cantidad;
}

try {
    require_once __DIR__ . '/../../../config/database.php';

    $consultaCajero = $conexion->prepare(
        'SELECT id
         FROM usuarios
         WHERE id = :id
           AND rol_id = 3
           AND activo = 1
           AND sucursal_id = :sucursal_id
         LIMIT 1'
    );
    $consultaCajero->execute([
        'id' => $cajeroId,
        'sucursal_id' => $sucursalId
    ]);

    if ($consultaCajero->fetchColumn() === false) {
        responderJson([
            'success' => false,
            'message' => 'El cajero no está autorizado para confirmar ventas.'
        ], 403);
    }

    $conexion->beginTransaction();

    $consultaProducto = $conexion->prepare(
        'SELECT p.id, p.codigo, p.nombre, p.precio, i.existencias
         FROM inventario_sucursal i
         INNER JOIN productos p ON p.id = i.producto_id
         WHERE i.producto_id = :producto_id
           AND i.sucursal_id = :sucursal_id
           AND p.activo = 1
         LIMIT 1
         FOR UPDATE'
    );

    $productosVenta = [];
    $totalCentavos = 0;

    foreach ($productosSolicitados as $productoId => $cantidad) {
        $consultaProducto->execute([
            'producto_id' => $productoId,
            'sucursal_id' => $sucursalId
        ]);
        $producto = $consultaProducto->fetch();

        if ($producto === false) {
            $conexion->rollBack();
            responderJson([
                'success' => false,
                'message' => 'Uno o más productos no pertenecen a tu sucursal o no están activos.'
            ], 404);
        }

        if ((int) $producto['existencias'] < $cantidad) {
            $conexion->rollBack();
            responderJson([
                'success' => false,
                'message' => 'No hay existencias suficientes para ' . $producto['nombre'] . '.'
            ], 409);
        }

        $precioCentavos = centavosDesdeDecimal((string) $producto['precio']);
        $importeCentavos = $precioCentavos * $cantidad;
        $totalCentavos += $importeCentavos;

        if ($totalCentavos > 9999999999) {
            $conexion->rollBack();
            responderJson([
                'success' => false,
                'message' => 'El total de la venta excede el límite permitido.'
            ], 409);
        }

        $productosVenta[] = [
            'id' => (int) $producto['id'],
            'codigo' => $producto['codigo'],
            'nombre' => $producto['nombre'],
            'cantidad' => $cantidad,
            'precio_unitario_centavos' => $precioCentavos,
            'importe_centavos' => $importeCentavos
        ];
    }

    $folio = generarFolio();
    $insertarVenta = $conexion->prepare(
        'INSERT INTO ventas (folio, sucursal_id, cajero_id, metodo_pago, total)
         VALUES (:folio, :sucursal_id, :cajero_id, :metodo_pago, :total)'
    );
    $insertarVenta->execute([
        'folio' => $folio,
        'sucursal_id' => $sucursalId,
        'cajero_id' => $cajeroId,
        'metodo_pago' => $metodoPago,
        'total' => decimalDesdeCentavos($totalCentavos)
    ]);
    $ventaId = (int) $conexion->lastInsertId();

    $consultaFecha = $conexion->prepare(
        'SELECT fecha_venta
         FROM ventas
         WHERE id = :id
         LIMIT 1'
    );
    $consultaFecha->execute(['id' => $ventaId]);
    $fechaVenta = (string) $consultaFecha->fetchColumn();

    $insertarDetalle = $conexion->prepare(
        'INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, importe)
         VALUES (:venta_id, :producto_id, :cantidad, :precio_unitario, :importe)'
    );
    $actualizarInventario = $conexion->prepare(
        'UPDATE inventario_sucursal
         SET existencias = existencias - :cantidad
         WHERE producto_id = :producto_id
           AND sucursal_id = :sucursal_id
           AND existencias >= :cantidad_validacion'
    );

    foreach ($productosVenta as $producto) {
        $insertarDetalle->execute([
            'venta_id' => $ventaId,
            'producto_id' => $producto['id'],
            'cantidad' => $producto['cantidad'],
            'precio_unitario' => decimalDesdeCentavos($producto['precio_unitario_centavos']),
            'importe' => decimalDesdeCentavos($producto['importe_centavos'])
        ]);

        $actualizarInventario->execute([
            'cantidad' => $producto['cantidad'],
            'producto_id' => $producto['id'],
            'sucursal_id' => $sucursalId,
            'cantidad_validacion' => $producto['cantidad']
        ]);

        if ($actualizarInventario->rowCount() !== 1) {
            $conexion->rollBack();
            responderJson([
                'success' => false,
                'message' => 'No hay existencias suficientes para uno o más productos.'
            ], 409);
        }
    }

    $conexion->commit();

    responderJson([
        'success' => true,
        'message' => 'Venta registrada correctamente.',
        'venta' => [
            'id' => $ventaId,
            'folio' => $folio,
            'fecha' => $fechaVenta,
            'metodo_pago' => $metodoPago,
            'total' => (float) decimalDesdeCentavos($totalCentavos),
            'productos' => array_map(static fn(array $producto): array => [
                'codigo' => $producto['codigo'],
                'nombre' => $producto['nombre'],
                'cantidad' => $producto['cantidad'],
                'precio_unitario' => (float) decimalDesdeCentavos($producto['precio_unitario_centavos']),
                'importe' => (float) decimalDesdeCentavos($producto['importe_centavos'])
            ], $productosVenta)
        ]
    ]);
} catch (PDOException $e) {
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }

    if (($e->errorInfo[1] ?? null) === 1062) {
        responderJson([
            'success' => false,
            'message' => 'No se pudo generar un folio único para la venta.'
        ], 409);
    }

    responderJson([
        'success' => false,
        'message' => 'No se pudo registrar la venta.'
    ], 500);
} catch (Throwable $e) {
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }

    responderJson([
        'success' => false,
        'message' => 'No se pudo registrar la venta.'
    ], 500);
}
