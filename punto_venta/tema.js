(() => {
    function iniciarTema() {
        const boton = document.getElementById("boton-tema");
        const pagina = document.documentElement;

        if (!boton) {
            console.error("No se encontró el botón con id boton-tema.");
            return;
        }

        function aplicarTema(tema) {
            const oscuro = tema === "oscuro";

            pagina.setAttribute(
                "data-tema",
                oscuro ? "oscuro" : "claro"
            );

            boton.textContent = oscuro
                ? "Modo claro"
                : "Modo oscuro";

            boton.setAttribute(
                "aria-pressed",
                String(oscuro)
            );
        }

        let tema = window.matchMedia(
            "(prefers-color-scheme: dark)"
        ).matches ? "oscuro" : "claro";

        try {
            const guardado = localStorage.getItem("punto-venta-tema");

            if (guardado === "claro" || guardado === "oscuro") {
                tema = guardado;
            }
        } catch {
        }

        aplicarTema(tema);

        boton.addEventListener("click", function () {
            const nuevoTema = pagina.getAttribute("data-tema") === "oscuro"
                ? "claro"
                : "oscuro";

            aplicarTema(nuevoTema);

            try {
                localStorage.setItem("punto-venta-tema", nuevoTema);
            } catch {
            }
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", iniciarTema);
    } else {
        iniciarTema();
    }
})();