<?php

require_once __DIR__ . '/../../../config/app.php';

session_start();
header('Content-Type: application/json; charset=utf-8');

function responderJson(array $respuesta, int $codigoHttp = 200): never
{
    http_response_code($codigoHttp);
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}

function obtenerSucursalSesion(): int
{
    if (($_SESSION['autenticado'] ?? false) !== true) {
        responderJson([
            'success' => false,
            'message' => 'La sesión ha expirado.',
            'redirect' => privateStorageUrl('/src/inicio_sesion/inicioSesion.html')
        ], 401);
    }
    if ((int) ($_SESSION['rol_id'] ?? 0) !== 2) {
        responderJson(['success' => false, 'message' => 'No tienes permiso para acceder a este módulo.'], 403);
    }
    $sucursalId = $_SESSION['sucursal_id'] ?? null;
    if ((!is_int($sucursalId) && !ctype_digit((string) $sucursalId)) || (int) $sucursalId <= 0) {
        responderJson(['success' => false, 'message' => 'La sucursal del gerente no es válida.'], 403);
    }
    return (int) $sucursalId;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJson(['success' => false, 'message' => 'Solamente se permite el método POST.'], 405);
}

$sucursalId = obtenerSucursalSesion();
$idRecibido = $_POST['id'] ?? '';
$nombre = $_POST['nombre'] ?? null;
$usuario = $_POST['usuario'] ?? null;
$correo = $_POST['correo'] ?? null;
$passwordUsuario = $_POST['password'] ?? ''; // Solo se usa al editar; al crear se genera una temporal.
$activo = $_POST['activo'] ?? null;

if (!is_string($idRecibido) || !is_string($nombre) || !is_string($usuario) || !is_string($correo) || !is_string($passwordUsuario) || !is_string($activo)) {
    responderJson(['success' => false, 'message' => 'Los datos del cajero no son válidos.'], 400);
}

$idRecibido = trim($idRecibido);
$nombre = trim($nombre);
$usuario = trim($usuario);
$correo = mb_strtolower(trim($correo));

if ($nombre === '' || $usuario === '') {
    responderJson(['success' => false, 'message' => 'Nombre y usuario son obligatorios.'], 400);
}
if ($correo === '' || mb_strlen($correo) > 150 || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    responderJson(['success' => false, 'message' => 'El correo electrónico no es válido.'], 400);
}

if (!in_array($activo, ['0', '1'], true)) {
    responderJson(['success' => false, 'message' => 'El estado del cajero no es válido.'], 400);
}

$id = null;
if ($idRecibido !== '') {
    if (!ctype_digit($idRecibido) || (int) $idRecibido <= 0) {
        responderJson(['success' => false, 'message' => 'El identificador del cajero no es válido.'], 400);
    }
    $id = (int) $idRecibido;
}

try {
    require_once __DIR__ . '/../../../config/database.php';

    if ($id === null) {
        // Al crear, la contraseña la genera el sistema y se envía por correo.
        require_once __DIR__ . '/../../../auth/acceso_temporal.php';

        $passwordTemporal = generarPasswordTemporal();
        $consulta = $conexion->prepare(
            'INSERT INTO usuarios
                (nombre, usuario, correo, password_hash, rol_id, sucursal_id, activo,
                 debe_cambiar_password, password_expira)
             VALUES
                (:nombre, :usuario, :correo, :password_hash, 3, :sucursal_id, :activo,
                 1, DATE_ADD(NOW(), INTERVAL ' . HORAS_VIGENCIA_PASSWORD_TEMPORAL . ' HOUR))'
        );
        $consulta->execute([
            'nombre' => $nombre,
            'usuario' => $usuario,
            'correo' => $correo,
            'password_hash' => password_hash($passwordTemporal, PASSWORD_DEFAULT),
            'sucursal_id' => $sucursalId,
            'activo' => (int) $activo
        ]);
        $nuevoId = (int) $conexion->lastInsertId();

        $correoEnviado = enviarCorreoBienvenida([
            'nombre' => $nombre,
            'usuario' => $usuario,
            'correo' => $correo,
            'rol_id' => 3
        ], $passwordTemporal);
        unset($passwordTemporal);

        responderJson([
            'success' => true,
            'id' => $nuevoId,
            'correo_enviado' => $correoEnviado,
            'message' => $correoEnviado
                ? 'Cajero creado. Le enviamos su contraseña temporal por correo.'
                : 'Cajero creado, pero no se pudo enviar el correo con su contraseña temporal.'
        ]);
    }

    $consultaPropiedad = $conexion->prepare(
        'SELECT id FROM usuarios
         WHERE id = :id AND rol_id = 3 AND sucursal_id = :sucursal_id
         LIMIT 1'
    );
    $consultaPropiedad->execute(['id' => $id, 'sucursal_id' => $sucursalId]);
    if ($consultaPropiedad->fetchColumn() === false) {
        responderJson(['success' => false, 'message' => 'No se encontró el cajero.'], 404);
    }

    if ($passwordUsuario === '') {
        $consulta = $conexion->prepare(
            'UPDATE usuarios
             SET nombre = :nombre,
                 usuario = :usuario,
                 correo = :correo,
                 activo = :activo
             WHERE id = :id AND rol_id = 3 AND sucursal_id = :sucursal_id'
        );
        $parametros = [
            'nombre' => $nombre,
            'usuario' => $usuario,
            'correo' => $correo,
            'activo' => (int) $activo,
            'id' => $id,
            'sucursal_id' => $sucursalId
        ];
    } else {
        $passwordHash = password_hash($passwordUsuario, PASSWORD_DEFAULT);
        $consulta = $conexion->prepare(
            'UPDATE usuarios
             SET nombre = :nombre,
                 usuario = :usuario,
                 correo = :correo,
                 password_hash = :password_hash,
                 activo = :activo
             WHERE id = :id AND rol_id = 3 AND sucursal_id = :sucursal_id'
        );
        $parametros = [
            'nombre' => $nombre,
            'usuario' => $usuario,
            'correo' => $correo,
            'password_hash' => $passwordHash,
            'activo' => (int) $activo,
            'id' => $id,
            'sucursal_id' => $sucursalId
        ];
    }

    $consulta->execute($parametros);
    responderJson(['success' => true, 'message' => 'Cajero guardado correctamente.']);
} catch (PDOException $e) {
    if (($e->errorInfo[1] ?? null) === 1062) {
        if (str_contains($e->getMessage(), 'uq_usuarios_correo')) {
            responderJson(['success' => false, 'message' => 'Ya existe un usuario con ese correo electrónico.'], 409);
        }
        responderJson(['success' => false, 'message' => 'Ya existe un usuario con ese nombre de acceso.'], 409);
    }
    responderJson(['success' => false, 'message' => 'No se pudo guardar el cajero.'], 500);
} catch (Throwable $e) {
    responderJson(['success' => false, 'message' => 'No se pudo guardar el cajero.'], 500);
}
