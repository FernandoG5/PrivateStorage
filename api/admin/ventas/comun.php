<?php

ob_start();
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);
require_once __DIR__ . '/../../../config/app.php';

function responderJsonVentas(array $respuesta, int $codigoHttp = 200): never
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    http_response_code($codigoHttp);
    header('Content-Type: application/json; charset=utf-8');

    $json = json_encode(
        $respuesta,
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );
    echo $json === false
        ? '{"success":false,"message":"Ocurrió un error interno del servidor."}'
        : $json;
    exit;
}

function prepararApiVentas(): void
{
    set_error_handler(
        static function (int $severity, string $message, string $file, int $line): bool {
            if (!(error_reporting() & $severity)) {
                return false;
            }

            throw new ErrorException($message, 0, $severity, $file, $line);
        }
    );

    register_shutdown_function(
        static function (): void {
            $error = error_get_last();
            $tiposFatales = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];

            if ($error === null || !in_array($error['type'], $tiposFatales, true)) {
                return;
            }

            error_log(sprintf(
                'Error fatal en API de ventas: %s en %s:%d',
                $error['message'],
                $error['file'],
                $error['line']
            ));
            responderJsonVentas([
                'success' => false,
                'message' => 'No se pudieron cargar las ventas.'
            ], 500);
        }
    );
}

function validarAdministradorVentas(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE && !session_start()) {
        throw new RuntimeException('No se pudo iniciar la sesión.');
    }

    if (($_SESSION['autenticado'] ?? false) !== true) {
        responderJsonVentas([
            'success' => false,
            'message' => 'La sesión ha expirado.',
            'redirect' => privateStorageUrl('/src/inicio_sesion/inicioSesion.html')
        ], 401);
    }

    if ((int) ($_SESSION['rol_id'] ?? 0) !== 1) {
        responderJsonVentas([
            'success' => false,
            'message' => 'No tienes permiso para acceder a este módulo.'
        ], 403);
    }
}

function valorFiltroVentas(string $nombre): string
{
    $valor = $_GET[$nombre] ?? '';
    if (!is_string($valor)) {
        responderJsonVentas([
            'success' => false,
            'message' => 'Los filtros recibidos no son válidos.'
        ], 400);
    }

    return trim($valor);
}

function validarFechaFiltroVentas(string $fecha): bool
{
    if ($fecha === '') {
        return true;
    }

    $valor = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);
    $errores = DateTimeImmutable::getLastErrors();

    return $valor !== false
        && $valor->format('Y-m-d') === $fecha
        && ($errores === false || ($errores['warning_count'] === 0 && $errores['error_count'] === 0));
}

function obtenerFiltrosVentas(): array
{
    $desde = valorFiltroVentas('desde');
    $hasta = valorFiltroVentas('hasta');
    $sucursal = valorFiltroVentas('sucursal');
    $busqueda = valorFiltroVentas('busqueda');

    if (
        !validarFechaFiltroVentas($desde)
        || !validarFechaFiltroVentas($hasta)
        || ($desde !== '' && $hasta !== '' && $desde > $hasta)
    ) {
        responderJsonVentas([
            'success' => false,
            'message' => 'El rango de fechas no es válido.'
        ], 400);
    }

    if ($sucursal !== '' && (!ctype_digit($sucursal) || (int) $sucursal < 1)) {
        responderJsonVentas([
            'success' => false,
            'message' => 'La sucursal seleccionada no es válida.'
        ], 400);
    }

    if (mb_strlen($busqueda) > 100) {
        responderJsonVentas([
            'success' => false,
            'message' => 'La búsqueda no puede exceder 100 caracteres.'
        ], 400);
    }

    return [
        'desde' => $desde,
        'hasta' => $hasta,
        'sucursal' => $sucursal === '' ? null : (int) $sucursal,
        'busqueda' => $busqueda
    ];
}

function condicionesVentas(array $filtros): array
{
    $condiciones = [];
    $parametros = [];

    if ($filtros['desde'] !== '') {
        $condiciones[] = 'v.fecha_venta >= :fecha_desde';
        $parametros['fecha_desde'] = $filtros['desde'] . ' 00:00:00';
    }

    if ($filtros['hasta'] !== '') {
        $hastaExclusivo = (new DateTimeImmutable($filtros['hasta']))
            ->modify('+1 day')
            ->format('Y-m-d 00:00:00');
        $condiciones[] = 'v.fecha_venta < :fecha_hasta';
        $parametros['fecha_hasta'] = $hastaExclusivo;
    }

    if ($filtros['sucursal'] !== null) {
        $condiciones[] = 'v.sucursal_id = :sucursal_id';
        $parametros['sucursal_id'] = $filtros['sucursal'];
    }

    if ($filtros['busqueda'] !== '') {
        $condiciones[] = '(v.folio LIKE :busqueda_folio
            OR u.nombre LIKE :busqueda_nombre
            OR u.usuario LIKE :busqueda_usuario)';
        $busqueda = '%' . $filtros['busqueda'] . '%';
        $parametros['busqueda_folio'] = $busqueda;
        $parametros['busqueda_nombre'] = $busqueda;
        $parametros['busqueda_usuario'] = $busqueda;
    }

    return [
        $condiciones === [] ? '' : ' WHERE ' . implode(' AND ', $condiciones),
        $parametros
    ];
}

function ejecutarConsultaVentas(PDO $conexion, string $sql, array $parametros): PDOStatement
{
    $consulta = $conexion->prepare($sql);
    foreach ($parametros as $nombre => $valor) {
        $tipo = is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR;
        $consulta->bindValue(':' . $nombre, $valor, $tipo);
    }
    $consulta->execute();

    return $consulta;
}
