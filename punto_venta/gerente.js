"use strict";

let datos = null;
let token = "";
let ocupado = false;

const buscar = id => document.getElementById(id);

const formatoMoneda = new Intl.NumberFormat("es-MX", {
    style: "currency",
    currency: "MXN"
});

function moneda(valor) {
    return formatoMoneda.format(Number(valor));
}

function mensaje(texto, error = false) {
    const elemento = buscar("estado-app");

    elemento.textContent = texto;
    elemento.classList.toggle("error", error);
    elemento.hidden = !texto;
}

function bloquear(valor) {
    ocupado = valor;

    document.querySelectorAll(
        'form[data-accion] button[type="submit"]'
    ).forEach(function (boton) {
        boton.disabled = valor || !datos;
    });

    buscar("actualizar-datos").disabled = valor;
}

async function solicitar(accion, formulario = null) {
    const opciones = {
        method: formulario ? "POST" : "GET",
        cache: "no-store",
        credentials: "same-origin"
    };

    if (formulario) {
        formulario.set("token", token);
        opciones.body = formulario;
    }

    let respuesta;

    try {
        respuesta = await fetch(
            "gerente_api.php?accion=" + encodeURIComponent(accion),
            opciones
        );
    } catch {
        throw new Error(
            "No se pudo contactar al servidor. Si estabas guardando, " +
            "comprueba los datos antes de repetir la operación."
        );
    }

    let resultado;

    try {
        resultado = await respuesta.json();
    } catch {
        throw new Error(
            "PHP no devolvió una respuesta JSON válida. " +
            "Revisa la conexión y el registro de errores de PHP."
        );
    }

    if (!respuesta.ok) {
        throw new Error(
            resultado.mensaje || "No se pudo completar la operación."
        );
    }

    return resultado;
}

function mostrarPanel(nombre) {
    document.querySelectorAll(".panel").forEach(function (panel) {
        panel.hidden = panel.id !== "panel-" + nombre;
    });

    buscar("mensaje-inicio").hidden = nombre !== "inicio";

    document.querySelectorAll("[data-navegacion]").forEach(function (boton) {
        const seleccionado = boton.dataset.navegacion === nombre;

        if (boton.classList.contains("enlace-menu")) {
            boton.classList.toggle("activo", seleccionado);

            if (seleccionado) {
                boton.setAttribute("aria-current", "page");
            } else {
                boton.removeAttribute("aria-current");
            }
        }

        if (boton.classList.contains("cuadro")) {
            boton.classList.toggle("seleccionado", seleccionado);
            boton.setAttribute("aria-expanded", String(seleccionado));
        }
    });
}

function mostrarVista(boton) {
    const panel = boton.closest(".panel");

    panel.querySelectorAll(".vista").forEach(function (vista) {
        vista.hidden = vista.id !== boton.dataset.vista;
    });

    panel.querySelectorAll("[data-vista]").forEach(function (opcion) {
        const seleccionada = opcion === boton;

        opcion.classList.toggle("activo", seleccionada);
        opcion.setAttribute("aria-expanded", String(seleccionada));
    });
}

document.querySelectorAll("[data-navegacion]").forEach(function (boton) {
    boton.addEventListener("click", function () {
        mostrarPanel(boton.dataset.navegacion);
    });
});

document.querySelectorAll("[data-vista]").forEach(function (boton) {
    boton.setAttribute("aria-controls", boton.dataset.vista);
    boton.setAttribute("aria-expanded", "false");

    boton.addEventListener("click", function () {
        mostrarVista(boton);
    });
});

function pintarTabla(id, filas, columnas, textoVacio = "No hay registros.") {
    const cuerpo = buscar(id);

    cuerpo.replaceChildren();

    if (filas.length === 0) {
        const fila = document.createElement("tr");
        const celda = document.createElement("td");

        celda.colSpan = columnas;
        celda.textContent = textoVacio;

        fila.appendChild(celda);
        cuerpo.appendChild(fila);

        return;
    }

    filas.forEach(function (valores) {
        const fila = document.createElement("tr");

        valores.forEach(function (valor) {
            const celda = document.createElement("td");

            if (valor instanceof Node) {
                celda.appendChild(valor);
            } else {
                celda.textContent = valor;
            }

            fila.appendChild(celda);
        });

        cuerpo.appendChild(fila);
    });
}

function pintarSelector(id, elementos, clave) {
    const selector = buscar(id);
    const anterior = selector.value;

    selector.replaceChildren();

    const inicial = document.createElement("option");
    inicial.value = "";
    inicial.textContent = elementos.length
        ? "Selecciona una opción"
        : "No hay registros disponibles";

    selector.appendChild(inicial);

    elementos.forEach(function (elemento) {
        const opcion = document.createElement("option");

        opcion.value = elemento[clave];
        opcion.textContent = elemento.codigo
            ? elemento.codigo + " — " + elemento.nombre
            : elemento.nombre;

        selector.appendChild(opcion);
    });

    if (elementos.some(elemento => String(elemento[clave]) === anterior)) {
        selector.value = anterior;
    }
}

function rellenarEdicion() {
    const producto = datos?.productos.find(function (producto) {
        return String(producto.id_producto) === buscar("editar-producto").value;
    });

    buscar("editar-nombre").value = producto ? producto.nombre : "";
    buscar("editar-precio").value = producto ? producto.precio : "";
    buscar("editar-minimo").value = producto ? producto.stock_minimo : "";
}

function rellenarStock() {
    const producto = datos?.productos.find(function (producto) {
        return String(producto.id_producto) === buscar("producto-stock").value;
    });

    buscar("stock-anterior").value = producto ? producto.existencias : "";
    buscar("nuevo-stock").value = producto ? producto.existencias : "";

    buscar("stock-observado").textContent = producto
        ? "Existencias consultadas: " + producto.existencias
        : "";
}

function pintarDatos() {
    const productos = datos.productos;
    const sucursal = datos.sucursal;

    buscar("nombre-sucursal").textContent = sucursal.nombre;
    buscar("sucursal-lateral").textContent = sucursal.nombre;

    buscar("resumen-productos").textContent =
        productos.length + " productos activos";

    const cajasActivas = datos.cajas.filter(caja => Number(caja.activa) === 1);

    buscar("resumen-cajas").textContent =
        cajasActivas.length + " cajas activas";

    pintarTabla(
        "tabla-productos",
        productos.map(producto => [
            producto.codigo,
            producto.nombre,
            moneda(producto.precio)
        ]),
        3
    );

    pintarTabla(
        "tabla-inventario",
        productos.map(producto => [
            producto.codigo,
            producto.nombre,
            producto.existencias,
            producto.stock_minimo
        ]),
        4
    );

    pintarTabla(
        "tabla-stock-bajo",
        productos
            .filter(producto =>
                Number(producto.existencias) <= Number(producto.stock_minimo)
            )
            .map(producto => [
                producto.nombre,
                producto.existencias,
                producto.stock_minimo
            ]),
        3,
        "No hay productos con stock bajo."
    );

    pintarSelector("editar-producto", productos, "id_producto");
    pintarSelector("producto-eliminar", productos, "id_producto");
    pintarSelector("producto-stock", productos, "id_producto");
    pintarSelector("caja-eliminar", cajasActivas, "id_caja");

    rellenarEdicion();
    rellenarStock();

    pintarTabla(
        "tabla-cajas",
        datos.cajas.map(caja => [
            caja.nombre,
            Number(caja.activa) === 1 ? "Activa" : "Inactiva"
        ]),
        2
    );

    const lista = buscar("datos-sucursal-lista");

    lista.replaceChildren();

    const campos = [
        ["Nombre", sucursal.nombre],
        ["Dirección", sucursal.direccion],
        ["Teléfono", sucursal.telefono || "Sin registrar"],
        ["Contacto", sucursal.contacto || "Sin registrar"],
        ["Estado", sucursal.estado]
    ];

    campos.forEach(function ([nombre, valor]) {
        const termino = document.createElement("dt");
        const descripcion = document.createElement("dd");

        termino.textContent = nombre;
        descripcion.textContent = valor;

        lista.append(termino, descripcion);
    });

    pintarTabla(
        "tabla-ventas",
        datos.ventas.map(function (venta) {
            const boton = document.createElement("button");

            boton.type = "button";
            boton.textContent = "Ver detalle";

            boton.addEventListener("click", function () {
                mostrarDetalleVenta(venta.id_venta);
            });

            return [
                venta.id_venta,
                venta.fecha,
                venta.caja,
                venta.cajero,
                moneda(venta.total),
                boton
            ];
        }),
        6,
        "Todavía no hay ventas registradas."
    );
}

async function cargarDatos() {
    const resultado = await solicitar("datos");

    datos = resultado;
    token = resultado.token;

    pintarDatos();
}

async function refrescar() {
    if (ocupado) {
        return;
    }

    bloquear(true);
    mensaje("Consultando la base de datos...");

    try {
        await cargarDatos();
        mensaje("Datos actualizados.");
    } catch (error) {
        mensaje(error.message, true);
    } finally {
        bloquear(false);
    }
}

buscar("actualizar-datos").addEventListener("click", refrescar);
buscar("editar-producto").addEventListener("change", rellenarEdicion);
buscar("producto-stock").addEventListener("change", rellenarStock);

document.querySelectorAll("form[data-accion]").forEach(function (formulario) {
    formulario.addEventListener("submit", async function (evento) {
        evento.preventDefault();

        if (ocupado || !datos || !formulario.reportValidity()) {
            return;
        }

        const accion = formulario.dataset.accion;

        if (
            accion === "eliminar_producto" ||
            accion === "eliminar_caja"
        ) {
            const confirmado = confirm(
                "¿Confirmas desactivar el registro seleccionado?"
            );

            if (!confirmado) {
                return;
            }
        }

        const valores = new FormData(formulario);

        bloquear(true);
        mensaje("Guardando...");

        try {
            const resultado = await solicitar(accion, valores);

            formulario.reset();

            try {
                await cargarDatos();
                mensaje(resultado.mensaje);
            } catch (error) {
                mensaje(
                    resultado.mensaje +
                    " No se pudo actualizar la vista: " +
                    error.message,
                    true
                );
            }

        } catch (error) {
            mensaje(error.message, true);
        } finally {
            bloquear(false);
        }
    });
});

async function mostrarDetalleVenta(id) {
    try {
        const respuesta = await fetch(
            "gerente_api.php?accion=detalle_venta&id=" +
            encodeURIComponent(id),
            {
                cache: "no-store",
                credentials: "same-origin"
            }
        );

        const resultado = await respuesta.json();

        if (!respuesta.ok) {
            throw new Error(resultado.mensaje || "No se pudo cargar la venta.");
        }

        buscar("titulo-detalle").textContent =
            "Venta #" + resultado.venta.id_venta;

        buscar("resumen-venta").textContent =
            resultado.venta.fecha +
            " — Total: " +
            moneda(resultado.venta.total);

        pintarTabla(
            "tabla-detalle",
            resultado.detalle.map(item => [
                item.nombre,
                item.cantidad,
                moneda(item.precio_unitario),
                moneda(Number(item.cantidad) * Number(item.precio_unitario))
            ]),
            4
        );

        const dialogo = buscar("dialogo-venta");

        if (!dialogo.open) {
            dialogo.showModal();
        }

    } catch (error) {
        mensaje("No se pudo mostrar el detalle: " + error.message, true);
    }
}

buscar("cerrar-detalle").addEventListener("click", function () {
    buscar("dialogo-venta").close();
});

refrescar();