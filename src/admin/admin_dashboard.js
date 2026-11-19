(() => {
    const elementoProductos = document.getElementById('total-productos-activos');
    const tablaStock = document.getElementById('dashboard-stock-bajo');
    const listaVentas = document.getElementById('dashboard-ultimas-ventas');
    const topSucursales = document.getElementById('dashboard-top-sucursales');
    const listaNotificaciones = document.getElementById('lista-notificaciones-stock');
    const notificacionesVacias = document.getElementById('notificaciones-vacias');
    const indicadorNotificaciones = document.querySelector('.indicador-notificaciones');
    let solicitudDashboard = 0;

    const escaparHtml = valor => String(valor ?? '').replace(/[&<>"']/g, caracter => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    }[caracter]));

    const obtenerLocale = () => (
        localStorage.getItem('private-storage-idioma') === 'en' ? 'en-US' : 'es-MX'
    );

    const moneda = new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    });

    const formatearFecha = fecha => {
        const valor = new Date(String(fecha).replace(' ', 'T'));
        if (Number.isNaN(valor.getTime())) return String(fecha);
        return new Intl.DateTimeFormat(obtenerLocale(), {
            dateStyle: 'short',
            timeStyle: 'short'
        }).format(valor);
    };

    const estadoVacioStock = () => {
        tablaStock.innerHTML = '<tr><td colspan="3" class="nota-panel">No hay productos con stock bajo.</td></tr>';
    };

    const renderizarStock = (productos, umbral) => {
        if (productos.length === 0) {
            estadoVacioStock();
            return;
        }

        tablaStock.innerHTML = productos.map(producto => `
            <tr>
                <td><span class="producto-demo-dot dot-${escaparHtml(producto.gravedad)}"></span>${escaparHtml(producto.producto)}</td>
                <td>${escaparHtml(producto.sucursal)}</td>
                <td>${Number(producto.restante)} <small>de umbral ${Number(umbral)}</small></td>
            </tr>
        `).join('');
    };

    const renderizarNotificaciones = productos => {
        indicadorNotificaciones.hidden = productos.length === 0;
        notificacionesVacias.hidden = productos.length > 0;
        listaNotificaciones.innerHTML = productos.map(producto => `
            <article class="notificacion-demo notificacion-stock">
                <span class="notificacion-icono" aria-hidden="true"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i></span>
                <div>
                    <h3>Stock bajo: ${escaparHtml(producto.producto)}</h3>
                    <p>${escaparHtml(producto.sucursal)} · ${Number(producto.restante)} unidades restantes.</p>
                </div>
            </article>
        `).join('');
    };

    const renderizarVentas = ventas => {
        if (ventas.length === 0) {
            listaVentas.innerHTML = '<p class="nota-panel">Aún no hay ventas registradas.</p>';
            return;
        }

        listaVentas.innerHTML = ventas.map(venta => `
            <div class="venta-demo">
                <span class="venta-icono" aria-hidden="true"><i class="fa-solid fa-store" aria-hidden="true"></i></span>
                <span>
                    <strong>${escaparHtml(venta.sucursal)}</strong>
                    <small>${escaparHtml(formatearFecha(venta.fecha_venta))} · Folio ${escaparHtml(venta.folio)}</small>
                </span>
                <strong class="venta-monto">${moneda.format(Number(venta.total))}</strong>
            </div>
        `).join('');
    };

    const renderizarTopSucursales = sucursales => {
        if (sucursales.length === 0) {
            topSucursales.innerHTML = '<p class="nota-panel">Aún no hay ventas registradas.</p>';
            return;
        }

        const maximo = Math.max(...sucursales.map(sucursal => Number(sucursal.total_vendido)));
        topSucursales.innerHTML = sucursales.map((sucursal, indice) => {
            const progreso = maximo > 0
                ? Math.round((Number(sucursal.total_vendido) / maximo) * 100)
                : 0;
            return `
                <div class="ranking-fila">
                    <span>${String(indice + 1).padStart(2, '0')}</span>
                    <strong title="${escaparHtml(sucursal.sucursal)}">${escaparHtml(sucursal.sucursal)}</strong>
                    <div class="ranking-track"><i class="progreso-cargado" style="--progreso-demo: ${progreso}%"></i></div>
                    <small>${moneda.format(Number(sucursal.total_vendido))}</small>
                </div>
            `;
        }).join('');
    };

    const renderizarGraficasVentas = datos => {
        const resumen = datos.resumen_ventas;
        const intervalos = datos.intervalos_ventas;
        const periodo = datos.periodo;
        const locale = obtenerLocale();
        const dinero = valor => moneda.format(Number(valor) || 0);

        document.getElementById('dashboard-ventas-total').textContent = dinero(resumen.total);
        document.getElementById('dashboard-ventas-cantidad').textContent = String(resumen.cantidad);
        document.getElementById('dashboard-ventas-promedio').textContent = dinero(resumen.promedio);

        const formatoFecha = fecha => {
            const [anio, mes, dia] = fecha.split('-').map(Number);
            return new Intl.DateTimeFormat(locale, {
                day: 'numeric',
                month: 'short',
                timeZone: 'UTC'
            }).format(new Date(Date.UTC(anio, mes - 1, dia, 12)));
        };
        const desde = formatoFecha(datos.desde);
        const hasta = formatoFecha(datos.hasta);
        document.getElementById('dashboard-ventas-fecha').textContent =
            datos.desde === datos.hasta ? desde : `${desde} – ${hasta}`;

        const titulos = {
            hoy: 'Ventas por hora',
            semana: 'Ventas por día de la semana',
            mes: 'Ventas por día del mes'
        };
        document.getElementById('dashboard-titulo-grafica').textContent = titulos[periodo];
        const maximo = Math.max(...intervalos.map(intervalo => Number(intervalo.total) || 0), 0);
        document.getElementById('dashboard-grafica-ventas').innerHTML = intervalos.map((intervalo, indice) => {
            const total = Number(intervalo.total) || 0;
            const cantidad = Number(intervalo.cantidad) || 0;
            const alto = maximo > 0 ? Math.max(total / maximo * 100, total > 0 ? 3 : 0) : 0;
            const etiqueta = periodo === 'semana'
                ? new Intl.DateTimeFormat(locale, { weekday: 'short', timeZone: 'UTC' }).format(
                    new Date(`${intervalo.fecha}T12:00:00Z`)
                )
                : intervalo.etiqueta;
            const mostrarEtiqueta = periodo === 'hoy'
                ? indice % 4 === 0
                : periodo === 'mes'
                    ? indice % 5 === 0 || indice === intervalos.length - 1
                    : true;
            const descripcion = `${etiqueta}: ${dinero(total)}, ${cantidad} ${cantidad === 1 ? 'venta' : 'ventas'}`;
            return `
                <div class="dashboard-grafica-columna" title="${escaparHtml(descripcion)}" aria-label="${escaparHtml(descripcion)}">
                    <span class="dashboard-grafica-valor">${total > 0 ? escaparHtml(dinero(total)) : ''}</span>
                    <div class="dashboard-grafica-barra-contenedor">
                        <span class="dashboard-grafica-barra" style="height:${alto}%"></span>
                    </div>
                    <span class="dashboard-grafica-etiqueta">${mostrarEtiqueta ? escaparHtml(etiqueta) : ''}</span>
                </div>
            `;
        }).join('');
        document.getElementById('dashboard-grafica-ventas').setAttribute(
            'aria-label',
            `${titulos[periodo]}. Total vendido: ${dinero(resumen.total)} en ${resumen.cantidad} ventas.`
        );
        document.getElementById('dashboard-grafica-vacia').hidden = Number(resumen.cantidad) > 0;

        const metodos = datos.metodos_pago;
        const totalMetodos = metodos.reduce((total, metodo) => total + (Number(metodo.cantidad) || 0), 0);
        const colores = {
            efectivo: '#7040c0',
            tarjeta: '#168aad',
            transferencia: '#e69b24'
        };
        const coloresAlternos = ['#d4668d', '#5676c5', '#4e9b72', '#d47a3c'];
        let angulo = 0;
        const segmentos = metodos.map((metodo, indice) => {
            const cantidad = Number(metodo.cantidad) || 0;
            const fin = totalMetodos > 0 ? angulo + cantidad / totalMetodos * 360 : angulo;
            const color = colores[String(metodo.metodo).toLocaleLowerCase('es-MX')]
                || coloresAlternos[indice % coloresAlternos.length];
            const segmento = `${color} ${angulo}deg ${fin}deg`;
            angulo = fin;
            return {
                ...metodo,
                cantidad,
                color,
                porcentaje: totalMetodos > 0 ? cantidad / totalMetodos * 100 : 0,
                segmento
            };
        });
        const dona = document.getElementById('dashboard-grafica-metodos');
        dona.style.background = totalMetodos > 0
            ? `conic-gradient(${segmentos.map(metodo => metodo.segmento).join(', ')})`
            : 'conic-gradient(var(--borde) 0deg 360deg)';
        dona.setAttribute(
            'aria-label',
            totalMetodos > 0
                ? `Distribución de ${totalMetodos} ventas por método de pago.`
                : 'No hay ventas registradas en este periodo.'
        );
        document.getElementById('dashboard-metodos-cantidad').textContent = String(totalMetodos);
        document.getElementById('dashboard-leyenda-metodos').innerHTML = segmentos.map(metodo => `
            <div class="dashboard-metodo-fila">
                <span class="dashboard-metodo-nombre">
                    <i aria-hidden="true" style="--color-metodo:${metodo.color}"></i>
                    ${escaparHtml(metodo.metodo)}
                </span>
                <span>${metodo.cantidad} · ${metodo.porcentaje.toFixed(0)}%</span>
            </div>
        `).join('');
        document.getElementById('dashboard-metodos-vacio').hidden = totalMetodos > 0;
    };

    const cargarDashboard = async (periodo = 'mes') => {
        const periodoValido = typeof periodo === 'string' && ['hoy', 'semana', 'mes'].includes(periodo)
            ? periodo
            : 'mes';
        const solicitud = ++solicitudDashboard;
        document.getElementById('dashboard-ventas-error').hidden = true;
        try {
            const respuesta = await fetch(
                `../../api/admin/dashboard.php?periodo=${encodeURIComponent(periodoValido)}`,
                { cache: 'no-store' }
            );
            const datos = await respuesta.json();

            if (respuesta.status === 401 && datos.redirect) {
                window.location.href = datos.redirect;
                return;
            }
            if (datos.success !== true) {
                throw new Error(datos.message || 'No se pudo cargar el dashboard.');
            }
            if (solicitud !== solicitudDashboard) return;

            elementoProductos.textContent = Number(datos.total_productos_activos);
            document.getElementById('resumen-inventario').textContent = 'Productos activos';
            renderizarStock(datos.stock_bajo, datos.umbral_stock);
            renderizarNotificaciones(datos.stock_bajo);
            renderizarVentas(datos.ultimas_ventas);
            renderizarTopSucursales(datos.ventas_por_sucursal);
            renderizarGraficasVentas(datos);
        } catch (error) {
            if (solicitud !== solicitudDashboard) return;
            console.error('No se pudo cargar el dashboard:', error);
            elementoProductos.textContent = '—';
            tablaStock.innerHTML = '<tr><td colspan="3" class="nota-panel">No se pudo cargar el inventario.</td></tr>';
            listaVentas.innerHTML = '<p class="nota-panel">No se pudieron cargar las ventas.</p>';
            topSucursales.innerHTML = '<p class="nota-panel">No se pudieron cargar las ventas.</p>';
            listaNotificaciones.replaceChildren();
            notificacionesVacias.hidden = false;
            notificacionesVacias.textContent = 'No se pudieron cargar las alertas de inventario.';
            indicadorNotificaciones.hidden = true;
            document.getElementById('dashboard-ventas-error').textContent =
                error.message || 'No se pudieron cargar las gráficas de ventas.';
            document.getElementById('dashboard-ventas-error').hidden = false;
            document.getElementById('dashboard-ventas-total').textContent = '—';
            document.getElementById('dashboard-ventas-cantidad').textContent = '—';
            document.getElementById('dashboard-ventas-promedio').textContent = '—';
            document.getElementById('dashboard-grafica-ventas').replaceChildren();
            document.getElementById('dashboard-grafica-vacia').hidden = true;
            document.getElementById('dashboard-grafica-metodos').style.background =
                'conic-gradient(var(--borde) 0deg 360deg)';
            document.getElementById('dashboard-metodos-cantidad').textContent = '—';
            document.getElementById('dashboard-leyenda-metodos').replaceChildren();
            document.getElementById('dashboard-metodos-vacio').hidden = true;
        }
    };

    document.querySelectorAll('[data-dashboard-periodo]').forEach(boton => {
        boton.addEventListener('click', () => {
            document.querySelectorAll('[data-dashboard-periodo]').forEach(opcion => {
                const seleccionado = opcion === boton;
                opcion.classList.toggle('activo', seleccionado);
                opcion.setAttribute('aria-pressed', String(seleccionado));
            });
            cargarDashboard(boton.dataset.dashboardPeriodo);
        });
    });

    cargarDashboard();
    window.addEventListener('private-storage-language-changed', () => {
        const periodoActivo = document.querySelector('[data-dashboard-periodo].activo')?.dataset.dashboardPeriodo || 'mes';
        cargarDashboard(periodoActivo);
    });
})();

    cargarDashboard();
    window.addEventListener('private-storage-language-changed', cargarDashboard);
})();
