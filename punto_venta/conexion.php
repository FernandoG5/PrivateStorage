<?php

$servidor = "127.0.0.1";
$puerto = "3306";
$base_datos = "punto_venta";
$usuario = "root";
$contrasena = "";

try {
    $conexion = new PDO(
        "mysql:host=$servidor;port=$puerto;dbname=$base_datos;charset=utf8mb4",
        $usuario,
        $contrasena,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $error) {
    error_log($error->getMessage());

    http_response_code(500);
    header("Content-Type: application/json; charset=utf-8");

    echo json_encode([
        "mensaje" => "No se pudo conectar con la base de datos."
    ]);

    exit;
}