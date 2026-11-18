<?php
// Utilidades compartidas por las plantillas de correo.
// Cada plantilla (bienvenida.php, restablecer.php) devuelve ['asunto' => ..., 'html' => ..., 'texto' => ...].

const CORREO_CID_LOGO = 'logo_privatestorage';
const CORREO_RUTA_LOGO = __DIR__ . '/../../recursos/logo_correo.png';

const CORREO_COLOR_PRINCIPAL = '#7040c0';
const CORREO_COLOR_PROFUNDO = '#38136f';
const CORREO_COLOR_TEXTO = '#211a2c';
const CORREO_COLOR_SECUNDARIO = '#70697d';
const CORREO_COLOR_FONDO = '#f4f0fb';
const CORREO_COLOR_BORDE = '#e7e1ef';
const CORREO_FUENTE = "'Segoe UI', Arial, Helvetica, sans-serif";

function escaparCorreo(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

// Carga una plantilla con $datos disponibles como variables.
function renderizarPlantillaCorreo(string $plantilla, array $datos): array
{
    $archivo = __DIR__ . '/' . basename($plantilla) . '.php';
    if (!is_file($archivo)) {
        throw new RuntimeException("No existe la plantilla de correo {$plantilla}.");
    }

    $resultado = (static function (string $__archivo, array $__datos): mixed {
        extract($__datos, EXTR_SKIP);
        return require $__archivo;
    })($archivo, $datos);

    if (!is_array($resultado) || !isset($resultado['asunto'], $resultado['html'], $resultado['texto'])) {
        throw new RuntimeException("La plantilla de correo {$plantilla} está incompleta.");
    }

    return $resultado;
}

// Imágenes que se incrustan con CID para que el logo se vea aunque el cliente bloquee imágenes remotas.
function imagenesCorreo(): array
{
    return [CORREO_CID_LOGO => CORREO_RUTA_LOGO];
}

// Botón compatible con Outlook (VML) y con el resto de clientes.
function botonCorreo(string $texto, string $url): string
{
    $urlHtml = escaparCorreo($url);
    $textoHtml = escaparCorreo($texto);
    $color = CORREO_COLOR_PRINCIPAL;
    $fuente = CORREO_FUENTE;

    return <<<HTML
<table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center" style="margin:0 auto;">
  <tr>
    <td align="center" bgcolor="{$color}" style="border-radius:10px;background-color:{$color};">
      <!--[if mso]>
      <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{$urlHtml}" style="height:48px;v-text-anchor:middle;width:240px;" arcsize="20%" stroke="f" fillcolor="{$color}">
        <w:anchorlock/>
        <center style="color:#ffffff;font-family:Arial,sans-serif;font-size:16px;font-weight:bold;">{$textoHtml}</center>
      </v:roundrect>
      <![endif]-->
      <!--[if !mso]><!-->
      <a href="{$urlHtml}" target="_blank" style="display:inline-block;padding:14px 36px;font-family:{$fuente};font-size:16px;font-weight:700;line-height:20px;color:#ffffff;text-decoration:none;border-radius:10px;background-color:{$color};">{$textoHtml}</a>
      <!--<![endif]-->
    </td>
  </tr>
</table>
HTML;
}

// Estructura común: encabezado morado con logo, tarjeta blanca de 600 px y pie.
function envolverCorreo(string $titulo, string $preencabezado, string $contenido): string
{
    $tituloHtml = escaparCorreo($titulo);
    $preencabezadoHtml = escaparCorreo($preencabezado);
    $cid = CORREO_CID_LOGO;
    $anio = date('Y');
    $c = [
        'principal' => CORREO_COLOR_PRINCIPAL,
        'profundo' => CORREO_COLOR_PROFUNDO,
        'texto' => CORREO_COLOR_TEXTO,
        'secundario' => CORREO_COLOR_SECUNDARIO,
        'fondo' => CORREO_COLOR_FONDO,
        'borde' => CORREO_COLOR_BORDE,
        'fuente' => CORREO_FUENTE,
    ];

    return <<<HTML
<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="color-scheme" content="light">
  <meta name="supported-color-schemes" content="light">
  <title>{$tituloHtml}</title>
  <!--[if mso]>
  <noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
  <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:{$c['fondo']};-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">
  <div style="display:none;max-height:0;overflow:hidden;mso-hide:all;font-size:1px;line-height:1px;color:{$c['fondo']};">{$preencabezadoHtml}</div>
  <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="{$c['fondo']}" style="background-color:{$c['fondo']};">
    <tr>
      <td align="center" style="padding:32px 16px;">
        <!--[if mso]><table role="presentation" width="600" border="0" cellpadding="0" cellspacing="0"><tr><td><![endif]-->
        <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">
          <tr>
            <td align="center" bgcolor="{$c['profundo']}" style="padding:32px 24px 28px;border-radius:16px 16px 0 0;background-color:{$c['profundo']};background-image:linear-gradient(145deg,#6b3cb4 0%,#51218f 48%,#35115f 100%);">
              <img src="cid:{$cid}" width="72" height="72" alt="PrivateStorage" style="display:block;margin:0 auto 12px;border:0;outline:none;width:72px;height:72px;">
              <p style="margin:0;font-family:{$c['fuente']};font-size:22px;font-weight:700;letter-spacing:0.5px;color:#ffffff;">PrivateStorage</p>
            </td>
          </tr>
          <tr>
            <td bgcolor="#ffffff" style="padding:36px 40px 32px;background-color:#ffffff;border-left:1px solid {$c['borde']};border-right:1px solid {$c['borde']};font-family:{$c['fuente']};font-size:16px;line-height:24px;color:{$c['texto']};">
              {$contenido}
            </td>
          </tr>
          <tr>
            <td bgcolor="#ffffff" style="padding:0 40px 28px;background-color:#ffffff;border-left:1px solid {$c['borde']};border-right:1px solid {$c['borde']};border-bottom:1px solid {$c['borde']};border-radius:0 0 16px 16px;">
              <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr><td style="border-top:1px solid {$c['borde']};padding-top:20px;font-family:{$c['fuente']};font-size:12px;line-height:18px;color:{$c['secundario']};text-align:center;">
                  Este es un correo automático, por favor no lo respondas.<br>
                  &copy; {$anio} PrivateStorage &middot; Gestión de ventas e inventario
                </td></tr>
              </table>
            </td>
          </tr>
        </table>
        <!--[if mso]></td></tr></table><![endif]-->
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
}
