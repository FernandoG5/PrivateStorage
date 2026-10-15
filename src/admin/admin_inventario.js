(() => {
    const selectorCategoria = document.querySelector('select[aria-label="Filtrar por categoría"]');
    const selectorSucursal = document.querySelector('select[aria-label="Filtrar por sucursal"]');
    const botonAgregar = document.querySelector('#sec-inventario .inventario-herramientas .boton-nuevo');
    const formulario = document.getElementById('formulario-producto-admin');
    const modal = document.getElementById('modal-producto-admin');
    const tituloModal = document.getElementById('titulo-modal-producto-admin');
    const errorFormulario = document.getElementById('error-formulario-producto-admin');
    const toast = document.getElementById('toast-inventario-admin');
    const cuerpoTabla = document.getElementById('tabla-inventario-admin');
    const categorias = document.getElementById('producto-admin-categoria');
    const sucursales = document.getElementById('producto-admin-sucursal');
    const paginacion = document.querySelector('#sec-inventario .inventario-paginacion');
    const selectorPorPagina = paginacion.querySelector('select');
    const resumenPaginacion = paginacion.querySelector('.inventario-paginacion-resumen');
    const controlesPaginacion = paginacion.querySelector('.inventario-paginacion-controles');
    const clavePorPagina = 'privateStorage.inventario.productosPorPagina';
    const opcionesPorPagina = [5, 10, 20, 30];
    let productos = [];
    let listaCategorias = [];
    let listaSucursales = [];
    let paginaActual = 1;
    let productosPorPagina = 10;
    let elementoEnfoqueAnterior = null;
    let temporizadorToast;

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

    const opciones = (items, textoInicial, etiqueta) => {
        return `<option value="">${textoInicial}</option>` + items.map(item => `
            <option value="${Number(item.id)}">
                ${escaparHtml(etiqueta(item))}
            </option>
        `).join('');
    };

    const leerProductosPorPagina = () => {
        try {
            const guardado = Number(localStorage.getItem(clavePorPagina));
            return opcionesPorPagina.includes(guardado) ? guardado : 10;
        } catch (error) {
            console.warn('No se pudo leer la preferencia de paginación:', error);
            try {
                localStorage.removeItem(clavePorPagina);
            } catch (errorAlLimpiar) {
                console.warn('No se pudo limpiar la preferencia de paginación:', errorAlLimpiar);
            }
            return 10;
        }
    };

    productosPorPagina = leerProductosPorPagina();
    selectorPorPagina.value = String(productosPorPagina);

    const guardarProductosPorPagina = cantidad => {
        try {
            localStorage.setItem(clavePorPagina, String(cantidad));
        } catch (error) {
            console.warn('No se pudo guardar la preferencia de paginación:', error);
            try {
                localStorage.removeItem(clavePorPagina);
            } catch (errorAlLimpiar) {
                console.warn('No se pudo limpiar la preferencia de paginación:', errorAlLimpiar);
            }
        }
    };

    const filtrarProductos = () => {
        const categoriaSeleccionada = selectorCategoria.value;
        const sucursalSeleccionada = selectorSucursal.value;
        return productos.filter(producto =>
            (!categoriaSeleccionada || String(producto.categoria_id ?? '') === categoriaSeleccionada)
            && (!sucursalSeleccionada || String(producto.sucursal_id ?? '') === sucursalSeleccionada)
        );
    };

    // Mantiene el recorte de filas aislado para poder migrarlo a LIMIT/OFFSET después.
    const paginar = (lista, pagina, porPagina) => {
        const inicio = (pagina - 1) * porPagina;
        return lista.slice(inicio, inicio + porPagina);
    };

    // Limita los botones visibles y agrega puntos suspensivos para rangos extensos.
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

    // Mantiene el resumen y la navegación sincronizados con el resultado filtrado.
    const renderizarPaginacion = totalProductos => {
        if (totalProductos === 0) {
            resumenPaginacion.textContent = 'No hay productos que coincidan con los filtros';
            controlesPaginacion.hidden = true;
            controlesPaginacion.innerHTML = '';
            return;
        }

        const totalPaginas = Math.ceil(totalProductos / productosPorPagina);
        paginaActual = Math.min(Math.max(1, paginaActual), totalPaginas);
        const inicio = (paginaActual - 1) * productosPorPagina + 1;
        const fin = Math.min(paginaActual * productosPorPagina, totalProductos);
        resumenPaginacion.textContent = `Mostrando ${inicio}-${fin} de ${totalProductos} productos`;
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

    const renderizarProductos = () => {
        const filtrados = filtrarProductos();
        const totalPaginas = Math.ceil(filtrados.length / productosPorPagina);
        paginaActual = Math.min(Math.max(1, paginaActual), Math.max(1, totalPaginas));
        const pagina = paginar(filtrados, paginaActual, productosPorPagina);
        renderizarPaginacion(filtrados.length);

        cuerpoTabla.innerHTML = pagina.map(producto => `
            <tr>
                <td class="col-centro">${producto.sucursal_id === null ? '—' : Number(producto.sucursal_id)}</td>
                <td class="codigo-producto">${escaparHtml(producto.codigo)}</td>
                <td><strong>${escaparHtml(producto.nombre)}</strong></td>
                <td>${escaparHtml(producto.categoria || 'Sin categoría')}</td>
                <td class="dinero-producto col-derecha">${moneda.format(Number(producto.precio ?? 0))}</td>
                <td class="col-centro">${producto.existencias === null ? '—' : Number(producto.existencias)}</td>
                <td class="dinero-producto col-derecha">${producto.existencias === null ? '—' : moneda.format(Number(producto.precio) * Number(producto.existencias))}</td>
                <td class="col-centro">
                    <div class="acciones-producto">
                        <button class="accion-producto accion-editar" type="button" data-editar-producto="${Number(producto.id)}" data-sucursal-id="${producto.sucursal_id === null ? '' : Number(producto.sucursal_id)}" title="Editar" aria-label="Editar"><i class="fa-solid fa-pen" aria-hidden="true"></i><span class="accion-texto">Editar</span></button>
                        <button class="accion-producto accion-eliminar" type="button" disabled title="Eliminar" aria-label="Eliminar"><i class="fa-solid fa-trash" aria-hidden="true"></i><span class="accion-texto">Eliminar</span></button>
                    </div>
                </td>
            </tr>
        `).join('');

        if (filtrados.length === 0) {
            cuerpoTabla.innerHTML = '<tr><td colspan="8" class="inventario-vacio">No hay productos que coincidan con los filtros</td></tr>';
        }
    };

    const cargarInventario = async () => {
        try {
            const respuesta = await fetch('../../api/admin/inventario.php');
            const datos = await respuesta.json();
            if (respuesta.status === 401 && datos.redirect) {
                window.location.href = datos.redirect;
                return;
            }
            if (datos.success !== true) {
                throw new Error(datos.message || 'No se pudo cargar el inventario.');
            }

            productos = Array.isArray(datos.productos) ? datos.productos : [];
            listaCategorias = Array.isArray(datos.categorias) ? datos.categorias : [];
            listaSucursales = Array.isArray(datos.sucursales) ? datos.sucursales : [];

            selectorCategoria.innerHTML = opciones(
                listaCategorias,
                'Todas las categorías',
                categoria => categoria.nombre
            );
            selectorSucursal.innerHTML = opciones(
                listaSucursales,
                'Todas las sucursales',
                sucursal => sucursal.nombre
            );
            categorias.innerHTML = opciones(
                listaCategorias,
                'Selecciona una categoría',
                categoria => categoria.nombre
            );
            sucursales.innerHTML = opciones(
                listaSucursales,
                'Selecciona una sucursal',
                sucursal => `${sucursal.nombre}${sucursal.estado === 'inactiva' ? ' (Inactiva)' : ''}`
            );
            renderizarProductos();
        } catch (error) {
            console.error('No se pudo cargar el inventario del administrador:', error);
            alert(error.message || 'No se pudo cargar el inventario.');
        }
    };

    const mostrarFormulario = (producto = null, activador = document.activeElement) => {
        formulario.reset();
        errorFormulario.textContent = '';
        errorFormulario.hidden = true;
        elementoEnfoqueAnterior = activador instanceof HTMLElement ? activador : null;
        tituloModal.textContent = producto ? 'Editar producto' : 'Agregar producto';
        document.getElementById('producto-admin-id').value = producto ? String(producto.id) : '';
        document.getElementById('producto-admin-sucursal-anterior').value =
            producto && producto.sucursal_id !== null ? String(producto.sucursal_id) : '';
        document.getElementById('producto-admin-codigo').value = producto ? producto.codigo : '';
        document.getElementById('producto-admin-nombre').value = producto ? producto.nombre : '';
        document.getElementById('producto-admin-precio').value = producto ? producto.precio : '';
        categorias.value = producto && producto.categoria_id !== null ? String(producto.categoria_id) : '';
        sucursales.value = producto && producto.sucursal_id !== null ? String(producto.sucursal_id) : '';
        document.getElementById('producto-admin-existencias').value =
            producto && producto.existencias !== null ? String(producto.existencias) : '0';
        document.getElementById('producto-admin-stock-minimo').value =
            producto && producto.stock_minimo !== null ? String(producto.stock_minimo) : '0';
        sucursales.disabled = Boolean(producto);
        formulario.classList.remove('oculto');
        modal.hidden = false;
        document.body.classList.add('inventario-modal-abierto');
        document.getElementById('producto-admin-codigo').focus();
    };

    const ocultarFormulario = () => {
        formulario.reset();
        sucursales.disabled = false;
        errorFormulario.textContent = '';
        errorFormulario.hidden = true;
        formulario.classList.add('oculto');
        modal.hidden = true;
        document.body.classList.remove('inventario-modal-abierto');
        if (elementoEnfoqueAnterior?.isConnected) {
            elementoEnfoqueAnterior.focus();
        }
        elementoEnfoqueAnterior = null;
    };

    const mostrarErrorFormulario = mensaje => {
        errorFormulario.textContent = mensaje;
        errorFormulario.hidden = false;
    };

    const mostrarToast = mensaje => {
        window.clearTimeout(temporizadorToast);
        toast.textContent = mensaje;
        toast.hidden = false;
        temporizadorToast = window.setTimeout(() => {
            toast.hidden = true;
        }, 3500);
    };

    selectorCategoria.addEventListener('change', () => {
        paginaActual = 1;
        renderizarProductos();
    });
    selectorSucursal.addEventListener('change', () => {
        paginaActual = 1;
        renderizarProductos();
    });
    selectorPorPagina.addEventListener('change', () => {
        const nuevoTamanio = Number(selectorPorPagina.value);
        if (!opcionesPorPagina.includes(nuevoTamanio)) return;
        productosPorPagina = nuevoTamanio;
        paginaActual = 1;
        guardarProductosPorPagina(productosPorPagina);
        renderizarProductos();
    });
    controlesPaginacion.addEventListener('click', evento => {
        const boton = evento.target.closest('button');
        if (!boton || boton.disabled) return;

        if (boton.dataset.paginacion === 'anterior') paginaActual -= 1;
        else if (boton.dataset.paginacion === 'siguiente') paginaActual += 1;
        else if (boton.dataset.pagina) paginaActual = Number(boton.dataset.pagina);
        renderizarProductos();
    });
    botonAgregar.addEventListener('click', evento => mostrarFormulario(null, evento.currentTarget));
    document.getElementById('boton-cancelar-producto-admin').addEventListener('click', ocultarFormulario);
    document.getElementById('boton-cerrar-modal-producto-admin').addEventListener('click', ocultarFormulario);

    modal.addEventListener('click', evento => {
        if (evento.target === modal) ocultarFormulario();
    });

    document.addEventListener('keydown', evento => {
        if (modal.hidden) return;

        if (evento.key === 'Escape') {
            evento.preventDefault();
            ocultarFormulario();
            return;
        }

        if (evento.key !== 'Tab') return;

        const elementosEnfocables = [...modal.querySelectorAll(
            'button:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
        )].filter(elemento => elemento.getClientRects().length > 0);
        const primero = elementosEnfocables[0];
        const ultimo = elementosEnfocables[elementosEnfocables.length - 1];

        if (!elementosEnfocables.includes(document.activeElement)) {
            evento.preventDefault();
            primero?.focus();
        } else if (evento.shiftKey && document.activeElement === primero) {
            evento.preventDefault();
            ultimo.focus();
        } else if (!evento.shiftKey && document.activeElement === ultimo) {
            evento.preventDefault();
            primero.focus();
        }
    });

    cuerpoTabla.addEventListener('click', evento => {
        const boton = evento.target.closest('button[data-editar-producto]');
        if (!boton) return;
        const producto = productos.find(item =>
            Number(item.id) === Number(boton.dataset.editarProducto)
            && String(item.sucursal_id ?? '') === boton.dataset.sucursalId
        );
        if (producto) mostrarFormulario(producto, boton);
    });

    formulario.addEventListener('submit', async evento => {
        evento.preventDefault();
        const campoInvalido = formulario.querySelector(':invalid');
        if (campoInvalido) {
            const etiqueta = formulario.querySelector(`label[for="${campoInvalido.id}"]`)?.textContent.trim();
            const mensaje = campoInvalido.validity.valueMissing
                ? `Completa el campo ${etiqueta || 'obligatorio'}.`
                : `Revisa el valor del campo ${etiqueta || 'indicado'}.`;
            mostrarErrorFormulario(mensaje);
            campoInvalido.focus();
            return;
        }

        const datosFormulario = new URLSearchParams({
            id: document.getElementById('producto-admin-id').value,
            codigo: document.getElementById('producto-admin-codigo').value,
            nombre: document.getElementById('producto-admin-nombre').value,
            precio: document.getElementById('producto-admin-precio').value,
            categoria_id: categorias.value,
            sucursal_id: sucursales.value,
            sucursal_anterior_id: document.getElementById('producto-admin-sucursal-anterior').value,
            existencias: document.getElementById('producto-admin-existencias').value,
            stock_minimo: document.getElementById('producto-admin-stock-minimo').value
        });

        try {
            const respuesta = await fetch('../../api/admin/inventario.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: datosFormulario
            });
            const datos = await respuesta.json();
            if (respuesta.status === 401 && datos.redirect) {
                window.location.href = datos.redirect;
                return;
            }
            if (datos.success !== true) {
                mostrarErrorFormulario(datos.message || 'No se pudo guardar el producto.');
                return;
            }

            ocultarFormulario();
            await cargarInventario();
            mostrarToast(datos.message || 'Producto guardado correctamente.');
        } catch (error) {
            console.error('No se pudo guardar el producto:', error);
            mostrarErrorFormulario('No se pudo comunicar con el servidor. Intenta nuevamente.');
        }
    });

    cargarInventario();
})();
