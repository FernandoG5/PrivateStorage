<?php

header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store");

if (!in_array(
    $_SERVER["REMOTE_ADDR"] ?? "",
    ["127.0.0.1", "::1"],
    true
)) {
    http_response_code(403);

    echo json_encode([
        "mensaje" => "Esta prueba solo está habilitada en localhost."
    ]);

    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "mensaje" => "Método no permitido."
    ]);

    exit;
}

require_once __DIR__ . "/conexion.php";

$id_sucursal = 1;

try {
    $sql = "SELECT
                id_producto,
                codigo,
                nombre,
                precio,
                existencias,
                stock_minimo
            FROM productos
            WHERE id_sucursal = ?
            AND activo = 1
            ORDER BY nombre";

    $consulta = $conexion->prepare($sql);

    $consulta->execute([$id_sucursal]);

    $productos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(
        ["productos" => $productos],
        JSON_UNESCAPED_UNICODE
    );

} catch (PDOException $error) {
    error_log($error->getMessage());

    http_response_code(500);

    echo json_encode([
        "mensaje" => "No se pudieron consultar los productos."
    ]);
}