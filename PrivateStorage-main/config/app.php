<?php

ini_set('display_errors', '0');
ini_set('log_errors', '1');

function privateStorageUrl(string $path): string
{
    $projectRoot = realpath(dirname(__DIR__));
    $scriptFilename = realpath($_SERVER['SCRIPT_FILENAME'] ?? '');
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

    if ($projectRoot === false || $scriptFilename === false || $scriptName === '') {
        throw new RuntimeException('No se pudo determinar la ruta pública de la aplicación.');
    }

    $projectRoot = rtrim(str_replace('\\', '/', $projectRoot), '/');
    $scriptFilename = str_replace('\\', '/', $scriptFilename);
    $relativeScript = substr($scriptFilename, strlen($projectRoot));

    if ($relativeScript === false
        || $relativeScript === ''
        || $relativeScript === $scriptFilename
        || $relativeScript[0] !== '/'
    ) {
        throw new RuntimeException('El script solicitado no pertenece a la aplicación.');
    }

    $suffix = $relativeScript;
    if (substr($scriptName, -strlen($suffix)) !== $suffix) {
        throw new RuntimeException('No se pudo determinar la ruta pública de la aplicación.');
    }

    $basePath = substr($scriptName, 0, -strlen($suffix));

    return rtrim($basePath, '/') . '/' . ltrim($path, '/');
}
