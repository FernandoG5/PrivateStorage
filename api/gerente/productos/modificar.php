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
    if (($_SESSION['autenticado'] ?? false) !== true) responderJson(['success' => false, 'message' => 'La sesión ha expirado.', 'redirect' => '/PrivateStorage-main/src/inicio_sesion/inicioSesion.html'], 401);
    if ((int) ($_SESSION['rol_id'] ?? 0) !== 2) responderJson(['success' => false, 'message' => 'No tienes permiso para acceder a este módulo.'], 403);
    $sucursalId = $_SESSION['sucursal_id'] ?? null;
    if ((!is_int($sucursalId) && !ctype_digit((string) $sucursalId)) || (int) $sucursalId <= 0) responderJson(['success' => false, 'message' => 'La sucursal del gerente no es válida.'], 403);
    return (int) $sucursalId;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') responderJson(['success' => false, 'message' => 'Solamente se permite el método POST.'], 405);
$sucursalId = obtenerSucursalSesion();
$id = $_POST['id_producto'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$precio = $_POST['precio'] ?? null;
$stockMinimo = $_POST['stock_minimo'] ?? null;

if (!is_string($id) || !is_string($nombre) || !is_string($precio) || !is_string($stockMinimo) || !ctype_digit($id) || (int) $id <= 0) responderJson(['success' => false, 'message' => 'Los datos del producto no son válidos.'], 400);
$nombre = trim($nombre);
if ($nombre === '') responderJson(['success' => false, 'message' => 'El nombre es obligatorio.'], 400);
if (!is_numeric($precio) || (float) $precio < 0 || (float) $precio > 99999999.99) responderJson(['success' => false, 'message' => 'El precio no es válido.'], 400);
if (!ctype_digit($stockMinimo) || (int) $stockMinimo < 0 || (int) $stockMinimo > 1000000) responderJson(['success' => false, 'message' => 'El stock mínimo no es válido.'], 400);

try {
    require_once __DIR__ . '/../../../config/database.php';
    $conexion->beginTransaction();
    $consulta = $conexion->prepare(
        'SELECT p.id FROM productos p
         INNER JOIN inventario_sucursal i ON i.producto_id = p.id
         WHERE p.id = :id AND p.activo = 1 AND i.sucursal_id = :sucursal_id
         LIMIT 1 FOR UPDATE'
    );
    $consulta->execute(['id' => (int) $id, 'sucursal_id' => $sucursalId]);
    if ($consulta->fetchColumn() === false) {
        $conexion->rollBack();
        responderJson(['success' => false, 'message' => 'El producto no pertenece a tu sucursal.'], 404);
    }
    $actualizarProducto = $conexion->prepare('UPDATE productos SET nombre = :nombre, precio = :precio WHERE id = :id');
    $actualizarProducto->execute(['nombre' => $nombre, 'precio' => $precio, 'id' => (int) $id]);
    $actualizarInventario = $conexion->prepare('UPDATE inventario_sucursal SET stock_minimo = :stock_minimo WHERE producto_id = :id AND sucursal_id = :sucursal_id');
    $actualizarInventario->execute(['stock_minimo' => (int) $stockMinimo, 'id' => (int) $id, 'sucursal_id' => $sucursalId]);
    $conexion->commit();
    responderJson(['success' => true, 'message' => 'Producto modificado correctamente.']);
} catch (Throwable $e) {
    if (isset($conexion) && $conexion->inTransaction()) $conexion->rollBack();
    responderJson(['success' => false, 'message' => 'No se pudo modificar el producto.'], 500);
}
