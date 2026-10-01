<?php

header('Content-Type: application/json; charset=utf-8');

function responderJson(array $respuesta, int $codigoHttp = 200): never
{
    http_response_code($codigoHttp);
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responderJson([
        'success' => false,
        'message' => 'Solamente se permite el método GET.'
    ], 405);
}

try {
    require_once __DIR__ . '/../../config/database.php';

    $consulta = $conexion->prepare(
        'SELECT id, nombre, direccion, telefono, estado, fecha_creacion
         FROM sucursales
         ORDER BY id DESC'
    );
    $consulta->execute();

    responderJson([
        'success' => true,
        'sucursales' => $consulta->fetchAll()
    ]);
} catch (Throwable $e) {
    responderJson([
        'success' => false,
        'message' => 'No se pudieron cargar las sucursales.'
    ], 500);
}
