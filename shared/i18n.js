(() => {
    const traducciones = {
        'Inicio de sesión': 'Log in',
        'Iniciar sesión': 'Log in',
        'Iniciar Sesión': 'Log In',
        'Recuperar contraseña': 'Recover password',
        'Restablecer contraseña': 'Reset password',
        'Nueva contraseña': 'New password',
        'Confirmar contraseña': 'Confirm password',
        'Sistema de gestión': 'Management system',
        'Accede con tus credenciales para continuar.': 'Sign in with your credentials to continue.',
        'Ingresa tus datos para acceder al sistema.': 'Enter your details to access the system.',
        'No. de trabajador': 'Employee ID',
        'Ingresa tu número de trabajador': 'Enter your employee ID',
        'Ingresa tu contraseña': 'Enter your password',
        'Mostrar contraseña': 'Show password',
        'Ocultar contraseña': 'Hide password',
        '¿Olvidaste tu contraseña?': 'Forgot your password?',
        'El usuario y la contraseña son obligatorios.': 'Username and password are required.',
        'Usuario o contraseña incorrectos.': 'Incorrect username or password.',
        'La cuenta está desactivada.': 'The account is disabled.',
        'No se pudo completar la autenticación.': 'Authentication could not be completed.',
        'Ocurrió un error interno del servidor.': 'An internal server error occurred.',
        'Solamente se permite el método POST.': 'Only the POST method is allowed.',
        'El correo electrónico es obligatorio.': 'The email address is required.',
        'El correo electrónico no es válido.': 'The email address is not valid.',
        'No se pudo procesar la solicitud.': 'The request could not be processed.',
        'Intenta más tarde.': 'Try again later.',
        'Los datos enviados no son válidos.': 'The submitted data is not valid.',
        'Tu contraseña se actualizó correctamente.': 'Your password was updated successfully.',
        'Redirigiendo al inicio de sesión...': 'Redirecting to log in...',
        'No se pudo conectar con el servidor. Intenta nuevamente.': 'Could not connect to the server. Please try again.',
        'No se pudo iniciar sesión.': 'Could not log in.',
        'No se pudo conectar con el servidor. Verifica que Apache esté iniciado y vuelve a intentar.': 'Could not connect to the server. Make sure Apache is running and try again.',
        'Ocurrió un error inesperado. Consulta la consola para más detalles.': 'An unexpected error occurred. Check the console for details.',
        'Tema oscuro': 'Dark theme',
        'Tema claro': 'Light theme',
        'Activar modo oscuro': 'Enable dark mode',
        'Activar modo claro': 'Enable light mode',
        'Te enviaremos un enlace para elegir una nueva contraseña.': 'We will send you a link to choose a new password.',
        'Escribe el correo electrónico registrado en tu cuenta.': 'Enter the email address registered to your account.',
        'Correo electrónico': 'Email address',
        'Enviar enlace': 'Send link',
        'Volver a iniciar sesión': 'Back to log in',
        'Elige una contraseña nueva para tu cuenta.': 'Choose a new password for your account.',
        'Debe tener al menos 8 caracteres.': 'It must contain at least 8 characters.',
        'La contraseña debe tener al menos 8 caracteres.': 'The password must contain at least 8 characters.',
        'Escribe tu nueva contraseña': 'Enter your new password',
        'Repite tu nueva contraseña': 'Repeat your new password',
        'Guardar contraseña': 'Save password',
        'Solicitar un enlace nuevo': 'Request a new link',
        'Cambiar idioma': 'Change language',
        'Cambiar idioma a inglés': 'Change language to English',
        'Cambiar idioma a español': 'Change language to Spanish',
        'Panel del gerente': 'Manager panel',
        'Menú del gerente': 'Manager menu',
        'Cargando gerente...': 'Loading manager...',
        'Cargando sucursal...': 'Loading branch...',
        'PANEL DE CONTROL': 'CONTROL PANEL',
        'Actualizar datos': 'Refresh data',
        'Inicio': 'Home',
        'Productos': 'Products',
        'Sucursal': 'Branch',
        'Sucursales': 'Branches',
        'Cajeros': 'Cashiers',
        'Administrador': 'Administrator',
        'Cajero': 'Cashier',
        'Gerente': 'Manager',
        'Bienvenido, ': 'Welcome, ',
        'Cargando': 'Loading',
        'Cargando inventario…': 'Loading inventory…',
        'Cargando ventas…': 'Loading sales…',
        'Cerrar sesión': 'Log out',
        'Información, cajas y ventas': 'Branch, cash registers and sales information',
        'Cargando inventario...': 'Loading inventory...',
        'Cargando cajeros...': 'Loading cashiers...',
        'Selecciona Productos o Sucursal.': 'Select Products or Branch.',
        'INVENTARIO': 'INVENTORY',
        'Agregar producto': 'Add product',
        'Buscar...': 'Search...',
        'Buscar por nombre o código': 'Search by name or code',
        'Buscar por nombre o código...': 'Search by name or code...',
        'Filtrar productos': 'Filter products',
        'Todos': 'All',
        'Stock bajo': 'Low stock',
        'Código': 'Code',
        'Nombre': 'Name',
        'Precio unitario': 'Unit price',
        'Precio de venta': 'Sale price',
        'Precio': 'Price',
        'Existencias': 'Stock',
        'Existencias iniciales': 'Initial stock',
        'Existencias totales': 'Total stock',
        'Nueva existencia total': 'New total stock',
        'Mínimo': 'Minimum',
        'Stock mínimo': 'Minimum stock',
        'Valor stock': 'Stock value',
        'Acciones': 'Actions',
        'Guardar producto': 'Save product',
        'Guardar cambios': 'Save changes',
        'Editar producto': 'Edit product',
        'Eliminar producto': 'Delete product',
        'Actualizar existencias': 'Update stock',
        'Productos con stock bajo': 'Products with low stock',
        'Productos con poco stock': 'Products with low stock',
        'Últimas ventas': 'Latest sales',
        'RENDIMIENTO': 'PERFORMANCE',
        'Top sucursales por ventas': 'Top branches by sales',
        'NAVEGACIÓN': 'NAVIGATION',
        'Accesos rápidos': 'Quick access',
        'Ver inventario': 'View inventory',
        'Consultar ventas': 'View sales',
        'Abrir sucursales': 'Open branches',
        'Administrar gerentes': 'Manage managers',
        'PRODUCTOS Y EXISTENCIAS': 'PRODUCTS AND STOCK',
        'Control de inventario': 'Inventory control',
        'Consulta de productos por sucursal y categoría.': 'Browse products by branch and category.',
        'Filtrar por sucursal': 'Filter by branch',
        'Filtrar por categoría': 'Filter by category',
        'Todas las sucursales': 'All branches',
        'Selecciona una categoría': 'Select a category',
        'Selecciona una sucursal': 'Select a branch',
        'Nombre del producto': 'Product name',
        'No. Sucursal': 'Branch no.',
        'Mostrar': 'Show',
        'por página': 'per page',
        'REGISTRO Y SEGUIMIENTO': 'RECORDS AND TRACKING',
        'Consulta de ventas': 'Sales overview',
        'Historial de ventas y detalle de tickets registrados.': 'Sales history and registered receipt details.',
        'Total vendido': 'Total sold',
        'Número de ventas': 'Number of sales',
        'Ticket promedio': 'Average receipt',
        'Producto más vendido': 'Best-selling product',
        'En el periodo seleccionado': 'For the selected period',
        'Por venta': 'Per sale',
        'Desde': 'From',
        'Hasta': 'To',
        'Hoy': 'Today',
        'Últimos 7 días': 'Last 7 days',
        'Este mes': 'This month',
        'Todo': 'All time',
        'Limpiar filtros': 'Clear filters',
        'Folio o cajero': 'Receipt or cashier',
        'Buscar folio o cajero': 'Search receipt or cashier',
        'Atajos de periodo': 'Period shortcuts',
        'Ventas registradas': 'Recorded sales',
        'Fecha y hora': 'Date and time',
        'Fecha desde': 'Start date',
        'Fecha hasta': 'End date',
        'Filtrar ventas por sucursal': 'Filter sales by branch',
        'Ventas por página': 'Sales per page',
        'unidades vendidas': 'units sold',
        'Sin ventas en el periodo': 'No sales in the period',
        'No hay productos con stock bajo.': 'There are no low-stock products.',
        'Stock bajo: ': 'Low stock: ',
        ' unidades restantes.': ' units remaining.',
        ' de umbral ': ' of threshold ',
        'Restante': 'Remaining',
        'restante': 'remaining',
        'Gestión de Gerentes': 'Manager management',
        'Listado y asignación de gerentes por tienda.': 'List and assign managers by store.',
        'Buscar gerente': 'Search manager',
        'Nombre o usuario': 'Name or username',
        'Sucursal asignada': 'Assigned branch',
        'No se pudieron cargar los gerentes.': 'Managers could not be loaded.',
        'No se pudieron cargar los gerentes. Revisa tu conexión e intenta nuevamente.': 'Managers could not be loaded. Check your connection and try again.',
        'No hay sucursales que mostrar': 'There are no branches to display',
        'No hay sucursales que coincidan con la búsqueda': 'No branches match the search',
        'Activa': 'Active',
        'Inactiva': 'Inactive',
        'activo': 'active',
        'activos': 'active',
        'activa': 'active',
        'activas': 'active',
        'inactivo': 'inactive',
        'inactivos': 'inactive',
        'inactiva': 'inactive',
        'inactivas': 'inactive',
        'Todas': 'All',
        'Gestión de Sucursales': 'Branch management',
        'Administra las sedes y su información de contacto.': 'Manage branches and their contact information.',
        'Estado': 'Status',
        'Filtrar': 'Filter',
        'ADMINISTRACIÓN DE SEDES': 'BRANCH ADMINISTRATION',
        'Gestión de Sucursales': 'Branch management',
        'Administra las sedes y su información de contacto.': 'Manage branches and their contact information.',
        'Agregar sucursal': 'Add branch',
        'Activas': 'Active',
        'Inactivas': 'Inactive',
        'Buscar sucursal': 'Search branch',
        'Nombre o dirección': 'Name or address',
        'Aún no hay sucursales registradas': 'No branches have been registered yet',
        'Guardar sucursal': 'Save branch',
        'Nombre de sucursal': 'Branch name',
        'Imagen (JPG, PNG o WEBP, máximo 2 MB)': 'Image (JPG, PNG or WEBP, maximum 2 MB)',
        'Vista previa de la imagen del producto': 'Product image preview',
        'Productos activos': 'Active products',
        'Total de productos': 'Total products',
        'Total de sucursales': 'Total branches',
        'Total de gerentes': 'Total managers',
        'Vistazo general': 'Overview',
        'RESUMEN DEL SISTEMA': 'SYSTEM SUMMARY',
        'CENTRO DE ACTIVIDAD': 'ACTIVITY CENTER',
        'Notificaciones': 'Notifications',
        'No hay alertas de stock bajo.': 'There are no low-stock alerts.',
        'Ventas': 'Sales',
        'PUNTO DE VENTA': 'POINT OF SALE',
        'Venta': 'Sale',
        'VENTA': 'SALE',
        'Punto de venta': 'Point of sale',
        'Registra una compra y actualiza las existencias.': 'Record a purchase and update stock.',
        'Categorías': 'Categories',
        'PRODUCTOS': 'PRODUCTS',
        'Venta actual': 'Current sale',
        'Carrito': 'Cart',
        'Agrega productos para comenzar la venta.': 'Add products to start the sale.',
        'Subtotal': 'Subtotal',
        'Total': 'Total',
        'Método de pago': 'Payment method',
        'Efectivo': 'Cash',
        'Tarjeta': 'Card',
        'Transferencia': 'Transfer',
        'Confirmar venta': 'Confirm sale',
        'CONTROL DE EXISTENCIAS': 'STOCK CONTROL',
        'Inventario': 'Inventory',
        'Existencias disponibles. Vista de solo lectura para el cajero.': 'Available stock. Read-only view for the cashier.',
        'Categoría': 'Category',
        'Todas las categorías': 'All categories',
        'Producto': 'Product',
        'Cantidad': 'Quantity',
        'Disponibilidad': 'Availability',
        'Disponible': 'Available',
        'Agotado': 'Out of stock',
        'Estado': 'Status',
        'Cancelar': 'Cancel',
        'Confirmar': 'Confirm',
        'Cerrar': 'Close',
        '¿Confirmar la venta?': 'Confirm the sale?',
        'CONFIRMAR VENTA': 'CONFIRM SALE',
        'Venta confirmada': 'Sale confirmed',
        'VENTA CONFIRMADA': 'SALE CONFIRMED',
        'Total pagado': 'Total paid',
        'Anterior': 'Previous',
        'Siguiente': 'Next',
        'Filas por página': 'Rows per page',
        'Mostrando': 'Showing',
        'Activo': 'Active',
        'Inactivo': 'Inactive',
        'No hay productos que coincidan con los filtros.': 'No products match the filters.',
        'No se pudieron cargar los productos.': 'Products could not be loaded.',
        'No se pudieron cargar los cajeros.': 'Cashiers could not be loaded.',
        'No se pudo cargar el inventario.': 'Inventory could not be loaded.',
        'No se pudieron cargar las ventas.': 'Sales could not be loaded.',
        'No hay ventas en este periodo.': 'There are no sales in this period.',
        'No se pudo cerrar la sesión. Intenta nuevamente.': 'Could not log out. Please try again.',
        'No se pudo comunicar con el servidor.': 'Could not communicate with the server.',
        'No se pudo actualizar el inventario.': 'Could not update inventory.',
        'Cargando ventas…': 'Loading sales…',
        'Detalles': 'Details',
        'Detalle': 'Details',
        'Fecha': 'Date',
        'Importe': 'Amount',
        'Dashboard': 'Dashboard',
        'Gerentes': 'Managers',
        'Agregar gerente': 'Add manager',
        'Guardar gerente': 'Save manager',
        'Desactivar gerente': 'Deactivate manager',
        'Editar gerente': 'Edit manager',
        'CONFIRMACIÓN': 'CONFIRMATION',
        'No disponible': 'Unavailable',
        'Nombre completo': 'Full name',
        'Nueva contraseña': 'New password',
        'Teléfono': 'Phone',
        'Dirección': 'Address'
        ,'ADMINISTRACIÓN DE SEDE': 'BRANCH ADMINISTRATION'
        ,'ADMINISTRACIÓN DE SEDES': 'BRANCH ADMINISTRATION'
        ,'ADMINISTRACION DE SEDE': 'BRANCH ADMINISTRATION'
        ,'ADMINISTRACION DE SEDES': 'BRANCH ADMINISTRATION'
        ,'ADMINISTRACIÓN DE PERSONAL': 'STAFF ADMINISTRATION'
        ,'PERSONAL DE CAJA': 'CASHIER STAFF'
        ,'Mi sucursal': 'My branch'
        ,'FICHA DE SUCURSAL': 'BRANCH DETAILS'
        ,'Datos de la sucursal': 'Branch details'
        ,'Registrar caja': 'Register cash register'
        ,'Consultar cajas': 'View cash registers'
        ,'Desactivar caja': 'Deactivate cash register'
        ,'Cajas': 'Cash registers'
        ,'Cajas de la sucursal': 'Branch cash registers'
        ,'Nombre de la caja': 'Cash register name'
        ,'Guardar caja': 'Save cash register'
        ,'Aún no hay cajas registradas.': 'No cash registers have been added yet.'
        ,'Aún no hay ventas en esta sucursal.': 'There are no sales at this branch yet.'
        ,'Historial de ventas': 'Sales history'
        ,'Folio': 'Receipt'
        ,'Caja': 'Cash register'
        ,'Fecha de creación': 'Created'
        ,'Agregar cajero': 'Add cashier'
        ,'Crear cajero': 'Create cashier'
        ,'Consultar cajeros': 'View cashiers'
        ,'Editar cajero': 'Edit cashier'
        ,'Buscar por nombre o usuario': 'Search by name or username'
        ,'Buscar productos': 'Search products'
        ,'Buscar en el inventario': 'Search inventory'
        ,'Activos': 'Active'
        ,'Inactivos': 'Inactive'
        ,'Total': 'Total'
        ,'Filtrar cajeros': 'Filter cashiers'
        ,'Déjalo vacío para conservar la actual': 'Leave blank to keep the current one'
        ,'Guardar cajero': 'Save cashier'
        ,'No hay cajeros que coincidan con los filtros.': 'No cashiers match the filters.'
        ,'Aún no hay cajeros registrados.': 'No cashiers have been registered yet.'
        ,'Aún no hay productos registrados.': 'No products have been registered yet.'
        ,'Aún no hay productos en el inventario.': 'No products are in the inventory yet.'
        ,'No se encontraron productos.': 'No products found.'
        ,'No se encontraron productos con esos filtros.': 'No products found with those filters.'
        ,'Resultados de búsqueda': 'Search results'
        ,'BÚSQUEDA': 'SEARCH'
        ,'CATEGORÍA': 'CATEGORY'
        ,'Categoría': 'Category'
        ,'Sin categoría': 'Uncategorized'
        ,'Selecciona una caja': 'Select a cash register'
        ,'Selecciona un producto': 'Select a product'
        ,'Eliminar producto del catálogo': 'Remove product from catalog'
        ,'Se desactivará para conservar sus registros de ventas.': 'It will be deactivated to preserve its sales records.'
        ,'El registro de cajas estará disponible próximamente.': 'Cash register management will be available soon.'
        ,'CONFIRMAR ACCIÓN': 'CONFIRM ACTION'
        ,'¿Continuar?': 'Continue?'
        ,'Desactivar producto': 'Deactivate product'
        ,'Desactivar': 'Deactivate'
        ,'Activar': 'Activate'
        ,'¿Desactivar ': 'Deactivate '
        ,' del catálogo?': ' from the catalog?'
        ,'Datos actualizados correctamente.': 'Data updated successfully.'
        ,'No se pudo recordar el tamaño de página en este navegador.': 'Could not remember the page size in this browser.'
        ,'No se pudo guardar la preferencia de tema.': 'Could not save the theme preference.'
        ,'No se pudo editar el cajero.': 'Could not edit the cashier.'
        ,'No se pudo guardar la preferencia de idioma.': 'Could not save the language preference.'
        ,'Cerrar confirmación': 'Close confirmation'
        ,'Cerrar formulario de sucursal': 'Close branch form'
        ,'Página anterior': 'Previous page'
        ,'Página siguiente': 'Next page'
        ,'Página ': 'Page '
        ,'Filas por página': 'Rows per page'
        ,'Mostrando ': 'Showing '
        ,'No hay ventas en este periodo': 'There are no sales in this period'
        ,'No hay ventas en este periodo.': 'There are no sales in this period.'
        ,'No se pudieron cargar las sucursales.': 'Branches could not be loaded.'
        ,'No se pudo cargar el contexto.': 'The context could not be loaded.'
        ,'No se pudo confirmar la venta.': 'Could not confirm the sale.'
        ,'La venta se confirmó, pero no se pudo actualizar el inventario en pantalla.': 'The sale was confirmed, but the inventory on screen could not be updated.'
        ,'No se pudo guardar la cantidad de filas por página.': 'Could not save the number of rows per page.'
        ,'No se pudieron cargar las categorías.': 'Categories could not be loaded.'
        ,'Aún no hay categorías registradas.': 'No categories have been registered yet.'
        ,'No se encontraron productos con esos filtros.': 'No products found with those filters.'
        ,'¿Confirmar esta venta por ': 'Confirm this sale for '
        ,'¿Confirmar la venta?': 'Confirm the sale?'
        ,'CONFIRMAR VENTA': 'CONFIRM SALE'
        ,'Venta confirmada': 'Sale confirmed'
        ,'VENTA CONFIRMADA': 'SALE CONFIRMED'
        ,'Total pagado': 'Total paid'
        ,'Cargando detalle de venta…': 'Loading sale details…'
        ,'No se pudo cargar el detalle de la venta.': 'Could not load the sale details.'
        ,'Esta venta no tiene productos en el detalle.': 'This sale has no products in its details.'
        ,'Método de pago': 'Payment method'
        ,'Cerrar ticket': 'Close receipt'
        ,'Aumentar cantidad': 'Increase quantity'
        ,'Disminuir cantidad': 'Decrease quantity'
        ,'Eliminar producto': 'Remove product'
        ,'Disponibilidad': 'Availability'
        ,'Disponible': 'Available'
        ,'Agotado': 'Out of stock'
        ,'Venta registrada': 'Sale recorded'
        ,'No disponible': 'Unavailable'
        ,'Contraseña': 'Password'
        ,'Usuario': 'Username'
        ,'Correo': 'Email'
        ,'No se encontraron productos.': 'No products found.'
        ,'Aún no hay productos en esta categoría.': 'There are no products in this category yet.'
        ,'No hay sucursales que coincidan con la búsqueda': 'No branches match the search'
        ,'No hay gerentes registrados.': 'No managers have been registered.'
        ,'Aún no hay gerentes registrados': 'No managers have been registered yet'
        ,'No se pudieron cargar las sucursales. Revisa tu conexión e intenta nuevamente.': 'Branches could not be loaded. Check your connection and try again.'
        ,'No se pudo cargar la paginación': 'Could not load pagination'
        ,'No se pudieron cargar las ventas.': 'Sales could not be loaded.'
        ,'No se pudieron cargar las alertas de inventario.': 'Inventory alerts could not be loaded.'
        ,'Aún no hay ventas registradas.': 'No sales have been recorded yet.'
        ,'El servidor devolvió una respuesta no válida.': 'The server returned an invalid response.'
        ,'Escribe un correo electrónico válido.': 'Enter a valid email address.'
        ,'El enlace no es válido o ya expiró. Solicita uno nuevo.': 'The link is invalid or has expired. Request a new one.'
        ,'Las contraseñas no coinciden.': 'The passwords do not match.'
        ,'El usuario y la contraseña son obligatorios.': 'Username and password are required.'
        ,'Módulo conectado. Los datos corresponden a tu sucursal.': 'Module connected. The data belongs to your branch.'
        ,'Abrir menú': 'Open menu'
        ,'Cerrar menú': 'Close menu'
        ,'Paginación del inventario': 'Inventory pagination'
        ,'Paginación de sucursales': 'Branch pagination'
        ,'Paginación de gerentes': 'Manager pagination'
        ,'Artículos': 'Items'
        ,'El servidor respondió con un formato inválido. Consulta la consola para más detalles.': 'The server returned an invalid format. Check the console for details.'
    };

    const atributos = ['aria-label', 'aria-valuetext', 'placeholder', 'title'];
    const textosOriginales = new WeakMap();
    const atributosOriginales = new WeakMap();
    const tituloOriginal = document.title;
    const traducirDesdeEspanol = (texto, idioma) => {
        if (idioma === 'es') return texto;
        return Object.entries(traducciones)
            .sort((a, b) => b[0].length - a[0].length)
            .reduce((resultado, [origen, destino]) => resultado.split(origen).join(destino), texto);
    };

    let traduciendo = false;
    const traducirInterfaz = idioma => {
        if (traduciendo) return;
        traduciendo = true;
        if (document.documentElement.lang !== idioma) document.documentElement.lang = idioma;
        const tituloTraducido = traducirDesdeEspanol(tituloOriginal, idioma);
        if (document.title !== tituloTraducido) document.title = tituloTraducido;
        const recorrido = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
        let nodo;
        while ((nodo = recorrido.nextNode())) {
            if (nodo.parentElement && !['SCRIPT', 'STYLE'].includes(nodo.parentElement.tagName)) {
                if (!textosOriginales.has(nodo)) textosOriginales.set(nodo, nodo.nodeValue);
                const textoTraducido = traducirDesdeEspanol(textosOriginales.get(nodo), idioma);
                if (nodo.nodeValue !== textoTraducido) nodo.nodeValue = textoTraducido;
            }
        }
        document.querySelectorAll('*').forEach(elemento => {
            if (!atributosOriginales.has(elemento)) atributosOriginales.set(elemento, new Map());
            const originales = atributosOriginales.get(elemento);
            atributos.forEach(atributo => {
                const valor = elemento.getAttribute(atributo);
                if (valor) {
                    if (!originales.has(atributo)) originales.set(atributo, valor);
                    const valorTraducido = traducirDesdeEspanol(originales.get(atributo), idioma);
                    if (valor !== valorTraducido) elemento.setAttribute(atributo, valorTraducido);
                }
            });
        });
        const boton = document.getElementById('boton-idioma');
        if (boton) {
            const etiqueta = idioma === 'en' ? 'Change language to Spanish' : 'Cambiar idioma a inglés';
            const textoBoton = idioma === 'en' ? 'EN' : 'ES';
            if (boton.textContent !== textoBoton) boton.textContent = textoBoton;
            if (boton.getAttribute('aria-label') !== etiqueta) boton.setAttribute('aria-label', etiqueta);
            if (boton.title !== etiqueta) boton.title = etiqueta;
        }
        traduciendo = false;
    };

    let idiomaActual = 'es';
    try {
        idiomaActual = localStorage.getItem('private-storage-idioma') === 'en' ? 'en' : 'es';
    } catch (error) {
        idiomaActual = 'es';
    }

    const aplicarIdioma = idioma => {
        idiomaActual = idioma;
        traducirInterfaz(idioma);
        try {
            localStorage.setItem('private-storage-idioma', idioma);
        } catch (error) {
            console.warn('Could not save language preference.', error);
        }
        window.dispatchEvent(new CustomEvent('private-storage-language-changed', {
            detail: { idioma }
        }));
    };

    traducirInterfaz(idiomaActual);
    document.getElementById('boton-idioma')?.addEventListener('click', () => {
        aplicarIdioma(idiomaActual === 'es' ? 'en' : 'es');
    });

    new MutationObserver(() => traducirInterfaz(idiomaActual)).observe(document.body, {
        childList: true,
        subtree: true
    });
})();
