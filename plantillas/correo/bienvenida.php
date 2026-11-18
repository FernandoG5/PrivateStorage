<?php
// Correo de bienvenida con contraseña temporal.
// Variables: $nombre, $usuario (No. de trabajador), $passwordTemporal, $rol, $urlInicioSesion,
//            $urlAviso, $horasVigencia

require_once __DIR__ . '/_base.php';

$c = [
    'principal' => CORREO_COLOR_PRINCIPAL,
    'texto' => CORREO_COLOR_TEXTO,
    'secundario' => CORREO_COLOR_SECUNDARIO,
    'fondo' => CORREO_COLOR_FONDO,
    'borde' => CORREO_COLOR_BORDE,
    'fuente' => CORREO_FUENTE,
];

$nombreHtml = escaparCorreo($nombre);
$usuarioHtml = escaparCorreo($usuario);
$passwordHtml = escaparCorreo($passwordTemporal);
$rolHtml = escaparCorreo($rol);
$urlAvisoHtml = escaparCorreo($urlAviso);
$horas = (int) $horasVigencia;
$boton = botonCorreo('Iniciar sesión', $urlInicioSesion);

$contenido = <<<HTML
<h1 style="margin:0 0 8px;font-family:{$c['fuente']};font-size:24px;line-height:32px;font-weight:700;color:{$c['texto']};">¡Te damos la bienvenida, {$nombreHtml}!</h1>
<p style="margin:0 0 24px;color:{$c['secundario']};">Se creó tu cuenta de <strong style="color:{$c['texto']};">{$rolHtml}</strong> en PrivateStorage. Estos son tus datos para iniciar sesión:</p>

<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="{$c['fondo']}" style="background-color:{$c['fondo']};border:1px solid {$c['borde']};border-radius:12px;">
  <tr>
    <td style="padding:20px 24px 8px;font-family:{$c['fuente']};">
      <p style="margin:0;font-size:12px;line-height:16px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:{$c['secundario']};">No. de trabajador</p>
      <p style="margin:4px 0 0;font-family:Consolas,'Courier New',monospace;font-size:22px;line-height:30px;font-weight:700;letter-spacing:2px;color:{$c['texto']};">{$usuarioHtml}</p>
    </td>
  </tr>
  <tr>
    <td style="padding:12px 24px 20px;font-family:{$c['fuente']};">
      <p style="margin:0;font-size:12px;line-height:16px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:{$c['secundario']};">Contraseña temporal</p>
      <p style="margin:4px 0 0;font-family:Consolas,'Courier New',monospace;font-size:22px;line-height:30px;font-weight:700;letter-spacing:2px;color:{$c['principal']};">{$passwordHtml}</p>
    </td>
  </tr>
</table>

<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:28px 0;">
  <tr><td align="center">{$boton}</td></tr>
</table>

<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#fff8e6" style="background-color:#fff8e6;border-left:4px solid #e0a100;border-radius:8px;">
  <tr>
    <td style="padding:14px 18px;font-family:{$c['fuente']};font-size:14px;line-height:21px;color:#5c4400;">
      <strong>Importante:</strong> por tu seguridad, al iniciar sesión por primera vez se te pedirá cambiar esta contraseña por una propia. La contraseña temporal vence en <strong>{$horas} horas</strong>.
    </td>
  </tr>
</table>

<p style="margin:24px 0 0;font-size:14px;line-height:21px;color:{$c['secundario']};">No compartas tus datos de acceso con nadie. Si no esperabas este correo, avisa al administrador de tu sucursal.</p>

<p style="margin:16px 0 0;font-size:12px;line-height:18px;color:{$c['secundario']};">
  <strong style="color:{$c['texto']};">Términos de uso y aviso de privacidad.</strong>
  PrivateStorage usa tu nombre, número de trabajador y correo electrónico únicamente para darte acceso al sistema y para las actividades de tu puesto.
  Al iniciar sesión deberás aceptar los términos y el aviso de privacidad, que puedes consultar completos en
  <a href="{$urlAvisoHtml}" target="_blank" style="color:{$c['principal']};text-decoration:underline;">este enlace</a>.
</p>
HTML;

$texto = "¡Te damos la bienvenida, {$nombre}!\n\n"
    . "Se creó tu cuenta de {$rol} en PrivateStorage. Estos son tus datos para iniciar sesión:\n\n"
    . "No. de trabajador: {$usuario}\n"
    . "Contraseña temporal: {$passwordTemporal}\n\n"
    . "Inicia sesión aquí: {$urlInicioSesion}\n\n"
    . "IMPORTANTE: al iniciar sesión por primera vez se te pedirá cambiar esta contraseña por una propia. "
    . "La contraseña temporal vence en {$horas} horas.\n\n"
    . "No compartas tus datos de acceso con nadie. Si no esperabas este correo, avisa al administrador de tu sucursal.\n\n"
    . "Términos de uso y aviso de privacidad: PrivateStorage usa tu nombre, número de trabajador y correo electrónico "
    . "únicamente para darte acceso al sistema y para las actividades de tu puesto. Consúltalos completos en: {$urlAviso}\n\n"
    . "Este es un correo automático, por favor no lo respondas.";

return [
    'asunto' => 'Tu acceso a PrivateStorage',
    'html' => envolverCorreo('Tu acceso a PrivateStorage', "Tu número de trabajador y tu contraseña temporal para entrar a PrivateStorage.", $contenido),
    'texto' => $texto,
];
