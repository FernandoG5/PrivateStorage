<?php
// Contraseñas temporales para usuarios nuevos y envío del correo de bienvenida.
// La contraseña en texto plano solo vive en memoria mientras se envía el correo:
// nunca se guarda, nunca se registra en logs y nunca se devuelve en la API.

require_once __DIR__ . '/../config/mailer.php';
require_once __DIR__ . '/../plantillas/correo/_base.php';

const HORAS_VIGENCIA_PASSWORD_TEMPORAL = 48;

// Sin caracteres que se confunden al leerlos: 0/O/o, 1/l/I.
const PASSWORD_MAYUSCULAS = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
const PASSWORD_MINUSCULAS = 'abcdefghijkmnpqrstuvwxyz';
const PASSWORD_NUMEROS = '23456789';
const PASSWORD_SIMBOLOS = '!@#$%*?-_';

function caracterAleatorio(string $alfabeto): string
{
    return $alfabeto[random_int(0, strlen($alfabeto) - 1)];
}

function generarPasswordTemporal(int $longitud = 12): string
{
    $longitud = max(10, $longitud);
    $todos = PASSWORD_MAYUSCULAS . PASSWORD_MINUSCULAS . PASSWORD_NUMEROS . PASSWORD_SIMBOLOS;

    // Se garantiza al menos un carácter de cada tipo.
    $caracteres = [
        caracterAleatorio(PASSWORD_MAYUSCULAS),
        caracterAleatorio(PASSWORD_MINUSCULAS),
        caracterAleatorio(PASSWORD_NUMEROS),
        caracterAleatorio(PASSWORD_SIMBOLOS),
    ];
    while (count($caracteres) < $longitud) {
        $caracteres[] = caracterAleatorio($todos);
    }

    // Mezcla Fisher-Yates con random_int (shuffle() no es criptográficamente seguro).
    for ($i = count($caracteres) - 1; $i > 0; $i--) {
        $j = random_int(0, $i);
        [$caracteres[$i], $caracteres[$j]] = [$caracteres[$j], $caracteres[$i]];
    }

    return implode('', $caracteres);
}

// Genera una nueva contraseña temporal para un usuario existente y devuelve el texto plano
// para enviarlo por correo. Solo se guarda el hash.
function asignarPasswordTemporal(PDO $conexion, int $usuarioId): string
{
    $passwordTemporal = generarPasswordTemporal();

    $conexion->prepare(
        'UPDATE usuarios
         SET password_hash = :password_hash,
             debe_cambiar_password = 1,
             password_expira = DATE_ADD(NOW(), INTERVAL ' . HORAS_VIGENCIA_PASSWORD_TEMPORAL . ' HOUR)
         WHERE id = :id'
    )->execute([
        'password_hash' => password_hash($passwordTemporal, PASSWORD_DEFAULT),
        'id' => $usuarioId,
    ]);

    return $passwordTemporal;
}

// Envía el correo de bienvenida. Devuelve false si falló (el detalle queda en logs/correo.log).
// $usuario: ['nombre', 'usuario', 'correo', 'rol_id']
function enviarCorreoBienvenida(array $usuario, string $passwordTemporal): bool
{
    try {
        $urlBase = obtenerUrlAplicacion();
        $correo = renderizarPlantillaCorreo('bienvenida', [
            'nombre' => (string) $usuario['nombre'],
            'usuario' => (string) $usuario['usuario'],
            'passwordTemporal' => $passwordTemporal,
            'rol' => (int) $usuario['rol_id'] === 2 ? 'gerente' : 'cajero',
            'urlInicioSesion' => $urlBase . '/src/inicio_sesion/inicioSesion.html',
            'urlAviso' => $urlBase . '/src/inicio_sesion/aviso_privacidad.html',
            'horasVigencia' => HORAS_VIGENCIA_PASSWORD_TEMPORAL,
        ]);

        enviarCorreo(
            (string) $usuario['correo'],
            (string) $usuario['nombre'],
            $correo['asunto'],
            $correo['html'],
            $correo['texto'],
            imagenesCorreo()
        );

        return true;
    } catch (PHPMailer\PHPMailer\Exception $e) {
        // enviarCorreo() ya registró el ErrorInfo y la respuesta del servidor.
        return false;
    } catch (Throwable $e) {
        registrarErrorCorreo(
            'bienvenida',
            get_class($e) . ': ' . $e->getMessage(),
            (string) ($usuario['correo'] ?? '')
        );
        return false;
    }
}
