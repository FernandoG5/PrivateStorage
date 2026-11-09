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

$idRecibido = $_POST['id'] ?? '';
$nombre = $_POST['nombre'] ?? null;
$direccion = $_POST['direccion'] ?? null;
$telefono = $_POST['telefono'] ?? null;
$estado = $_POST['estado'] ?? null;

if (
    !is_string($idRecibido) ||
    !is_string($nombre) ||
    !is_string($direccion) ||
    !is_string($telefono) ||
    !is_string($estado)
) {
    responderJson([
        'success' => false,
        'message' => 'Los datos de la sucursal no son válidos.'
    ], 400);
}

$idRecibido = trim($idRecibido);
$nombre = trim($nombre);
$direccion = trim($direccion);
$telefono = trim($telefono);
$estado = trim($estado);

if ($nombre === '' || $direccion === '' || $telefono === '') {
    responderJson([
        'success' => false,
        'message' => 'Nombre, dirección y teléfono son obligatorios.'
    ], 400);
}

if (!in_array($estado, ['activa', 'inactiva'], true)) {
    responderJson([
        'success' => false,
        'message' => 'El estado de la sucursal no es válido.'
    ], 400);
}

$id = null;
if ($idRecibido !== '') {
    if (!ctype_digit($idRecibido) || (int) $idRecibido <= 0) {
        responderJson([
            'success' => false,
            'message' => 'El identificador de la sucursal no es válido.'
        ], 400);
    }

    $id = (int) $idRecibido;
}

try {
    require_once __DIR__ . '/../../config/database.php';

    if ($id === null) {
        $consulta = $conexion->prepare(
            'INSERT INTO sucursales (nombre, direccion, telefono, estado)
             VALUES (:nombre, :direccion, :telefono, :estado)'
        );
        $consulta->execute([
            'nombre' => $nombre,
            'direccion' => $direccion,
            'telefono' => $telefono,
            'estado' => $estado
        ]);
    } else {
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
            'UPDATE sucursales
             SET nombre = :nombre,
                 direccion = :direccion,
                 telefono = :telefono,
                 estado = :estado
             WHERE id = :id'
        );
        $consulta->execute([
            'nombre' => $nombre,
            'direccion' => $direccion,
            'telefono' => $telefono,
            'estado' => $estado,
            'id' => $id
        ]);
    }

    responderJson([
        'success' => true,
        'message' => 'Sucursal guardada correctamente.'
    ]);
} catch (PDOException $e) {
    $codigoMysql = $e->errorInfo[1] ?? null;

    if ($codigoMysql === 1062) {
        responderJson([
            'success' => false,
            'message' => 'Ya existe una sucursal con ese nombre.'
        ], 409);
    }

    responderJson([
        'success' => false,
        'message' => 'No se pudo guardar la sucursal.'
    ], 500);
} catch (Throwable $e) {
    responderJson([
        'success' => false,
        'message' => 'No se pudo guardar la sucursal.'
    ], 500);
}
