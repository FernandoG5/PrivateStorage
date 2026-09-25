<?php

header("Content-Type: application/json; charset=utf-8");

function responder($mensaje, $codigo = 200) {
    http_response_code($codigo);

    echo json_encode(
        ["mensaje" => $mensaje],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

if (!in_array(
    $_SERVER["REMOTE_ADDR"] ?? "",
    ["127.0.0.1", "::1"],
    true
)) {
    responder("Esta prueba solo está habilitada en localhost.", 403);
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responder("Utiliza el formulario para registrar productos.", 405);
}

$campos = [
    "codigo",
    "nombre",
    "precio",
    "existencias",
    "stock_minimo"
];

foreach ($campos as $campo) {
    if (
        !isset($_POST[$campo]) ||
        !is_string($_POST[$campo]) ||
        trim($_POST[$campo]) === ""
    ) {
        responder("Completa correctamente todos los campos.", 422);
    }
}

$codigo = trim($_POST["codigo"]);
$nombre = trim($_POST["nombre"]);
$precio = trim($_POST["precio"]);

$existencias = filter_var(
    $_POST["existencias"],
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 0, "max_range" => 1000000]]
);

$stock_minimo = filter_var(
    $_POST["stock_minimo"],
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 0, "max_range" => 1000000]]
);

if (
    preg_match_all("/./us", $codigo) > 50 ||
    preg_match_all("/./us", $nombre) > 120
) {
    responder("El código o el nombre es demasiado largo.", 422);
}

if (!preg_match('/^\d{1,8}(\.\d{1,2})?$/', $precio)) {
    responder("Escribe un precio válido con máximo dos decimales.", 422);
}

if ($existencias === false || $stock_minimo === false) {
    responder("Las existencias y el mínimo deben ser enteros no negativos.", 422);
}

require_once __DIR__ . "/conexion.php";

$id_sucursal = 1;

try {
    $consultaSucursal = $conexion->prepare(
        "SELECT id_sucursal
         FROM sucursales
         WHERE id_sucursal = ?
           AND estado = 'activa'"
    );

    $consultaSucursal->execute([$id_sucursal]);

    if (!$consultaSucursal->fetch()) {
        responder("La sucursal indicada no existe o está inactiva.", 422);
    }

    $sql = "INSERT INTO productos (
                id_sucursal,
                codigo,
                nombre,
                precio,
                existencias,
                stock_minimo
            )
            VALUES (?, ?, ?, ?, ?, ?)";

    $consulta = $conexion->prepare($sql);

    $consulta->execute([
        $id_sucursal,
        $codigo,
        $nombre,
        $precio,
        $existencias,
        $stock_minimo
    ]);

    responder("Producto guardado correctamente.", 201);

} catch (PDOException $error) {
    error_log($error->getMessage());

    if (($error->errorInfo[1] ?? 0) == 1062) {
        responder("Ese código ya está registrado en esta sucursal.", 409);
    }

    responder("No se pudo guardar el producto. Revisa la conexión y la tabla.", 500);
}