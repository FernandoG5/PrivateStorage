<?php

require_once __DIR__ . '/../../config/app.php';

session_start();
header('Content-Type: application/json; charset=utf-8');

function responderJson(array $respuesta, int $codigoHttp = 200): never
{
    http_response_code($codigoHttp);
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}

if (($_SESSION['autenticado'] ?? false) !== true) {
    responderJson([
        'success' => false,
        'message' => 'La sesión ha expirado.',
        'redirect' => privateStorageUrl('/src/inicio_sesion/inicioSesion.html')
    ], 401);
}

if (!in_array((int) ($_SESSION['rol_id'] ?? 0), [1, 2], true)) {
    responderJson(['success' => false, 'message' => 'No tienes permiso para acceder a este módulo.'], 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJson(['success' => false, 'message' => 'Solamente se permite el método POST.'], 405);
}

$idRecibido = $_POST['id'] ?? '';
$sucursalIdRecibido = $_POST['sucursal_id'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$cajeroIdRecibido = $_POST['cajero_id'] ?? '';
$estado = $_POST['estado'] ?? null;

if (
    !is_string($idRecibido)
    || !is_string($sucursalIdRecibido)
    || !is_string($nombre)
    || !is_string($cajeroIdRecibido)
    || !is_string($estado)
) {
    responderJson(['success' => false, 'message' => 'Los datos de la caja no son válidos.'], 400);
}

$idRecibido = trim($idRecibido);
$sucursalIdRecibido = trim($sucursalIdRecibido);
$nombre = trim($nombre);
$cajeroIdRecibido = trim($cajeroIdRecibido);
$estado = trim($estado);

if ($nombre === '' || mb_strlen($nombre) > 80) {
    responderJson(['success' => false, 'message' => 'El nombre de la caja es obligatorio y no debe exceder 80 caracteres.'], 400);
}
$rolId = (int) $_SESSION['rol_id'];
if ($rolId === 2) {
    $sucursalSesion = $_SESSION['sucursal_id'] ?? null;
    if ((!is_int($sucursalSesion) && !ctype_digit((string) $sucursalSesion)) || (int) $sucursalSesion <= 0) {
        responderJson(['success' => false, 'message' => 'La sucursal del gerente no es válida.'], 403);
    }
    $sucursalSesion = (int) $sucursalSesion;
    if ($sucursalIdRecibido !== '' && (!ctype_digit($sucursalIdRecibido) || (int) $sucursalIdRecibido !== $sucursalSesion)) {
        responderJson(['success' => false, 'message' => 'No puedes registrar cajas en otra sucursal.'], 403);
    }
    $sucursalId = $sucursalSesion;
} else {
    if (!ctype_digit($sucursalIdRecibido) || (int) $sucursalIdRecibido <= 0) {
        responderJson(['success' => false, 'message' => 'La sucursal seleccionada no es válida.'], 400);
    }
    $sucursalId = (int) $sucursalIdRecibido;
}
if (!in_array($estado, ['activa', 'inactiva'], true)) {
    responderJson(['success' => false, 'message' => 'El estado de la caja no es válido.'], 400);
}

$id = null;
if ($idRecibido !== '') {
    if (!ctype_digit($idRecibido) || (int) $idRecibido <= 0) {
        responderJson(['success' => false, 'message' => 'El identificador de la caja no es válido.'], 400);
    }
    $id = (int) $idRecibido;
}

$cajeroId = null;
if ($cajeroIdRecibido !== '') {
    if (!ctype_digit($cajeroIdRecibido) || (int) $cajeroIdRecibido <= 0) {
        responderJson(['success' => false, 'message' => 'El cajero seleccionado no es válido.'], 400);
    }
    $cajeroId = (int) $cajeroIdRecibido;
}

try {
    require_once __DIR__ . '/../../config/database.php';

    $consultaSucursal = $conexion->prepare('SELECT id FROM sucursales WHERE id = :id LIMIT 1');
    $consultaSucursal->execute(['id' => $sucursalId]);
    if ($consultaSucursal->fetchColumn() === false) {
        responderJson(['success' => false, 'message' => 'La sucursal seleccionada no existe.'], 404);
    }

    if ($cajeroId !== null) {
        $consultaCajero = $conexion->prepare(
            'SELECT id FROM usuarios
             WHERE id = :id AND rol_id = 3 AND sucursal_id = :sucursal_id AND activo = 1
             LIMIT 1'
        );
        $consultaCajero->execute(['id' => $cajeroId, 'sucursal_id' => $sucursalId]);
        if ($consultaCajero->fetchColumn() === false) {
            $asignacionActual = null;
            if ($id !== null) {
                $consultaAsignacionActual = $conexion->prepare(
                    'SELECT c.id
                     FROM cajas c
                     INNER JOIN usuarios u ON u.id = c.cajero_id
                     WHERE c.id = :caja_id
                       AND c.sucursal_id = :caja_sucursal_id
                       AND c.cajero_id = :cajero_id
                       AND u.rol_id = 3
                       AND u.sucursal_id = :cajero_sucursal_id
                     LIMIT 1'
                );
                $consultaAsignacionActual->execute([
                    'caja_id' => $id,
                    'caja_sucursal_id' => $sucursalId,
                    'cajero_id' => $cajeroId,
                    'cajero_sucursal_id' => $sucursalId
                ]);
                $asignacionActual = $consultaAsignacionActual->fetchColumn();
            }
            if ($asignacionActual === false || $asignacionActual === null) {
                responderJson(['success' => false, 'message' => 'El cajero debe estar activo y pertenecer a la sucursal seleccionada.'], 400);
            }
        }
    }

    if ($id === null) {
        $consulta = $conexion->prepare(
            'INSERT INTO cajas (sucursal_id, nombre, cajero_id, estado)
             VALUES (:sucursal_id, :nombre, :cajero_id, :estado)'
        );
        $consulta->execute([
            'sucursal_id' => $sucursalId,
            'nombre' => $nombre,
            'cajero_id' => $cajeroId,
            'estado' => $estado
        ]);
    } else {
        $consultaCaja = $conexion->prepare(
            'SELECT id FROM cajas WHERE id = :id AND sucursal_id = :sucursal_id LIMIT 1'
        );
        $consultaCaja->execute(['id' => $id, 'sucursal_id' => $sucursalId]);
        if ($consultaCaja->fetchColumn() === false) {
            responderJson(['success' => false, 'message' => 'La caja no existe en esta sucursal.'], 404);
        }

        $consulta = $conexion->prepare(
            'UPDATE cajas
             SET nombre = :nombre, cajero_id = :cajero_id, estado = :estado
             WHERE id = :id AND sucursal_id = :sucursal_id'
        );
        $consulta->execute([
            'nombre' => $nombre,
            'cajero_id' => $cajeroId,
            'estado' => $estado,
            'id' => $id,
            'sucursal_id' => $sucursalId
        ]);
    }

    responderJson(['success' => true, 'message' => 'Caja guardada correctamente.']);
} catch (PDOException $e) {
    if (($e->errorInfo[1] ?? null) === 1062) {
        responderJson(['success' => false, 'message' => 'Ya existe una caja con ese nombre en la sucursal.'], 409);
    }
    responderJson(['success' => false, 'message' => 'No se pudo guardar la caja.'], 500);
} catch (Throwable $e) {
    responderJson(['success' => false, 'message' => 'No se pudo guardar la caja.'], 500);
}
