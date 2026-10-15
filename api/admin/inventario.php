<?php

require_once __DIR__ . '/../../config/app.php';

session_start();
header('Content-Type: application/json; charset=utf-8');

function responderJson(array $respuesta, int $codigoHttp = 200): never
{
    http_response_code($codigoHttp);
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
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

try {
    require_once __DIR__ . '/../../config/database.php';

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $consultaCategorias = $conexion->prepare(
            'SELECT id, nombre
             FROM categorias
             WHERE activo = 1
             ORDER BY nombre ASC'
        );
        $consultaCategorias->execute();

        $consultaSucursales = $conexion->prepare(
            'SELECT id, nombre, estado
             FROM sucursales
             ORDER BY nombre ASC'
        );
        $consultaSucursales->execute();

        $consultaProductos = $conexion->prepare(
            "SELECT p.id, p.codigo, p.nombre, p.precio, p.categoria_id,
                    COALESCE(c.nombre, 'Sin categoría') AS categoria,
                    i.sucursal_id, COALESCE(s.nombre, 'Sin sucursal') AS sucursal,
                    i.existencias, i.stock_minimo
             FROM productos p
             LEFT JOIN categorias c ON c.id = p.categoria_id
             LEFT JOIN inventario_sucursal i ON i.producto_id = p.id
             LEFT JOIN sucursales s ON s.id = i.sucursal_id
             WHERE p.activo = 1
             ORDER BY p.nombre ASC, s.nombre ASC"
        );
        $consultaProductos->execute();

        responderJson([
            'success' => true,
            'categorias' => $consultaCategorias->fetchAll(),
            'sucursales' => $consultaSucursales->fetchAll(),
            'productos' => $consultaProductos->fetchAll()
        ]);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        responderJson([
            'success' => false,
            'message' => 'Solamente se permiten los métodos GET y POST.'
        ], 405);
    }

    $idRecibido = $_POST['id'] ?? '';
    $codigo = $_POST['codigo'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $precio = $_POST['precio'] ?? null;
    $categoriaIdRecibido = $_POST['categoria_id'] ?? null;
    $sucursalIdRecibido = $_POST['sucursal_id'] ?? null;
    $existencias = $_POST['existencias'] ?? null;
    $stockMinimo = $_POST['stock_minimo'] ?? null;

    if (
        !is_string($idRecibido)
        || !is_string($codigo)
        || !is_string($nombre)
        || !is_string($precio)
        || !is_string($categoriaIdRecibido)
        || !is_string($sucursalIdRecibido)
        || !is_string($existencias)
        || !is_string($stockMinimo)
    ) {
        responderJson(['success' => false, 'message' => 'Los datos del producto no son válidos.'], 400);
    }

    $idRecibido = trim($idRecibido);
    $codigo = trim($codigo);
    $nombre = trim($nombre);

    if ($codigo === '' || strlen($codigo) > 50 || $nombre === '' || mb_strlen($nombre) > 120) {
        responderJson(['success' => false, 'message' => 'Código y nombre son obligatorios y deben ser válidos.'], 400);
    }
    if (!is_numeric($precio) || (float) $precio < 0 || (float) $precio > 99999999.99) {
        responderJson(['success' => false, 'message' => 'El precio no es válido.'], 400);
    }
    if (!ctype_digit($categoriaIdRecibido) || (int) $categoriaIdRecibido <= 0) {
        responderJson(['success' => false, 'message' => 'Selecciona una categoría válida.'], 400);
    }
    if (!ctype_digit($sucursalIdRecibido) || (int) $sucursalIdRecibido <= 0) {
        responderJson(['success' => false, 'message' => 'Selecciona una sucursal válida.'], 400);
    }
    if (!ctype_digit($existencias) || (int) $existencias > 1000000) {
        responderJson(['success' => false, 'message' => 'Las existencias no son válidas.'], 400);
    }
    if (!ctype_digit($stockMinimo) || (int) $stockMinimo > 1000000) {
        responderJson(['success' => false, 'message' => 'El stock mínimo no es válido.'], 400);
    }

    $id = null;
    if ($idRecibido !== '') {
        if (!ctype_digit($idRecibido) || (int) $idRecibido <= 0) {
            responderJson(['success' => false, 'message' => 'El identificador del producto no es válido.'], 400);
        }
        $id = (int) $idRecibido;
    }

    $categoriaId = (int) $categoriaIdRecibido;
    $sucursalId = (int) $sucursalIdRecibido;

    $conexion->beginTransaction();

    $consultaCategoria = $conexion->prepare(
        'SELECT id FROM categorias WHERE id = :id AND activo = 1 LIMIT 1'
    );
    $consultaCategoria->execute(['id' => $categoriaId]);
    if ($consultaCategoria->fetchColumn() === false) {
        $conexion->rollBack();
        responderJson(['success' => false, 'message' => 'La categoría seleccionada no existe o está inactiva.'], 400);
    }

    $consultaSucursal = $conexion->prepare(
        'SELECT id FROM sucursales WHERE id = :id LIMIT 1'
    );
    $consultaSucursal->execute(['id' => $sucursalId]);
    if ($consultaSucursal->fetchColumn() === false) {
        $conexion->rollBack();
        responderJson(['success' => false, 'message' => 'La sucursal seleccionada no existe o está inactiva.'], 400);
    }

    if ($id === null) {
        $consultaCodigo = $conexion->prepare(
            'SELECT id FROM productos WHERE codigo = :codigo LIMIT 1'
        );
        $consultaCodigo->execute(['codigo' => $codigo]);
        if ($consultaCodigo->fetchColumn() !== false) {
            $conexion->rollBack();
            responderJson(['success' => false, 'message' => 'Ya existe un producto con ese código.'], 409);
        }

        $insertarProducto = $conexion->prepare(
            'INSERT INTO productos (codigo, nombre, precio, categoria_id, activo)
             VALUES (:codigo, :nombre, :precio, :categoria_id, 1)'
        );
        $insertarProducto->execute([
            'codigo' => $codigo,
            'nombre' => $nombre,
            'precio' => $precio,
            'categoria_id' => $categoriaId
        ]);
        $id = (int) $conexion->lastInsertId();

        $insertarInventario = $conexion->prepare(
            'INSERT INTO inventario_sucursal (producto_id, sucursal_id, existencias, stock_minimo)
             VALUES (:producto_id, :sucursal_id, :existencias, :stock_minimo)'
        );
        $insertarInventario->execute([
            'producto_id' => $id,
            'sucursal_id' => $sucursalId,
            'existencias' => (int) $existencias,
            'stock_minimo' => (int) $stockMinimo
        ]);
        $mensaje = 'Producto agregado correctamente.';
    } else {
        $consultaProducto = $conexion->prepare(
            'SELECT id FROM productos WHERE id = :id AND activo = 1 LIMIT 1 FOR UPDATE'
        );
        $consultaProducto->execute(['id' => $id]);
        if ($consultaProducto->fetchColumn() === false) {
            $conexion->rollBack();
            responderJson(['success' => false, 'message' => 'El producto no existe o está inactivo.'], 404);
        }

        $consultaDuplicado = $conexion->prepare(
            'SELECT id FROM productos WHERE codigo = :codigo AND id <> :id LIMIT 1'
        );
        $consultaDuplicado->execute(['codigo' => $codigo, 'id' => $id]);
        if ($consultaDuplicado->fetchColumn() !== false) {
            $conexion->rollBack();
            responderJson(['success' => false, 'message' => 'Ya existe otro producto con ese código.'], 409);
        }

        $actualizarProducto = $conexion->prepare(
            'UPDATE productos
             SET codigo = :codigo, nombre = :nombre, precio = :precio, categoria_id = :categoria_id
             WHERE id = :id'
        );
        $actualizarProducto->execute([
            'codigo' => $codigo,
            'nombre' => $nombre,
            'precio' => $precio,
            'categoria_id' => $categoriaId,
            'id' => $id
        ]);

        $actualizarInventario = $conexion->prepare(
            'UPDATE inventario_sucursal
             SET sucursal_id = :sucursal_id, existencias = :existencias, stock_minimo = :stock_minimo
             WHERE producto_id = :producto_id AND sucursal_id = :sucursal_anterior'
        );
        $actualizarInventario->execute([
            'sucursal_id' => $sucursalId,
            'existencias' => (int) $existencias,
            'stock_minimo' => (int) $stockMinimo,
            'producto_id' => $id,
            'sucursal_anterior' => (int) ($_POST['sucursal_anterior_id'] ?? $sucursalId)
        ]);

        if ($actualizarInventario->rowCount() === 0) {
            $consultaInventario = $conexion->prepare(
                'SELECT id FROM inventario_sucursal
                 WHERE producto_id = :producto_id AND sucursal_id = :sucursal_id
                 LIMIT 1'
            );
            $consultaInventario->execute(['producto_id' => $id, 'sucursal_id' => $sucursalId]);
            if ($consultaInventario->fetchColumn() === false) {
                $conexion->rollBack();
                responderJson(['success' => false, 'message' => 'No se encontró la existencia de esta sucursal.'], 404);
            }
        }
        $mensaje = 'Producto modificado correctamente.';
    }

    $conexion->commit();
    responderJson(['success' => true, 'message' => $mensaje]);
} catch (PDOException $e) {
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }
    if (($e->errorInfo[1] ?? null) === 1062) {
        responderJson(['success' => false, 'message' => 'Ya existe un producto con ese código en la sucursal.'], 409);
    }
    error_log('Error al guardar el inventario del administrador: ' . $e->getMessage());
    responderJson(['success' => false, 'message' => 'No se pudo guardar el producto.'], 500);
} catch (Throwable $e) {
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }
    error_log('Error al guardar el inventario del administrador: ' . $e->getMessage());
    responderJson(['success' => false, 'message' => 'No se pudo guardar el producto.'], 500);
}
