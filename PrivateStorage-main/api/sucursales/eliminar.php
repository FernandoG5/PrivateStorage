<?php

header('Content-Type: application/json; charset=utf-8');

function responderJson(array $respuesta, int $codigoHttp = 200): never
{
    http_response_code($codigoHttp);
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJson([
        'success' => false,
        'message' => 'Solamente se permite el método POST.'
    ], 405);
}

$idRecibido = $_POST['id'] ?? null;

if (!is_string($idRecibido) || !ctype_digit($idRecibido) || (int) $idRecibido <= 0) {
    responderJson([
        'success' => false,
        'message' => 'El identificador de la sucursal no es válido.'
    ], 400);
}

$id = (int) $idRecibido;

try {
    require_once __DIR__ . '/../../config/database.php';

    $consultaExiste = $conexion->prepare(
        'SELECT id FROM sucursales WHERE id = :id LIMIT 1'
    );
    $consultaExiste->execute(['id' => $id]);

    if ($consultaExiste->fetchColumn() === false) {
        responderJson([
            'success' => false,
            'message' => 'La sucursal no existe.'
        ], 404);
    }

    $consulta = $conexion->prepare(
        'DELETE FROM sucursales WHERE id = :id'
    );
    $consulta->execute(['id' => $id]);

    responderJson([
        'success' => true,
        'message' => 'Sucursal eliminada correctamente.'
    ]);
} catch (PDOException $e) {
    $codigoMysql = $e->errorInfo[1] ?? null;

    if ($codigoMysql === 1451) {
        responderJson([
            'success' => false,
            'message' => 'No se puede eliminar esta sucursal porque tiene usuarios asociados.'
        ], 409);
    }

    responderJson([
        'success' => false,
        'message' => 'No se pudo eliminar la sucursal.'
    ], 500);
} catch (Throwable $e) {
    responderJson([
        'success' => false,
        'message' => 'No se pudo eliminar la sucursal.'
    ], 500);
}
