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
        'SELECT u.id, u.nombre, u.usuario, u.sucursal_id, u.activo, u.fecha_creacion,
                s.nombre AS sucursal
         FROM usuarios u
         LEFT JOIN sucursales s ON s.id = u.sucursal_id
         WHERE u.rol_id = 2
         ORDER BY u.id DESC'
    );
    $consulta->execute();

    responderJson([
        'success' => true,
        'gerentes' => $consulta->fetchAll()
    ]);
} catch (Throwable $e) {
    responderJson([
        'success' => false,
        'message' => 'No se pudieron cargar los gerentes.'
    ], 500);
}
