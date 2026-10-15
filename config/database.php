<?php

ini_set('display_errors', '0');
ini_set('log_errors', '1');

$configFile = __DIR__ . '/config.php';

if (!is_file($configFile)) {
    throw new RuntimeException(
        'Falta config/config.php. Cópialo desde config/config.example.php y configura tus credenciales.'
    );
}

$config = require $configFile;
$valoresTexto = ['host', 'db', 'user', 'pass'];
if (!is_array($config)) {
    throw new RuntimeException('La configuración de la base de datos está incompleta.');
}

$clavesRequeridas = ['host', 'db', 'user', 'pass', 'port'];
foreach ($clavesRequeridas as $clave) {
    if (!array_key_exists($clave, $config)) {
        throw new RuntimeException('La configuración de la base de datos está incompleta.');
    }
}
foreach ($valoresTexto as $clave) {
    if (!is_string($config[$clave]) || $config[$clave] === '') {
        throw new RuntimeException('La configuración de la base de datos está incompleta.');
    }
}
if ((!is_int($config['port']) && !ctype_digit((string) $config['port']))
    || (int) $config['port'] < 1
    || (int) $config['port'] > 65535
) {
    throw new RuntimeException('El puerto de la base de datos no es válido.');
}

// Let the calling endpoint handle the error so it can return its normal response format.
$conexion = new PDO(
    sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $config['host'],
        $config['port'],
        $config['db']
    ),
    $config['user'],
    $config['pass'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);