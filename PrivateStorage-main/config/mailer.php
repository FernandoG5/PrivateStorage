<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';

function obtenerConfiguracionApp(): array
{
    $configFile = __DIR__ . '/config.php';

    if (!is_file($configFile)) {
        throw new RuntimeException(
            'Falta config/config.php. Cópialo desde config/config.example.php y configura tus credenciales.'
        );
    }

    $config = require $configFile;
    if (!is_array($config)) {
        throw new RuntimeException('La configuración de la aplicación está incompleta.');
    }

    return $config;
}

function obtenerUrlAplicacion(): string
{
    $appUrl = obtenerConfiguracionApp()['app_url'] ?? null;

    if (!is_string($appUrl) || filter_var($appUrl, FILTER_VALIDATE_URL) === false) {
        throw new RuntimeException('Falta app_url en config/config.php.');
    }

    return rtrim($appUrl, '/');
}

// Lanza PHPMailer\PHPMailer\Exception si el correo no se pudo enviar.
function enviarCorreo(string $para, string $nombre, string $asunto, string $html, string $texto): void
{
    $mail = obtenerConfiguracionApp()['mail'] ?? null;
    $clavesRequeridas = ['host', 'port', 'user', 'pass', 'from', 'from_name'];

    if (!is_array($mail)) {
        throw new RuntimeException('Falta la configuración de correo en config/config.php.');
    }
    foreach ($clavesRequeridas as $clave) {
        if (!isset($mail[$clave]) || $mail[$clave] === '') {
            throw new RuntimeException('La configuración de correo está incompleta.');
        }
    }

    $mailer = new PHPMailer(true);
    $mailer->isSMTP();
    $mailer->Host = $mail['host'];
    $mailer->Port = (int) $mail['port'];
    $mailer->SMTPAuth = true;
    $mailer->Username = $mail['user'];
    $mailer->Password = $mail['pass'];
    $mailer->SMTPSecure = (int) $mail['port'] === 465
        ? PHPMailer::ENCRYPTION_SMTPS
        : PHPMailer::ENCRYPTION_STARTTLS;
    $mailer->CharSet = PHPMailer::CHARSET_UTF8;
    $mailer->Timeout = 15;

    $mailer->setFrom($mail['from'], $mail['from_name']);
    $mailer->addAddress($para, $nombre);
    $mailer->isHTML(true);
    $mailer->Subject = $asunto;
    $mailer->Body = $html;
    $mailer->AltBody = $texto;

    $mailer->send();
}
