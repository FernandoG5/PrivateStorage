(() => {
    const menuButton = document.querySelector('.menu-movil');
    const menu = document.getElementById('menu-navegacion');
    const notificationButton = document.querySelector('.notificaciones-toggle');
    const notificationPanel = document.getElementById('panel-notificaciones');
    const closeNotificationsButton = document.querySelector('.cerrar-notificaciones');

    const closeMobileMenu = () => {
        document.body.classList.remove('menu-movil-abierto');
        menuButton.setAttribute('aria-expanded', 'false');
        menuButton.setAttribute('aria-label', 'Abrir menú');
        menuButton.innerHTML = '<i class="fa-solid fa-bars" aria-hidden="true"></i>';
    };

    menuButton.addEventListener('click', () => {
        const isExpanded = menuButton.getAttribute('aria-expanded') === 'true';
        document.body.classList.toggle('menu-movil-abierto', !isExpanded);
        menuButton.setAttribute('aria-expanded', String(!isExpanded));
        menuButton.setAttribute('aria-label', isExpanded ? 'Abrir menú' : 'Cerrar menú');
        menuButton.innerHTML = `<i class="fa-solid fa-${isExpanded ? 'bars' : 'xmark'}" aria-hidden="true"></i>`;
    });

    menu.addEventListener('click', event => {
        if (event.target.closest('.nav-item')) {
            closeMobileMenu();
        }
    });

    const setNotificationsOpen = isOpen => {
        notificationPanel.hidden = !isOpen;
        notificationButton.setAttribute('aria-expanded', String(isOpen));
        notificationButton.setAttribute(
            'aria-label',
            isOpen ? 'Cerrar notificaciones' : 'Abrir notificaciones',
        );
    };

    notificationButton.addEventListener('click', () => {
        const isOpen = notificationButton.getAttribute('aria-expanded') === 'true';
        setNotificationsOpen(!isOpen);
    });

    closeNotificationsButton.addEventListener('click', () => setNotificationsOpen(false));

    document.addEventListener('click', event => {
        if (
            !notificationPanel.hidden
            && !notificationPanel.contains(event.target)
            && !notificationButton.contains(event.target)
        ) {
            setNotificationsOpen(false);
        }
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            setNotificationsOpen(false);
            closeMobileMenu();
        }
    });

    document.querySelectorAll('.tarjeta-borde[role="button"]').forEach(card => {
        card.addEventListener('keydown', event => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                card.click();
            }
        });
    });

    requestAnimationFrame(() => {
        document.querySelectorAll('.ranking-track i').forEach(bar => {
            bar.classList.add('progreso-cargado');
        });
    });
})();
