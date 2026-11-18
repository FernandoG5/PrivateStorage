<?php
// Correo para restablecer la contraseña.
// Variables: $nombre, $enlace, $minutosVigencia

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
$enlaceHtml = escaparCorreo($enlace);
$minutos = (int) $minutosVigencia;
$boton = botonCorreo('Restablecer contraseña', $enlace);

$contenido = <<<HTML
<h1 style="margin:0 0 8px;font-family:{$c['fuente']};font-size:24px;line-height:32px;font-weight:700;color:{$c['texto']};">Restablece tu contraseña</h1>
<p style="margin:0 0 16px;color:{$c['secundario']};">Hola, <strong style="color:{$c['texto']};">{$nombreHtml}</strong>.</p>
<p style="margin:0 0 8px;color:{$c['secundario']};">Recibimos una solicitud para restablecer la contraseña de tu cuenta de PrivateStorage. Pulsa el botón para elegir una nueva:</p>

<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:28px 0;">
  <tr><td align="center">{$boton}</td></tr>
</table>

<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#fff8e6" style="background-color:#fff8e6;border-left:4px solid #e0a100;border-radius:8px;">
  <tr>
    <td style="padding:14px 18px;font-family:{$c['fuente']};font-size:14px;line-height:21px;color:#5c4400;">
      El enlace vence en <strong>{$minutos} minutos</strong> y solo puede usarse una vez.
    </td>
  </tr>
</table>

<p style="margin:24px 0 0;font-size:14px;line-height:21px;color:{$c['secundario']};">Si no solicitaste este cambio, ignora este correo: tu contraseña actual seguirá funcionando.</p>

<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="{$c['fondo']}" style="margin-top:20px;background-color:{$c['fondo']};border:1px solid {$c['borde']};border-radius:10px;">
  <tr>
    <td style="padding:12px 16px;font-family:{$c['fuente']};font-size:12px;line-height:18px;color:{$c['secundario']};word-break:break-all;">
      ¿El botón no funciona? Copia y pega este enlace en tu navegador:<br>
      <a href="{$enlaceHtml}" target="_blank" style="color:{$c['principal']};text-decoration:underline;">{$enlaceHtml}</a>
    </td>
  </tr>
</table>
HTML;

$texto = "Hola, {$nombre}.\n\n"
    . "Recibimos una solicitud para restablecer la contraseña de tu cuenta de PrivateStorage.\n"
    . "Abre este enlace para elegir una nueva: {$enlace}\n\n"
    . "El enlace vence en {$minutos} minutos y solo puede usarse una vez.\n"
    . "Si no solicitaste este cambio, ignora este correo: tu contraseña actual seguirá funcionando.\n\n"
    . "Este es un correo automático, por favor no lo respondas.";

return [
    'asunto' => 'Restablece tu contraseña de PrivateStorage',
    'html' => envolverCorreo('Restablece tu contraseña', "Usa este enlace para elegir una nueva contraseña. Vence en {$minutos} minutos.", $contenido),
    'texto' => $texto,
];
