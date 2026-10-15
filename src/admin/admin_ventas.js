(() => {
    const tablaVentas = document.getElementById('tabla-ventas-admin');
    const fechaDesde = document.getElementById('ventas-fecha-desde');
    const fechaHasta = document.getElementById('ventas-fecha-hasta');
    const sucursal = document.getElementById('ventas-filtro-sucursal');
    const busqueda = document.getElementById('ventas-busqueda');
    const selectorPorPagina = document.getElementById('ventas-por-pagina');
    const resumenPaginacion = document.getElementById('ventas-paginacion-resumen');
    const controlesPaginacion = document.getElementById('ventas-paginacion-controles');
    const errorResumen = document.getElementById('ventas-error-resumen');
    const modal = document.getElementById('modal-detalle-venta');
    const dialogo = modal.querySelector('[role="dialog"]');
    const contenidoDetalle = document.getElementById('detalle-venta-contenido');
    const clavePorPagina = 'privateStorage.ventas.porPagina';
    const opcionesPorPagina = [5, 10, 20, 30];
    const moneda = new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    });
    let paginaActual = 1;
    let ventasPorPagina = leerVentasPorPagina();
    let solicitudActual = 0;
    let temporizadorBusqueda;
    let elementoEnfoqueAnterior = null;

    selectorPorPagina.value = String(ventasPorPagina);

    const escaparHtml = valor => String(valor ?? '').replace(/[&<>"']/g, caracter => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    }[caracter]));

    function leerVentasPorPagina() {
        try {
            const guardado = Number(localStorage.getItem(clavePorPagina));
            return opcionesPorPagina.includes(guardado) ? guardado : 10;
        } catch (error) {
            console.warn('No se pudo leer la preferencia de paginación de ventas:', error);
            try {
                localStorage.removeItem(clavePorPagina);
            } catch (errorAlLimpiar) {
                console.warn('No se pudo limpiar la preferencia de paginación de ventas:', errorAlLimpiar);
            }
            return 10;
        }
    }

    const guardarVentasPorPagina = cantidad => {
        try {
            localStorage.setItem(clavePorPagina, String(cantidad));
        } catch (error) {
            console.warn('No se pudo guardar la preferencia de paginación de ventas:', error);
            try {
                localStorage.removeItem(clavePorPagina);
            } catch (errorAlLimpiar) {
                console.warn('No se pudo limpiar la preferencia de paginación de ventas:', errorAlLimpiar);
            }
        }
    };

    const obtenerJson = async url => {
        const respuesta = await fetch(url);
        let datos;
        try {
            datos = await respuesta.json();
        } catch (error) {
            throw new Error('El servidor devolvió una respuesta no válida.');
        }

        if (respuesta.status === 401 && datos.redirect) {
            window.location.href = datos.redirect;
            throw new Error('La sesión ha expirado.');
        }
        if (!respuesta.ok || datos.success !== true) {
            throw new Error(datos.message || 'No se pudo completar la solicitud.');
        }

        return datos;
    };

    const obtenerFiltros = () => {
        const parametros = new URLSearchParams();
        if (fechaDesde.value) parametros.set('desde', fechaDesde.value);
        if (fechaHasta.value) parametros.set('hasta', fechaHasta.value);
        if (sucursal.value) parametros.set('sucursal', sucursal.value);
        if (busqueda.value.trim()) parametros.set('busqueda', busqueda.value.trim());
        return parametros;
    };

    const urlConParametros = (ruta, parametros) => {
        const cadena = parametros.toString();
        return cadena ? `${ruta}?${cadena}` : ruta;
    };

    const formatearFecha = fecha => {
        const valor = new Date(String(fecha).replace(' ', 'T'));
        if (Number.isNaN(valor.getTime())) return String(fecha);
        return new Intl.DateTimeFormat('es-MX', {
            dateStyle: 'short',
            timeStyle: 'short'
        }).format(valor);
    };

    const formatearPartesFecha = fecha => {
        const valor = new Date(String(fecha).replace(' ', 'T'));
        if (Number.isNaN(valor.getTime())) {
            return { fecha: String(fecha), hora: '' };
        }
        return {
            fecha: new Intl.DateTimeFormat('es-MX', { dateStyle: 'short' }).format(valor),
            hora: new Intl.DateTimeFormat('es-MX', { timeStyle: 'short' }).format(valor)
        };
    };

    const actualizarResumen = resumen => {
        document.getElementById('ventas-total-vendido').textContent =
            moneda.format(Number(resumen.total_vendido));
        document.getElementById('ventas-numero-ventas').textContent =
            Number(resumen.numero_ventas).toLocaleString('es-MX');
        document.getElementById('ventas-ticket-promedio').textContent =
            moneda.format(Number(resumen.ticket_promedio));
        const producto = resumen.producto_mas_vendido;
        document.getElementById('ventas-producto-mas-vendido').textContent =
            producto ? producto.nombre : '—';
        document.getElementById('ventas-producto-mas-vendido').title =
            producto ? producto.nombre : '';
        document.getElementById('ventas-producto-cantidad').textContent =
            producto ? `${Number(producto.cantidad).toLocaleString('es-MX')} unidades vendidas` : 'Sin ventas en el periodo';
    };

    const actualizarResumenError = () => {
        document.getElementById('ventas-total-vendido').textContent = '—';
        document.getElementById('ventas-numero-ventas').textContent = '—';
        document.getElementById('ventas-ticket-promedio').textContent = '—';
        document.getElementById('ventas-producto-mas-vendido').textContent = '—';
        document.getElementById('ventas-producto-cantidad').textContent = 'No disponible';
    };

    const paginasVisibles = (pagina, totalPaginas) => {
        if (totalPaginas <= 7) {
            return Array.from({ length: totalPaginas }, (_, indice) => indice + 1);
        }

        const visibles = new Set([1, totalPaginas]);
        for (let numero = Math.max(2, pagina - 1); numero <= Math.min(totalPaginas - 1, pagina + 1); numero += 1) {
            visibles.add(numero);
        }

        const resultado = [];
        let anterior = 0;
        for (const numero of [...visibles].sort((a, b) => a - b)) {
            if (numero - anterior === 2) resultado.push(numero - 1);
            else if (numero - anterior > 2) resultado.push('…');
            resultado.push(numero);
            anterior = numero;
        }
        return resultado;
    };

    const renderizarPaginacion = paginacion => {
        const total = Number(paginacion.total);
        const totalPaginas = Number(paginacion.total_paginas);
        paginaActual = Number(paginacion.pagina);

        if (total === 0) {
            resumenPaginacion.textContent = 'No hay ventas en este periodo';
            controlesPaginacion.hidden = true;
            controlesPaginacion.replaceChildren();
            return;
        }

        const inicio = (paginaActual - 1) * ventasPorPagina + 1;
        const fin = Math.min(paginaActual * ventasPorPagina, total);
        resumenPaginacion.textContent = `Mostrando ${inicio}-${fin} de ${total.toLocaleString('es-MX')} ventas`;
        controlesPaginacion.hidden = false;
        controlesPaginacion.innerHTML = `
            <button class="paginacion-boton" type="button" data-paginacion="anterior"
                aria-label="Página anterior" ${paginaActual === 1 ? 'disabled' : ''}>Anterior</button>
            ${paginasVisibles(paginaActual, totalPaginas).map(numero => Number.isInteger(numero)
                ? `<button class="paginacion-boton${numero === paginaActual ? ' activa' : ''}" type="button"
                    data-pagina="${numero}" aria-label="Página ${numero}" ${numero === paginaActual ? 'aria-current="page" disabled' : ''}>${numero}</button>`
                : '<span class="paginacion-elipsis" aria-hidden="true">…</span>'
            ).join('')}
            <button class="paginacion-boton" type="button" data-paginacion="siguiente"
                aria-label="Página siguiente" ${paginaActual === totalPaginas ? 'disabled' : ''}>Siguiente</button>
        `;
    };

    const actualizarSucursales = sucursales => {
        const seleccionada = sucursal.value;
        sucursal.innerHTML = '<option value="">Todas las sucursales</option>' + sucursales.map(item => `
            <option value="${Number(item.id)}">
                ${escaparHtml(item.nombre)}${item.estado === 'inactiva' ? ' (Inactiva)' : ''}
            </option>
        `).join('');
        if (sucursales.some(item => String(item.id) === seleccionada)) {
            sucursal.value = seleccionada;
        }
    };

    const renderizarVentas = ventas => {
        if (ventas.length === 0) {
            tablaVentas.innerHTML = '<tr><td class="ventas-estado" colspan="8">No hay ventas en este periodo.</td></tr>';
            return;
        }

        tablaVentas.innerHTML = ventas.map(venta => {
            const fecha = formatearPartesFecha(venta.fecha_venta);
            return `
                <tr class="ventas-fila" data-venta-id="${Number(venta.id)}" tabindex="0"
                    aria-label="Ver detalle de la venta ${escaparHtml(venta.folio)}">
                    <td data-label="Folio">
                        <strong class="ventas-folio" title="${escaparHtml(venta.folio)}">${escaparHtml(venta.folio)}</strong>
                    </td>
                    <td data-label="Fecha y hora">
                        <span class="ventas-fecha-partes">
                            <span>${escaparHtml(fecha.fecha)}</span>
                            <span>${escaparHtml(fecha.hora)}</span>
                        </span>
                    </td>
                    <td data-label="Sucursal"><span class="ventas-sucursal" title="${escaparHtml(venta.sucursal)}">${escaparHtml(venta.sucursal)}</span></td>
                    <td data-label="Cajero"><span class="ventas-cajero" title="${escaparHtml(venta.cajero)}">${escaparHtml(venta.cajero)}</span></td>
                    <td class="col-centro" data-label="Artículos">${Number(venta.articulos).toLocaleString('es-MX')}</td>
                    <td data-label="Método de pago"><span class="ventas-metodo-pago">${escaparHtml(venta.metodo_pago || '—')}</span></td>
                    <td class="col-centro" data-label="Total">${moneda.format(Number(venta.total))}</td>
                    <td class="col-centro ventas-detalle-celda" data-label="Detalle">
                        <button class="ventas-detalle-boton" type="button" data-ver-detalle="${Number(venta.id)}"
                            aria-label="Ver detalle" title="Ver detalle">
                            <i class="fa-solid fa-receipt" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    };

    const actualizarVentas = async () => {
        const idSolicitud = ++solicitudActual;
        const filtros = obtenerFiltros();
        const parametrosResumen = new URLSearchParams(filtros);
        const parametrosListado = new URLSearchParams(filtros);
        parametrosListado.set('pagina', String(paginaActual));
        parametrosListado.set('por_pagina', String(ventasPorPagina));
        tablaVentas.innerHTML = '<tr><td class="ventas-estado" colspan="8">Cargando ventas…</td></tr>';

        const resultadoResumen = await obtenerJson(
            urlConParametros('../../api/admin/ventas/resumen.php', parametrosResumen)
        ).then(
            datos => ({ correcto: true, datos }),
            error => ({ correcto: false, error })
        );
        if (idSolicitud !== solicitudActual) return;
        if (resultadoResumen.correcto) {
            actualizarResumen(resultadoResumen.datos.resumen);
            errorResumen.hidden = true;
            errorResumen.textContent = '';
        } else {
            console.error('No se pudo cargar el resumen de ventas:', resultadoResumen.error);
            actualizarResumenError();
            errorResumen.textContent = 'No se pudieron cargar las ventas.';
            errorResumen.hidden = false;
        }

        try {
            const datos = await obtenerJson(
                urlConParametros('../../api/admin/ventas/listar.php', parametrosListado)
            );
            if (idSolicitud !== solicitudActual) return;
            actualizarSucursales(datos.sucursales);
            renderizarVentas(datos.ventas);
            renderizarPaginacion(datos.paginacion);
        } catch (error) {
            if (idSolicitud !== solicitudActual) return;
            console.error('No se pudieron cargar las ventas:', error);
            tablaVentas.innerHTML = '<tr><td class="ventas-estado ventas-error" colspan="8">No se pudieron cargar las ventas.</td></tr>';
            resumenPaginacion.textContent = 'No se pudo cargar la paginación';
            controlesPaginacion.hidden = true;
        }
    };

    const fechaLocal = fecha => {
        const desplazamiento = fecha.getTimezoneOffset() * 60000;
        return new Date(fecha.getTime() - desplazamiento).toISOString().slice(0, 10);
    };

    const seleccionarPeriodo = periodo => {
        const hoy = new Date();
        const fechaFin = fechaLocal(hoy);
        fechaHasta.value = periodo === 'todo' ? '' : fechaFin;

        if (periodo === 'hoy') {
            fechaDesde.value = fechaFin;
        } else if (periodo === '7dias') {
            const inicio = new Date(hoy);
            inicio.setDate(inicio.getDate() - 6);
            fechaDesde.value = fechaLocal(inicio);
        } else if (periodo === 'mes') {
            fechaDesde.value = fechaLocal(new Date(hoy.getFullYear(), hoy.getMonth(), 1));
        } else {
            fechaDesde.value = '';
        }

        document.querySelectorAll('[data-ventas-periodo]').forEach(boton => {
            boton.setAttribute('aria-pressed', String(boton.dataset.ventasPeriodo === periodo));
        });
        paginaActual = 1;
        actualizarVentas();
    };

    const abrirDetalle = async (id, activador) => {
        elementoEnfoqueAnterior = activador instanceof HTMLElement ? activador : null;
        contenidoDetalle.innerHTML = '<p class="nota-panel">Cargando detalle de venta…</p>';
        modal.hidden = false;
        document.body.classList.add('inventario-modal-abierto');
        document.getElementById('boton-cerrar-modal-venta').focus();

        try {
            const datos = await obtenerJson(`../../api/admin/ventas/detalle.php?id=${encodeURIComponent(id)}`);
            const venta = datos.venta;
            contenidoDetalle.innerHTML = `
                <div class="ventas-ticket-datos">
                    <p><span>Folio</span><strong>${escaparHtml(venta.folio)}</strong></p>
                    <p><span>Fecha y hora</span><strong>${escaparHtml(formatearFecha(venta.fecha_venta))}</strong></p>
                    <p><span>Sucursal</span><strong>${escaparHtml(venta.sucursal)}</strong></p>
                    <p><span>Cajero</span><strong>${escaparHtml(venta.cajero)}</strong></p>
                    <p><span>Método de pago</span><strong>${escaparHtml(venta.metodo_pago || '—')}</strong></p>
                </div>
                <div class="ventas-ticket-tabla-contenedor">
                    <table class="ventas-ticket-tabla">
                        <thead>
                            <tr><th>Producto</th><th class="col-centro">Cant.</th><th class="col-derecha">Precio unitario</th><th class="col-derecha">Subtotal</th></tr>
                        </thead>
                        <tbody>
                            ${venta.productos.length === 0
                                ? '<tr><td colspan="4" class="ventas-estado">Esta venta no tiene productos en el detalle.</td></tr>'
                                : venta.productos.map(producto => `
                                    <tr>
                                        <td data-label="Producto">${escaparHtml(producto.producto)}</td>
                                        <td class="col-centro" data-label="Cantidad">${Number(producto.cantidad)}</td>
                                        <td class="col-derecha" data-label="Precio unitario">${moneda.format(Number(producto.precio_unitario))}</td>
                                        <td class="col-derecha" data-label="Subtotal">${moneda.format(Number(producto.subtotal))}</td>
                                    </tr>
                                `).join('')
                            }
                        </tbody>
                    </table>
                </div>
                <p class="ventas-ticket-total"><span>Total</span><strong>${moneda.format(Number(venta.total))}</strong></p>
            `;
        } catch (error) {
            console.error('No se pudo cargar el detalle de la venta:', error);
            contenidoDetalle.innerHTML = '<p class="ventas-detalle-error" role="alert">No se pudo cargar el detalle de la venta.</p>';
        }
    };

    const cerrarDetalle = () => {
        modal.hidden = true;
        document.body.classList.remove('inventario-modal-abierto');
        if (elementoEnfoqueAnterior?.isConnected) {
            elementoEnfoqueAnterior.focus();
        }
        elementoEnfoqueAnterior = null;
    };

    document.querySelectorAll('[data-ventas-periodo]').forEach(boton => {
        boton.addEventListener('click', () => seleccionarPeriodo(boton.dataset.ventasPeriodo));
    });

    [fechaDesde, fechaHasta, sucursal].forEach(control => {
        control.addEventListener('change', () => {
            paginaActual = 1;
            document.querySelectorAll('[data-ventas-periodo]').forEach(boton => {
                boton.setAttribute('aria-pressed', 'false');
            });
            actualizarVentas();
        });
    });

    busqueda.addEventListener('input', () => {
        window.clearTimeout(temporizadorBusqueda);
        temporizadorBusqueda = window.setTimeout(() => {
            paginaActual = 1;
            actualizarVentas();
        }, 250);
    });

    document.getElementById('ventas-limpiar-filtros').addEventListener('click', () => {
        fechaDesde.value = '';
        fechaHasta.value = '';
        sucursal.value = '';
        busqueda.value = '';
        document.querySelectorAll('[data-ventas-periodo]').forEach(boton => {
            boton.setAttribute('aria-pressed', 'false');
        });
        paginaActual = 1;
        actualizarVentas();
    });

    selectorPorPagina.addEventListener('change', () => {
        const nuevoTamanio = Number(selectorPorPagina.value);
        if (!opcionesPorPagina.includes(nuevoTamanio)) return;
        ventasPorPagina = nuevoTamanio;
        paginaActual = 1;
        guardarVentasPorPagina(ventasPorPagina);
        actualizarVentas();
    });

    controlesPaginacion.addEventListener('click', evento => {
        const boton = evento.target.closest('button');
        if (!boton || boton.disabled) return;
        if (boton.dataset.paginacion === 'anterior') paginaActual -= 1;
        else if (boton.dataset.paginacion === 'siguiente') paginaActual += 1;
        else if (boton.dataset.pagina) paginaActual = Number(boton.dataset.pagina);
        actualizarVentas();
    });

    tablaVentas.addEventListener('click', evento => {
        const botonDetalle = evento.target.closest('[data-ver-detalle]');
        const fila = evento.target.closest('tr[data-venta-id]');
        const id = botonDetalle?.dataset.verDetalle || fila?.dataset.ventaId;
        if (!id) return;
        abrirDetalle(id, botonDetalle || fila);
    });

    tablaVentas.addEventListener('keydown', evento => {
        const fila = evento.target.closest('tr[data-venta-id]');
        if (!fila || (evento.key !== 'Enter' && evento.key !== ' ')) return;
        if (evento.target.closest('button')) return;
        evento.preventDefault();
        abrirDetalle(fila.dataset.ventaId, fila);
    });

    document.getElementById('boton-cerrar-modal-venta').addEventListener('click', cerrarDetalle);
    modal.addEventListener('click', evento => {
        if (evento.target === modal) cerrarDetalle();
    });
    modal.addEventListener('keydown', evento => {
        if (evento.key === 'Escape') {
            evento.preventDefault();
            cerrarDetalle();
            return;
        }
        if (evento.key !== 'Tab') return;

        const elementosEnfocables = [...dialogo.querySelectorAll(
            'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
        )];
        if (elementosEnfocables.length === 0) {
            evento.preventDefault();
            dialogo.focus();
            return;
        }

        const primero = elementosEnfocables[0];
        const ultimo = elementosEnfocables[elementosEnfocables.length - 1];
        if (evento.shiftKey && document.activeElement === primero) {
            evento.preventDefault();
            ultimo.focus();
        } else if (!evento.shiftKey && document.activeElement === ultimo) {
            evento.preventDefault();
            primero.focus();
        }
    });

    actualizarVentas();
})();
