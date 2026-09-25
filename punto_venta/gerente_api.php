<?php

header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store");

function responder($datos, $codigo = 200) {
    http_response_code($codigo);

    echo json_encode(
        $datos,
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );

    exit;
}

if (!in_array(
    $_SERVER["REMOTE_ADDR"] ?? "",
    ["127.0.0.1", "::1"],
    true
)) {
    responder([
        "mensaje" => "Esta versión de prueba solo permite acceso desde localhost."
    ], 403);
}

ini_set("session.use_strict_mode", "1");

session_name("gerente_local");

session_set_cookie_params([
    "httponly" => true,
    "samesite" => "Strict"
]);

session_start();

if (empty($_SESSION["token_gerente"])) {
    $_SESSION["token_gerente"] = bin2hex(random_bytes(32));
}

require_once __DIR__ . "/conexion.php";

$id_sucursal = 1;

function ejecutarConsulta($sql, $valores = []) {
    global $conexion;

    $consulta = $conexion->prepare($sql);
    $consulta->execute($valores);

    return $consulta;
}

function leerTexto($campo, $maximo) {
    $valor = $_POST[$campo] ?? null;

    if (!is_string($valor)) {
        throw new RuntimeException(
            "Revisa el campo: $campo.",
            422
        );
    }

    $valor = trim($valor);
    $longitud = preg_match_all("/./us", $valor);

    if (
        $valor === "" ||
        $longitud === false ||
        $longitud > $maximo
    ) {
        throw new RuntimeException(
            "Revisa la longitud del campo: $campo.",
            422
        );
    }

    return $valor;
}

function leerEntero($campo, $minimo = 0, $maximo = 1000000) {
    $valor = $_POST[$campo] ?? null;

    if (!is_string($valor)) {
        throw new RuntimeException(
            "Revisa el campo: $campo.",
            422
        );
    }

    $numero = filter_var(
        $valor,
        FILTER_VALIDATE_INT,
        [
            "options" => [
                "min_range" => $minimo,
                "max_range" => $maximo
            ]
        ]
    );

    if ($numero === false) {
        throw new RuntimeException(
            "El campo $campo debe ser un entero entre $minimo y $maximo.",
            422
        );
    }

    return $numero;
}

function leerPrecio() {
    $precio = $_POST["precio"] ?? null;

    if (!is_string($precio)) {
        throw new RuntimeException("Precio inválido.", 422);
    }

    $precio = trim($precio);

    if (!preg_match('/^\d{1,8}(\.\d{1,2})?$/', $precio)) {
        throw new RuntimeException(
            "El precio debe ser no negativo y tener máximo dos decimales.",
            422
        );
    }

    return $precio;
}

function buscarProductoBloqueado($id_producto, $id_sucursal) {
    $producto = ejecutarConsulta(
        "SELECT id_producto, existencias
         FROM productos
         WHERE id_producto = ?
           AND id_sucursal = ?
           AND activo = 1
         FOR UPDATE",
        [$id_producto, $id_sucursal]
    )->fetch();

    if (!$producto) {
        throw new RuntimeException(
            "El producto no existe o ya está inactivo en esta sucursal.",
            404
        );
    }

    return $producto;
}

try {
    $sucursal = ejecutarConsulta(
        "SELECT id_sucursal, nombre, direccion, telefono, contacto, estado
         FROM sucursales
         WHERE id_sucursal = ?
           AND estado = 'activa'",
        [$id_sucursal]
    )->fetch();

    if (!$sucursal) {
        throw new RuntimeException(
            "La sucursal configurada no existe o está inactiva.",
            422
        );
    }

    $metodo = $_SERVER["REQUEST_METHOD"];
    $accion = $_GET["accion"] ?? "";

    if ($metodo === "GET") {
        if ($accion === "datos") {
            $productos = ejecutarConsulta(
                "SELECT
                    id_producto,
                    codigo,
                    nombre,
                    precio,
                    existencias,
                    stock_minimo
                 FROM productos
                 WHERE id_sucursal = ?
                   AND activo = 1
                 ORDER BY nombre",
                [$id_sucursal]
            )->fetchAll();

            $cajas = ejecutarConsulta(
                "SELECT id_caja, nombre, activa
                 FROM cajas
                 WHERE id_sucursal = ?
                 ORDER BY nombre",
                [$id_sucursal]
            )->fetchAll();

            $ventas = ejecutarConsulta(
                "SELECT
                    v.id_venta,
                    v.fecha,
                    v.total,
                    c.nombre AS caja,
                    u.nombre AS cajero
                 FROM ventas v
                 INNER JOIN cajas c
                    ON c.id_caja = v.id_caja
                 INNER JOIN usuarios u
                    ON u.id_usuario = v.id_cajero
                 WHERE v.id_sucursal = ?
                 ORDER BY v.fecha DESC, v.id_venta DESC",
                [$id_sucursal]
            )->fetchAll();

            responder([
                "token" => $_SESSION["token_gerente"],
                "sucursal" => $sucursal,
                "productos" => $productos,
                "cajas" => $cajas,
                "ventas" => $ventas
            ]);
        }

        if ($accion === "detalle_venta") {
            $valor = $_GET["id"] ?? "";

            $id_venta = is_string($valor)
                ? filter_var(
                    $valor,
                    FILTER_VALIDATE_INT,
                    ["options" => ["min_range" => 1]]
                )
                : false;

            if ($id_venta === false) {
                throw new RuntimeException(
                    "Identificador de venta inválido.",
                    422
                );
            }

            $venta = ejecutarConsulta(
                "SELECT id_venta, fecha, total
                 FROM ventas
                 WHERE id_venta = ?
                   AND id_sucursal = ?",
                [$id_venta, $id_sucursal]
            )->fetch();

            if (!$venta) {
                throw new RuntimeException(
                    "Venta no encontrada en esta sucursal.",
                    404
                );
            }

            $detalle = ejecutarConsulta(
                "SELECT
                    p.codigo,
                    p.nombre,
                    d.cantidad,
                    d.precio_unitario
                 FROM detalle_venta d
                 INNER JOIN productos p
                    ON p.id_producto = d.id_producto
                 WHERE d.id_venta = ?
                 ORDER BY d.id_detalle",
                [$id_venta]
            )->fetchAll();

            responder([
                "venta" => $venta,
                "detalle" => $detalle
            ]);
        }

        throw new RuntimeException("Consulta no encontrada.", 404);
    }

    if ($metodo !== "POST") {
        throw new RuntimeException("Método no permitido.", 405);
    }

    $token = $_POST["token"] ?? "";

    if (
        !is_string($token) ||
        !hash_equals($_SESSION["token_gerente"], $token)
    ) {
        throw new RuntimeException(
            "La sesión de la página cambió. Recarga antes de guardar.",
            403
        );
    }

    switch ($accion) {
        case "agregar_producto":
            $codigo = leerTexto("codigo", 50);
            $nombre = leerTexto("nombre", 120);
            $precio = leerPrecio();
            $existencias = leerEntero("existencias");
            $minimo = leerEntero("stock_minimo");

            ejecutarConsulta(
                "INSERT INTO productos (
                    id_sucursal,
                    codigo,
                    nombre,
                    precio,
                    existencias,
                    stock_minimo
                 )
                 VALUES (?, ?, ?, ?, ?, ?)",
                [
                    $id_sucursal,
                    $codigo,
                    $nombre,
                    $precio,
                    $existencias,
                    $minimo
                ]
            );

            $mensaje = "Producto guardado correctamente.";
            break;

        case "modificar_producto":
            $id_producto = leerEntero("id_producto", 1, 2147483647);
            $nombre = leerTexto("nombre", 120);
            $precio = leerPrecio();
            $minimo = leerEntero("stock_minimo");

            $conexion->beginTransaction();

            buscarProductoBloqueado($id_producto, $id_sucursal);

            ejecutarConsulta(
                "UPDATE productos
                 SET nombre = ?, precio = ?, stock_minimo = ?
                 WHERE id_producto = ?
                   AND id_sucursal = ?",
                [
                    $nombre,
                    $precio,
                    $minimo,
                    $id_producto,
                    $id_sucursal
                ]
            );

            $conexion->commit();

            $mensaje = "Producto modificado correctamente.";
            break;

        case "eliminar_producto":
            $id_producto = leerEntero("id_producto", 1, 2147483647);

            $conexion->beginTransaction();

            buscarProductoBloqueado($id_producto, $id_sucursal);

            ejecutarConsulta(
                "UPDATE productos
                 SET activo = 0
                 WHERE id_producto = ?
                   AND id_sucursal = ?",
                [$id_producto, $id_sucursal]
            );

            $conexion->commit();

            $mensaje = "Producto retirado del catálogo. Su historial se conserva.";
            break;

        case "actualizar_stock":
            $id_producto = leerEntero("id_producto", 1, 2147483647);
            $existencias = leerEntero("existencias");
            $anteriores = leerEntero("existencias_anteriores");

            $conexion->beginTransaction();

            $producto = buscarProductoBloqueado(
                $id_producto,
                $id_sucursal
            );

            if ((int)$producto["existencias"] !== $anteriores) {
                throw new RuntimeException(
                    "Las existencias cambiaron desde tu consulta. Actualiza los datos y revisa el ajuste.",
                    409
                );
            }

            ejecutarConsulta(
                "UPDATE productos
                 SET existencias = ?
                 WHERE id_producto = ?
                   AND id_sucursal = ?",
                [$existencias, $id_producto, $id_sucursal]
            );

            $conexion->commit();

            $mensaje = "Existencias actualizadas correctamente.";
            break;

        case "agregar_caja":
            $nombre = leerTexto("nombre", 50);

            ejecutarConsulta(
                "INSERT INTO cajas (id_sucursal, nombre)
                 VALUES (?, ?)",
                [$id_sucursal, $nombre]
            );

            $mensaje = "Caja registrada correctamente.";
            break;

        case "eliminar_caja":
            $id_caja = leerEntero("id_caja", 1, 2147483647);

            $consulta = ejecutarConsulta(
                "UPDATE cajas
                 SET activa = 0
                 WHERE id_caja = ?
                   AND id_sucursal = ?
                   AND activa = 1",
                [$id_caja, $id_sucursal]
            );

            if ($consulta->rowCount() === 0) {
                throw new RuntimeException(
                    "La caja no existe o ya está inactiva.",
                    404
                );
            }

            $mensaje = "Caja desactivada. Sus ventas se conservan.";
            break;

        default:
            throw new RuntimeException(
                "Operación no encontrada.",
                404
            );
    }

    responder(["mensaje" => $mensaje]);

} catch (Throwable $error) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    if ($error instanceof PDOException) {
        error_log($error->getMessage());

        if (($error->errorInfo[1] ?? 0) == 1062) {
            responder([
                "mensaje" => "Ese código de producto o nombre de caja ya existe en la sucursal, incluso si está inactivo."
            ], 409);
        }

        responder([
            "mensaje" => "No se pudo realizar la operación. Revisa las tablas y el registro de errores de PHP."
        ], 500);
    }

    $codigo = (int)$error->getCode();

    if (!in_array($codigo, [403, 404, 405, 409, 422], true)) {
        error_log($error->getMessage());

        responder([
            "mensaje" => "Ocurrió un error al procesar la operación."
        ], 500);
    }

    responder([
        "mensaje" => $error->getMessage()
    ], $codigo);
}