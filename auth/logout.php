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

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $parametrosCookie = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $parametrosCookie['path'],
        $parametrosCookie['domain'],
        $parametrosCookie['secure'],
        $parametrosCookie['httponly']
    );
}

session_destroy();

responderJson([
    'success' => true,
    'redirect' => '/PrivateStorage-main/src/inicio_sesion/inicioSesion.html'
]);