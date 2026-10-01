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
$usuario = $_POST['usuario'] ?? null;
$password = $_POST['password'] ?? null;
$sucursalIdRecibido = $_POST['sucursal_id'] ?? null;
$activo = $_POST['activo'] ?? null;

if (
    !is_string($idRecibido) ||
    !is_string($nombre) ||
    !is_string($usuario) ||
    !is_string($password) ||
    !is_string($sucursalIdRecibido) ||
    !is_string($activo)
) {
    responderJson([
        'success' => false,
        'message' => 'Los datos del gerente no son válidos.'
    ], 400);
}

$idRecibido = trim($idRecibido);
$nombre = trim($nombre);
$usuario = trim($usuario);
$sucursalIdRecibido = trim($sucursalIdRecibido);

if ($nombre === '' || $usuario === '') {
    responderJson([
        'success' => false,
        'message' => 'Nombre y usuario son obligatorios.'
    ], 400);
}

if (!ctype_digit($sucursalIdRecibido) || (int) $sucursalIdRecibido <= 0) {
    responderJson([
        'success' => false,
        'message' => 'La sucursal seleccionada no es válida.'
    ], 400);
}

if (!in_array($activo, ['0', '1'], true)) {
    responderJson([
        'success' => false,
        'message' => 'El estado del gerente no es válido.'
    ], 400);
}

$sucursalId = (int) $sucursalIdRecibido;
$id = null;

if ($idRecibido !== '') {
    if (!ctype_digit($idRecibido) || (int) $idRecibido <= 0) {
        responderJson([
            'success' => false,
            'message' => 'El identificador del gerente no es válido.'
        ], 400);
    }

    $id = (int) $idRecibido;
}

if ($id === null && $password === '') {
    responderJson([
        'success' => false,
        'message' => 'La contraseña es obligatoria para crear un gerente.'
    ], 400);
}

try {
    require_once __DIR__ . '/../../config/database.php';

    $consultaSucursal = $conexion->prepare(
        'SELECT id FROM sucursales WHERE id = :id LIMIT 1'
    );
    $consultaSucursal->execute(['id' => $sucursalId]);

    if ($consultaSucursal->fetchColumn() === false) {
        responderJson([
            'success' => false,
            'message' => 'La sucursal seleccionada no es válida.'
        ], 400);
    }

    if ($id === null) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $consulta = $conexion->prepare(
            'INSERT INTO usuarios (nombre, usuario, password_hash, rol_id, sucursal_id, activo)
             VALUES (:nombre, :usuario, :password_hash, 2, :sucursal_id, :activo)'
        );
        $consulta->execute([
            'nombre' => $nombre,
            'usuario' => $usuario,
            'password_hash' => $hash,
            'sucursal_id' => $sucursalId,
            'activo' => (int) $activo
        ]);
    } else {
        $consultaGerente = $conexion->prepare(
            'SELECT id FROM usuarios WHERE id = :id AND rol_id = 2 LIMIT 1'
        );
        $consultaGerente->execute(['id' => $id]);

        if ($consultaGerente->fetchColumn() === false) {
            responderJson([
                'success' => false,
                'message' => 'El gerente no existe.'
            ], 404);
        }

        if ($password === '') {
            $consulta = $conexion->prepare(
                'UPDATE usuarios
                 SET nombre = :nombre,
                     usuario = :usuario,
                     sucursal_id = :sucursal_id,
                     activo = :activo
                 WHERE id = :id
                   AND rol_id = 2'
            );
            $parametros = [
                'nombre' => $nombre,
                'usuario' => $usuario,
                'sucursal_id' => $sucursalId,
                'activo' => (int) $activo,
                'id' => $id
            ];
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $consulta = $conexion->prepare(
                'UPDATE usuarios
                 SET nombre = :nombre,
                     usuario = :usuario,
                     password_hash = :password_hash,
                     sucursal_id = :sucursal_id,
                     activo = :activo
                 WHERE id = :id
                   AND rol_id = 2'
            );
            $parametros = [
                'nombre' => $nombre,
                'usuario' => $usuario,
                'password_hash' => $hash,
                'sucursal_id' => $sucursalId,
                'activo' => (int) $activo,
                'id' => $id
            ];
        }

        $consulta->execute($parametros);
    }

    responderJson([
        'success' => true,
        'message' => 'Gerente guardado correctamente.'
    ]);
} catch (PDOException $e) {
    $codigoMysql = $e->errorInfo[1] ?? null;

    if ($codigoMysql === 1062) {
        responderJson([
            'success' => false,
            'message' => 'Ya existe un usuario con ese nombre de acceso.'
        ], 409);
    }

    responderJson([
        'success' => false,
        'message' => 'No se pudo guardar el gerente.'
    ], 500);
} catch (Throwable $e) {
    responderJson([
        'success' => false,
        'message' => 'No se pudo guardar el gerente.'
    ], 500);
}
