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
        responderJson(['success' => false, 'message' => 'La sesión ha expirado.', 'redirect' => '/PrivateStorage-main/src/inicio_sesion/inicioSesion.html'], 401);
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJson(['success' => false, 'message' => 'Solamente se permite el método POST.'], 405);
}

$sucursalId = obtenerSucursalSesion();
$codigo = $_POST['codigo'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$precio = $_POST['precio'] ?? null;
$existencias = $_POST['existencias'] ?? null;
$stockMinimo = $_POST['stock_minimo'] ?? null;

if (!is_string($codigo) || !is_string($nombre) || !is_string($precio) || !is_string($existencias) || !is_string($stockMinimo)) {
    responderJson(['success' => false, 'message' => 'Los datos del producto no son válidos.'], 400);
}

$codigo = trim($codigo);
$nombre = trim($nombre);

if ($codigo === '' || $nombre === '') {
    responderJson(['success' => false, 'message' => 'Código y nombre son obligatorios.'], 400);
}
if (!is_numeric($precio) || (float) $precio < 0 || (float) $precio > 99999999.99) {
    responderJson(['success' => false, 'message' => 'El precio no es válido.'], 400);
}
if (!ctype_digit($existencias) || (int) $existencias < 0 || (int) $existencias > 1000000) {
    responderJson(['success' => false, 'message' => 'Las existencias no son válidas.'], 400);
}
if (!ctype_digit($stockMinimo) || (int) $stockMinimo < 0 || (int) $stockMinimo > 1000000) {
    responderJson(['success' => false, 'message' => 'El stock mínimo no es válido.'], 400);
}

try {
    require_once __DIR__ . '/../../../config/database.php';
    $conexion->beginTransaction();

    $consultaProducto = $conexion->prepare(
        'SELECT id, activo FROM productos WHERE codigo = :codigo LIMIT 1 FOR UPDATE'
    );
    $consultaProducto->execute(['codigo' => $codigo]);
    $producto = $consultaProducto->fetch();
    $mensaje = 'Producto agregado correctamente.';

    if ($producto === false) {
        $consultaInsertar = $conexion->prepare(
            'INSERT INTO productos (codigo, nombre, precio, activo)
             VALUES (:codigo, :nombre, :precio, 1)'
        );
        $consultaInsertar->execute([
            'codigo' => $codigo,
            'nombre' => $nombre,
            'precio' => $precio
        ]);
        $productoId = (int) $conexion->lastInsertId();
    } else {
        if ((int) $producto['activo'] !== 1) {
            $conexion->rollBack();
            responderJson(['success' => false, 'message' => 'El código pertenece a un producto inactivo.'], 409);
        }
        $productoId = (int) $producto['id'];
        $consultaInventario = $conexion->prepare(
            'SELECT id FROM inventario_sucursal
             WHERE producto_id = :producto_id AND sucursal_id = :sucursal_id
             LIMIT 1'
        );
        $consultaInventario->execute(['producto_id' => $productoId, 'sucursal_id' => $sucursalId]);
        if ($consultaInventario->fetchColumn() !== false) {
            $conexion->rollBack();
            responderJson(['success' => false, 'message' => 'Este producto ya está registrado en tu sucursal.'], 409);
        }
        $mensaje = 'El producto ya existía en el catálogo y fue agregado a tu sucursal.';
    }

    $consultaInventario = $conexion->prepare(
        'INSERT INTO inventario_sucursal (producto_id, sucursal_id, existencias, stock_minimo)
         VALUES (:producto_id, :sucursal_id, :existencias, :stock_minimo)'
    );
    $consultaInventario->execute([
        'producto_id' => $productoId,
        'sucursal_id' => $sucursalId,
        'existencias' => (int) $existencias,
        'stock_minimo' => (int) $stockMinimo
    ]);

    $conexion->commit();
    responderJson(['success' => true, 'message' => $mensaje]);
} catch (PDOException $e) {
    if (isset($conexion) && $conexion->inTransaction()) $conexion->rollBack();
    if (($e->errorInfo[1] ?? null) === 1062) {
        responderJson(['success' => false, 'message' => 'Este producto ya está registrado en tu sucursal.'], 409);
    }
    responderJson(['success' => false, 'message' => 'No se pudo agregar el producto.'], 500);
} catch (Throwable $e) {
    if (isset($conexion) && $conexion->inTransaction()) $conexion->rollBack();
    responderJson(['success' => false, 'message' => 'No se pudo agregar el producto.'], 500);
}
