<?php
// Pantalla de cambio obligatorio de contraseña en el primer inicio de sesión.
// Solo se puede abrir después de iniciar sesión con una contraseña temporal vigente.

ini_set('display_errors', '0');
session_start();

$pendiente = $_SESSION['cambio_password_pendiente'] ?? null;
if (!is_array($pendiente) || !isset($pendiente['user_id']) || !is_string($_SESSION['csrf_cambio_password'] ?? null)) {
    header('Location: inicioSesion.html', true, 303);
    exit;
}

header('Cache-Control: no-store');
$csrf = htmlspecialchars($_SESSION['csrf_cambio_password'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="no-referrer">
    <title>Crea tu contraseña</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="inicioSesion.css">
    <style>
        .aceptar-terminos {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin: 4px 0 2px;
            color: var(--texto-secundario);
            font-size: 14px;
            line-height: 1.5;
            cursor: pointer;
        }

        .aceptar-terminos input {
            flex: 0 0 auto;
            width: 18px;
            height: 18px;
            margin-top: 2px;
            accent-color: var(--morado-principal);
            cursor: pointer;
        }

        .aceptar-terminos a {
            color: var(--morado-principal);
            font-weight: 600;
        }

        :root[data-tema="oscuro"] .aceptar-terminos a {
            color: var(--violeta);
        }
    </style>
</head>

<body>
    <main class="login-page">
        <section class="login-card" aria-label="Crear contraseña">
            <button
                class="theme-toggle"
                id="boton-tema"
                type="button"
                aria-label="Activar modo oscuro"
                aria-pressed="false"
            >
                <span class="theme-toggle-icon" aria-hidden="true">◐</span>
                <span class="theme-toggle-text">Tema oscuro</span>
            </button>

            <div class="login-layout">
                <div class="brand-panel">
                    <div class="brand-panel-orb brand-panel-orb-one" aria-hidden="true"></div>
                    <div class="brand-panel-orb brand-panel-orb-two" aria-hidden="true"></div>
                    <div class="brand-panel-content">
                        <img class="login-logo" src="../../recursos/LogoBlancoPS.png" alt="PrivateStorage">
                        <p class="brand-kicker">Sistema de gestión</p>
                        <h1>PrivateStorage</h1>
                        <p class="brand-description">Por tu seguridad, reemplaza la contraseña temporal por una que solo tú conozcas.</p>
                    </div>
                    <div class="brand-panel-line" aria-hidden="true">
                        <span></span><span></span><span></span>
                    </div>
                </div>

                <div class="form-panel">
                    <div class="form-heading">
                        <p class="form-kicker">Primer inicio de sesión</p>
                        <h2>Crea tu contraseña</h2>
                        <p>Debe tener al menos 8 caracteres y ser distinta a la temporal.</p>
                    </div>

                    <form class="login-form" id="formulario-cambiar-password" novalidate>
                        <input type="hidden" id="csrf" value="<?= $csrf ?>">

                        <div class="form-group">
                            <label class="form-label" for="password">Nueva contraseña</label>
                            <div class="input-shell">
                                <svg class="input-icon" viewBox="0 0 24 24" aria-hidden="true">
                                    <rect x="4" y="10" width="16" height="11" rx="2"></rect>
                                    <path d="M8 10V7a4 4 0 1 1 8 0v3m-4 5v2"></path>
                                </svg>
                                <input
                                    class="form-input"
                                    type="password"
                                    id="password"
                                    name="password"
                                    minlength="8"
                                    placeholder="Escribe tu nueva contraseña"
                                    autocomplete="new-password"
                                >
                                <button
                                    class="password-toggle"
                                    type="button"
                                    aria-label="Mostrar contraseña"
                                    aria-controls="password"
                                    aria-pressed="false"
                                >
                                    <svg class="eye-icon" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-off-icon" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"></path>
                                        <path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a15.7 15.7 0 0 1-3.1 3.8M6.2 6.2C3.5 8 2 12 2 12s3.5 7 10 7a10.6 10.6 0 0 0 4.1-.8"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password-confirmacion">Confirmar contraseña</label>
                            <div class="input-shell">
                                <svg class="input-icon" viewBox="0 0 24 24" aria-hidden="true">
                                    <rect x="4" y="10" width="16" height="11" rx="2"></rect>
                                    <path d="M8 10V7a4 4 0 1 1 8 0v3m-4 5v2"></path>
                                </svg>
                                <input
                                    class="form-input"
                                    type="password"
                                    id="password-confirmacion"
                                    name="password_confirmacion"
                                    minlength="8"
                                    placeholder="Repite tu nueva contraseña"
                                    autocomplete="new-password"
                                >
                                <button
                                    class="password-toggle"
                                    type="button"
                                    aria-label="Mostrar contraseña"
                                    aria-controls="password-confirmacion"
                                    aria-pressed="false"
                                >
                                    <svg class="eye-icon" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-off-icon" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"></path>
                                        <path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a15.7 15.7 0 0 1-3.1 3.8M6.2 6.2C3.5 8 2 12 2 12s3.5 7 10 7a10.6 10.6 0 0 0 4.1-.8"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <label class="aceptar-terminos" for="acepta-terminos">
                            <input type="checkbox" id="acepta-terminos" name="acepta_terminos" value="1">
                            <span>He leído y acepto los <a href="aviso_privacidad.html" target="_blank" rel="noopener">términos de uso y el aviso de privacidad</a>.</span>
                        </label>

                        <p class="login-message" role="alert" aria-live="polite"></p>

                        <button class="login-button" type="submit">Guardar y entrar</button>

                        <a class="form-link form-link-centrado" href="inicioSesion.html">Volver al inicio de sesión</a>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <script>
        const botonTema = document.querySelector('#boton-tema');
        let temaGuardado = null;
        try {
            temaGuardado = localStorage.getItem('private-storage-tema');
        } catch (error) {
            temaGuardado = null;
        }

        const establecerTema = temaOscuro => {
            document.documentElement.dataset.tema = temaOscuro ? 'oscuro' : '';
            botonTema.setAttribute('aria-pressed', String(temaOscuro));
            botonTema.setAttribute('aria-label', temaOscuro ? 'Activar modo claro' : 'Activar modo oscuro');
            botonTema.querySelector('.theme-toggle-text').textContent = temaOscuro ? 'Tema claro' : 'Tema oscuro';
        };

        establecerTema(temaGuardado === 'oscuro');

        botonTema.addEventListener('click', () => {
            const temaOscuro = document.documentElement.dataset.tema !== 'oscuro';
            establecerTema(temaOscuro);
            try {
                localStorage.setItem('private-storage-tema', temaOscuro ? 'oscuro' : 'claro');
            } catch (error) {
                // Sin almacenamiento disponible: el tema solo dura en esta página.
            }
        });

        document.querySelectorAll('.password-toggle').forEach(boton => {
            const campo = document.getElementById(boton.getAttribute('aria-controls'));

            boton.addEventListener('click', () => {
                const mostrarPassword = campo.type === 'password';
                campo.type = mostrarPassword ? 'text' : 'password';
                boton.setAttribute('aria-pressed', String(mostrarPassword));
                boton.setAttribute('aria-label', mostrarPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
            });
        });

        const formulario = document.querySelector('#formulario-cambiar-password');
        const campoPassword = document.querySelector('#password');
        const campoConfirmacion = document.querySelector('#password-confirmacion');
        const casillaTerminos = document.querySelector('#acepta-terminos');
        const botonGuardar = document.querySelector('.login-button');
        const mensaje = document.querySelector('.login-message');

        const mostrarMensaje = (texto, exito = false) => {
            mensaje.textContent = texto;
            mensaje.classList.toggle('es-exito', exito);
        };

        formulario.addEventListener('submit', async evento => {
            evento.preventDefault();

            const password = campoPassword.value;
            const passwordConfirmacion = campoConfirmacion.value;

            if (password.length < 8) {
                mostrarMensaje('La contraseña debe tener al menos 8 caracteres.');
                return;
            }
            if (password !== passwordConfirmacion) {
                mostrarMensaje('Las contraseñas no coinciden.');
                return;
            }
            if (!casillaTerminos.checked) {
                mostrarMensaje('Debes aceptar los términos de uso y el aviso de privacidad para continuar.');
                return;
            }

            botonGuardar.disabled = true;
            mostrarMensaje('');
            let redirigiendo = false;

            try {
                const respuesta = await fetch('../../auth/guardar_password_nueva.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        csrf: document.querySelector('#csrf').value,
                        password,
                        password_confirmacion: passwordConfirmacion,
                        acepta_terminos: '1'
                    })
                });
                const datos = await respuesta.json();

                if (datos.success === true) {
                    redirigiendo = true;
                    formulario.reset();
                    mostrarMensaje(`${datos.message || 'Contraseña guardada.'} Entrando...`, true);
                    window.setTimeout(() => {
                        window.location.href = datos.redirect || 'inicioSesion.html';
                    }, 1200);
                    return;
                }

                mostrarMensaje(datos.message || 'No se pudo guardar la contraseña.');
                if (datos.redirect) {
                    redirigiendo = true;
                    window.setTimeout(() => {
                        window.location.href = datos.redirect;
                    }, 3000);
                }
            } catch (error) {
                console.error('No se pudo guardar la contraseña:', error);
                mostrarMensaje('No se pudo conectar con el servidor. Intenta nuevamente.');
            } finally {
                if (!redirigiendo) {
                    botonGuardar.disabled = false;
                }
            }
        });
    </script>
</body>

</html>
