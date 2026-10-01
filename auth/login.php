<?php

session_start();

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

$usuario = $_POST['usuario'] ?? null;
$password = $_POST['password'] ?? null;

if (!is_string($usuario) || !is_string($password) || trim($usuario) === '' || $password === '') {
	responderJson([
		'success' => false,
		'message' => 'El usuario y la contraseña son obligatorios.'
	], 400);
}

try {
	require_once __DIR__ . '/../config/database.php';

	$consulta = $conexion->prepare(
		'SELECT id, nombre, usuario, password_hash, rol_id, sucursal_id, activo
		 FROM usuarios
		 WHERE usuario = :usuario
		 LIMIT 1'
	);
	$consulta->execute(['usuario' => $usuario]);
	$usuarioEncontrado = $consulta->fetch();

	if ($usuarioEncontrado === false) {
		responderJson([
			'success' => false,
			'message' => 'Usuario o contraseña incorrectos.'
		], 401);
	}

	if ((int) $usuarioEncontrado['activo'] !== 1) {
		responderJson([
			'success' => false,
			'message' => 'La cuenta está desactivada.'
		], 403);
	}

	$passwordCorrecta = password_verify($password, $usuarioEncontrado['password_hash']);

	if (!$passwordCorrecta) {
		responderJson([
			'success' => false,
			'message' => 'Usuario o contraseña incorrectos.'
		], 401);
	}

	$redirecciones = [
		1 => '/PrivateStorage-main/src/admin/admin.html',
		2 => '/PrivateStorage-main/src/gerente/gerente.html',
		3 => '/PrivateStorage-main/src/cajero/cajero.html'
	];
	$rolId = (int) $usuarioEncontrado['rol_id'];

	if (!isset($redirecciones[$rolId])) {
		responderJson([
			'success' => false,
			'message' => 'No se pudo completar la autenticación.'
		], 500);
	}

	session_regenerate_id(true);
	$_SESSION = [
		'user_id' => (int) $usuarioEncontrado['id'],
		'nombre' => $usuarioEncontrado['nombre'],
		'usuario' => $usuarioEncontrado['usuario'],
		'rol_id' => $rolId,
		'sucursal_id' => $usuarioEncontrado['sucursal_id'] === null
			? null
			: (int) $usuarioEncontrado['sucursal_id'],
		'autenticado' => true
	];

	responderJson([
		'success' => true,
		'redirect' => $redirecciones[$rolId]
	]);
} catch (Throwable $e) {
	responderJson([
		'success' => false,
		'message' => 'Ocurrió un error interno del servidor.'
	], 500);
}
