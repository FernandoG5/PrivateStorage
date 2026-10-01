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
        'message' => 'El identificador del gerente no es válido.'
    ], 400);
}

$id = (int) $idRecibido;

try {
    require_once __DIR__ . '/../../config/database.php';

    $consultaExiste = $conexion->prepare(
        'SELECT id FROM usuarios WHERE id = :id AND rol_id = 2 LIMIT 1'
    );
    $consultaExiste->execute(['id' => $id]);

    if ($consultaExiste->fetchColumn() === false) {
        responderJson([
            'success' => false,
            'message' => 'El gerente no existe.'
        ], 404);
    }

    $consulta = $conexion->prepare(
        'UPDATE usuarios
         SET activo = 0
         WHERE id = :id
           AND rol_id = 2'
    );
    $consulta->execute(['id' => $id]);

    responderJson([
        'success' => true,
        'message' => 'Gerente desactivado correctamente.'
    ]);
} catch (Throwable $e) {
    responderJson([
        'success' => false,
        'message' => 'No se pudo desactivar el gerente.'
    ], 500);
}
