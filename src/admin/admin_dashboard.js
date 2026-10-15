(() => {
    const elementoProductos = document.getElementById('total-productos-activos');
    const tablaStock = document.getElementById('dashboard-stock-bajo');
    const listaVentas = document.getElementById('dashboard-ultimas-ventas');
    const topSucursales = document.getElementById('dashboard-top-sucursales');
    const listaNotificaciones = document.getElementById('lista-notificaciones-stock');
    const notificacionesVacias = document.getElementById('notificaciones-vacias');
    const indicadorNotificaciones = document.querySelector('.indicador-notificaciones');

    const escaparHtml = valor => String(valor ?? '').replace(/[&<>"']/g, caracter => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    }[caracter]));

    const moneda = new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    });

    const formatearFecha = fecha => {
        const valor = new Date(String(fecha).replace(' ', 'T'));
        if (Number.isNaN(valor.getTime())) return String(fecha);
        return new Intl.DateTimeFormat('es-MX', {
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

    const cargarDashboard = async () => {
        try {
            const respuesta = await fetch('../../api/admin/dashboard.php');
            const datos = await respuesta.json();

            if (respuesta.status === 401 && datos.redirect) {
                window.location.href = datos.redirect;
                return;
            }
            if (datos.success !== true) {
                throw new Error(datos.message || 'No se pudo cargar el dashboard.');
            }

            elementoProductos.textContent = Number(datos.total_productos_activos);
            document.getElementById('resumen-inventario').textContent = 'Productos activos';
            renderizarStock(datos.stock_bajo, datos.umbral_stock);
            renderizarNotificaciones(datos.stock_bajo);
            renderizarVentas(datos.ultimas_ventas);
            renderizarTopSucursales(datos.ventas_por_sucursal);
        } catch (error) {
            console.error('No se pudo cargar el dashboard:', error);
            elementoProductos.textContent = '—';
            tablaStock.innerHTML = '<tr><td colspan="3" class="nota-panel">No se pudo cargar el inventario.</td></tr>';
            listaVentas.innerHTML = '<p class="nota-panel">No se pudieron cargar las ventas.</p>';
            topSucursales.innerHTML = '<p class="nota-panel">No se pudieron cargar las ventas.</p>';
            listaNotificaciones.replaceChildren();
            notificacionesVacias.hidden = false;
            notificacionesVacias.textContent = 'No se pudieron cargar las alertas de inventario.';
            indicadorNotificaciones.hidden = true;
        }
    };

    cargarDashboard();
})();
