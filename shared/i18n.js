(() => {
    const traducciones = {
        // Autenticación y recuperación
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
        'El servidor respondió con un formato inválido. Consulta la consola para más detalles.': 'The server returned an invalid format. Check the console for details.',
        'El servidor devolvió una respuesta no válida.': 'The server returned an invalid response.',
        'Te enviaremos un enlace para elegir una nueva contraseña.': 'We will send you a link to choose a new password.',
        'Escribe el correo electrónico registrado en tu cuenta.': 'Enter the email address registered to your account.',
        'Escribe un correo electrónico válido.': 'Enter a valid email address.',
        'Correo electrónico': 'Email address',
        'nombre@correo.com': 'name@email.com',
        'Enviar enlace': 'Send link',
        'Volver a iniciar sesión': 'Back to log in',
        'Elige una contraseña nueva para tu cuenta.': 'Choose a new password for your account.',
        'Debe tener al menos 8 caracteres.': 'It must contain at least 8 characters.',
        'La contraseña debe tener al menos 8 caracteres.': 'The password must contain at least 8 characters.',
        'Escribe tu nueva contraseña': 'Enter your new password',
        'Repite tu nueva contraseña': 'Repeat your new password',
        'Guardar contraseña': 'Save password',
        'Solicitar un enlace nuevo': 'Request a new link',
        'El enlace no es válido o ya expiró. Solicita uno nuevo.': 'The link is invalid or has expired. Request a new one.',
        'Las contraseñas no coinciden.': 'The passwords do not match.',

        // Tema, idioma y navegación general
        'Tema oscuro': 'Dark theme',
        'Tema claro': 'Light theme',
        'Modo oscuro': 'Dark mode',
        'Modo claro': 'Light mode',
        'Activar modo oscuro': 'Enable dark mode',
        'Activar modo claro': 'Enable light mode',
        'Cambiar tema': 'Change theme',
        'Cambiar idioma': 'Change language',
        'Cambiar idioma a inglés': 'Change language to English',
        'Cambiar idioma a español': 'Change language to Spanish',
        'PrivateStorage - Panel': 'PrivateStorage - Panel',
        'Private Storage, panel del gerente': 'Private Storage, manager panel',
        'Private Storage, panel': 'Private Storage, panel',
        'Navegación del administrador': 'Administrator navigation',
        'Menú del gerente': 'Manager menu',
        'Menú del cajero': 'Cashier menu',
        'Abrir menú': 'Open menu',
        'Cerrar menú': 'Close menu',
        'Buscar en el panel': 'Search panel',
        'Panel del gerente': 'Manager panel',
        'PANEL DE CONTROL': 'CONTROL PANEL',
        'NAVEGACIÓN': 'NAVIGATION',
        'Accesos rápidos': 'Quick access',
        'Actualizar datos': 'Refresh data',
        'Datos actualizados correctamente.': 'Data updated successfully.',
        'Módulo conectado. Los datos corresponden a tu sucursal.': 'Module connected. The data belongs to your branch.',
        'Inicio': 'Home',
        'Dashboard': 'Dashboard',
        'Productos': 'Products',
        'Sucursal': 'Branch',
        'Sucursales': 'Branches',
        'Cajeros': 'Cashiers',
        'Cajas': 'Cash registers',
        'Gerentes': 'Managers',
        'Ventas': 'Sales',
        'Venta': 'Sale',
        'VENTA': 'SALE',
        'Inventario': 'Inventory',
        'INVENTARIO': 'INVENTORY',
        'Administrador': 'Administrator',
        'Cajero': 'Cashier',
        'Gerente': 'Manager',
        'Bienvenido, ': 'Welcome, ',
        'Cerrar sesión': 'Log out',
        'No se pudo cerrar la sesión. Intenta nuevamente.': 'Could not log out. Please try again.',
        'No se pudo comunicar con el servidor.': 'Could not communicate with the server.',
        'No se pudo comunicar con el servidor. Intenta nuevamente.': 'Could not communicate with the server. Please try again.',

        // Dashboard y notificaciones
        'RESUMEN DEL SISTEMA': 'SYSTEM SUMMARY',
        'Vistazo general': 'Overview',
        'ACTIVIDAD': 'ACTIVITY',
        'CENTRO DE ACTIVIDAD': 'ACTIVITY CENTER',
        'RENDIMIENTO': 'PERFORMANCE',
        'Notificaciones': 'Notifications',
        'Abrir notificaciones': 'Open notifications',
        'Cerrar notificaciones': 'Close notifications',
        'No hay alertas de stock bajo.': 'There are no low-stock alerts.',
        'No se pudieron cargar las alertas de inventario.': 'Inventory alerts could not be loaded.',
        'Productos activos': 'Active products',
        'Total de productos': 'Total products',
        'Total de sucursales': 'Total branches',
        'Total de gerentes': 'Total managers',
        'Productos con stock bajo': 'Products with low stock',
        'Productos con poco stock': 'Products with low stock',
        'No hay productos con stock bajo.': 'There are no low-stock products.',
        'Stock bajo: ': 'Low stock: ',
        ' unidades restantes.': ' units remaining.',
        ' de umbral ': ' of threshold ',
        'Restante': 'Remaining',
        'restante': 'remaining',
        'Últimas ventas': 'Latest sales',
        'Top sucursales por ventas': 'Top branches by sales',
        'Aún no hay ventas registradas.': 'No sales have been recorded yet.',
        'Ver inventario': 'View inventory',
        'Consultar ventas': 'View sales',
        'Abrir sucursales': 'Open branches',
        'Administrar gerentes': 'Manage managers',

        // Inventario y productos
        'PRODUCTOS Y EXISTENCIAS': 'PRODUCTS AND STOCK',
        'Control de inventario': 'Inventory control',
        'Consulta de productos por sucursal y categoría.': 'Browse products by branch and category.',
        'Completa los datos del producto y sus existencias.': 'Fill in the product details and stock.',
        'CONTROL DE EXISTENCIAS': 'STOCK CONTROL',
        'Existencias disponibles. Vista de solo lectura para el cajero.': 'Available stock. Read-only view for the cashier.',
        'Agregar producto': 'Add product',
        'Editar producto': 'Edit product',
        'Modificar producto': 'Modify product',
        'Eliminar producto': 'Delete product',
        'Desactivar producto': 'Deactivate product',
        'Eliminar producto del catálogo': 'Remove product from catalog',
        'Eliminar del catálogo': 'Remove from catalog',
        'Consultar productos': 'View products',
        'Guardar producto': 'Save product',
        'Guardar cambios': 'Save changes',
        'Actualizar existencias': 'Update stock',
        'La nueva cantidad reemplaza el total actual.': 'The new quantity replaces the current total.',
        'Se desactivará para conservar sus registros de ventas.': 'It will be deactivated to preserve its sales records.',
        'Filtrar por sucursal': 'Filter by branch',
        'Filtrar por categoría': 'Filter by category',
        'Filtrar productos': 'Filter products',
        'Todas las sucursales': 'All branches',
        'Todas las categorías': 'All categories',
        'Categorías de productos': 'Product categories',
        'Selecciona una categoría': 'Select a category',
        'Selecciona una sucursal': 'Select a branch',
        'Selecciona un producto': 'Select a product',
        'No. Sucursal': 'Branch no.',
        'Nombre del producto': 'Product name',
        'Código': 'Code',
        'Nombre': 'Name',
        'Categoría': 'Category',
        'Categorías': 'Categories',
        'CATEGORÍA': 'CATEGORY',
        'Sin categoría': 'Uncategorized',
        'Precio unitario': 'Unit price',
        'Precio de venta': 'Sale price',
        'Precio vendido': 'Sale price',
        'Precio': 'Price',
        'Existencias iniciales': 'Initial stock',
        'Existencias totales': 'Total stock',
        'Nueva existencia total': 'New total stock',
        'Existencias': 'Stock',
        'Stock mínimo': 'Minimum stock',
        'Mínimo': 'Minimum',
        'Valor stock': 'Stock value',
        'Stock bajo': 'Low stock',
        'Poco stock': 'Low stock',
        'con stock bajo': 'low-stock products',
        'Imagen (JPG, PNG o WEBP, máximo 2 MB)': 'Image (JPG, PNG or WEBP, maximum 2 MB)',
        'Vista previa de la imagen del producto': 'Product image preview',
        'Buscar productos por nombre o código...': 'Search products by name or code...',
        'Buscar productos por nombre o código': 'Search products by name or code',
        'Buscar por nombre o código...': 'Search by name or code...',
        'Buscar por nombre o código': 'Search by name or code',
        'Buscar productos': 'Search products',
        'Buscar en el inventario': 'Search inventory',
        'Buscar...': 'Search...',
        'No hay productos que coincidan con los filtros.': 'No products match the filters.',
        'No se encontraron productos con esos filtros.': 'No products found with those filters.',
        'No se encontraron productos.': 'No products found.',
        'Aún no hay productos en esta categoría.': 'There are no products in this category yet.',
        'Aún no hay productos registrados.': 'No products have been registered yet.',
        'Aún no hay productos en el inventario.': 'No products are in the inventory yet.',
        'Aún no hay categorías registradas.': 'No categories have been registered yet.',
        'No se pudieron cargar los productos.': 'Products could not be loaded.',
        'No se pudieron cargar las categorías.': 'Categories could not be loaded.',
        'No se pudo cargar el inventario.': 'Inventory could not be loaded.',
        'No se pudo actualizar el inventario.': 'Could not update inventory.',

        // Ventas y punto de venta
        'REGISTRO Y SEGUIMIENTO': 'RECORDS AND TRACKING',
        'HISTORIAL': 'HISTORY',
        'COMPROBANTE': 'RECEIPT',
        'Consulta de ventas': 'Sales overview',
        'Historial de ventas': 'Sales history',
        'Historial de ventas y detalle de tickets registrados.': 'Sales history and registered receipt details.',
        'Resumen del periodo': 'Period summary',
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
        'unidades vendidas': 'units sold',
        'Sin ventas en el periodo': 'No sales in the period',
        'No hay ventas en este periodo.': 'There are no sales in this period.',
        'No hay ventas en este periodo': 'There are no sales in this period',
        'No se pudieron cargar las ventas.': 'Sales could not be loaded.',
        'Detalle de venta': 'Sale details',
        'Cerrar detalle de venta': 'Close sale details',
        'Cargando detalle de venta…': 'Loading sale details…',
        'No se pudo cargar el detalle de la venta.': 'Could not load the sale details.',
        'Esta venta no tiene productos en el detalle.': 'This sale has no products in its details.',
        'PUNTO DE VENTA': 'POINT OF SALE',
        'Punto de venta': 'Point of sale',
        'Registra una compra y actualiza las existencias.': 'Record a purchase and update stock.',
        'PRODUCTOS': 'PRODUCTS',
        'Venta actual': 'Current sale',
        'Carrito': 'Cart',
        'Agrega productos para comenzar la venta.': 'Add products to start the sale.',
        'Subtotal': 'Subtotal',
        'Total': 'Total',
        'Total pagado': 'Total paid',
        'Método de pago': 'Payment method',
        'Efectivo': 'Cash',
        'Tarjeta': 'Card',
        'Transferencia': 'Transfer',
        'Confirmar venta': 'Confirm sale',
        'CONFIRMAR VENTA': 'CONFIRM SALE',
        '¿Confirmar la venta?': 'Confirm the sale?',
        '¿Confirmar esta venta por ': 'Confirm this sale for ',
        'Venta confirmada': 'Sale confirmed',
        'VENTA CONFIRMADA': 'SALE CONFIRMED',
        'Venta registrada': 'Sale recorded',
        'No se pudo confirmar la venta.': 'Could not confirm the sale.',
        'La venta se confirmó, pero no se pudo actualizar el inventario en pantalla.': 'The sale was confirmed, but the inventory on screen could not be updated.',
        'Cerrar ticket': 'Close receipt',
        'Aumentar cantidad': 'Increase quantity',
        'Disminuir cantidad': 'Decrease quantity',
        'Disponibilidad': 'Availability',
        'Disponible': 'Available',
        'Agotado': 'Out of stock',
        'Artículos': 'Items',
        'Cantidad': 'Quantity',
        'Importe': 'Amount',
        'Folio': 'Receipt',
        'Fecha': 'Date',
        'Detalles': 'Details',
        'Detalle': 'Details',

        // Sucursales, gerentes, cajeros y cajas
        'ADMINISTRACIÓN DE SEDES': 'BRANCH ADMINISTRATION',
        'ADMINISTRACIÓN DE SEDE': 'BRANCH ADMINISTRATION',
        'ADMINISTRACION DE SEDES': 'BRANCH ADMINISTRATION',
        'ADMINISTRACION DE SEDE': 'BRANCH ADMINISTRATION',
        'Gestión de Sucursales': 'Branch management',
        'Administra las sedes y su información de contacto.': 'Manage branches and their contact information.',
        'Resumen de sucursales': 'Branches summary',
        'Agregar sucursal': 'Add branch',
        'Guardar sucursal': 'Save branch',
        'Eliminar sucursal': 'Delete branch',
        'Cerrar formulario de sucursal': 'Close branch form',
        'Nombre de sucursal': 'Branch name',
        'Buscar sucursal': 'Search branch',
        'Nombre o dirección': 'Name or address',
        'Aún no hay sucursales registradas': 'No branches have been registered yet',
        'No hay sucursales que mostrar': 'There are no branches to display',
        'No hay sucursales que coincidan con la búsqueda': 'No branches match the search',
        'No se pudieron cargar las sucursales.': 'Branches could not be loaded.',
        'No se pudieron cargar las sucursales. Revisa tu conexión e intenta nuevamente.': 'Branches could not be loaded. Check your connection and try again.',
        '¿Eliminar definitivamente la sucursal ': 'Permanently delete branch ',
        'Esta acción no se puede deshacer.': 'This action cannot be undone.',
        'FICHA DE SUCURSAL': 'BRANCH DETAILS',
        'Datos de la sucursal': 'Branch details',
        'Información, cajas y ventas': 'Branch, cash registers and sales information',
        'Mi sucursal': 'My branch',
        'Registrar caja': 'Register cash register',
        'Consultar cajas': 'View cash registers',
        'Desactivar caja': 'Deactivate cash register',
        'Cajas de la sucursal': 'Branch cash registers',
        'Nombre de la caja': 'Cash register name',
        'Guardar caja': 'Save cash register',
        'Selecciona una caja': 'Select a cash register',
        'Caja': 'Cash register',
        'Aún no hay cajas registradas.': 'No cash registers have been added yet.',
        'Aún no hay ventas en esta sucursal.': 'There are no sales at this branch yet.',
        'El registro de cajas estará disponible próximamente.': 'Cash register management will be available soon.',
        'Gestión de Gerentes': 'Manager management',
        'Listado y asignación de gerentes por tienda.': 'List and assign managers by store.',
        'Resumen de gerentes': 'Managers summary',
        'Buscar gerente': 'Search manager',
        'Nombre o usuario': 'Name or username',
        'Sucursal asignada': 'Assigned branch',
        'Agregar gerente': 'Add manager',
        'Guardar gerente': 'Save manager',
        'Desactivar gerente': 'Deactivate manager',
        'Editar gerente': 'Edit manager',
        'Cerrar formulario de gerente': 'Close manager form',
        'No hay gerentes registrados.': 'No managers have been registered.',
        'Aún no hay gerentes registrados': 'No managers have been registered yet',
        'No hay gerentes que coincidan con la búsqueda': 'No managers match the search',
        'No hay gerentes que mostrar': 'There are no managers to display',
        'Gerente desactivado correctamente': 'Manager deactivated successfully',
        'Gerente guardado correctamente': 'Manager saved successfully',
        'No se pudo desactivar el gerente.': 'Could not deactivate the manager.',
        'No se pudo guardar el gerente.': 'Could not save the manager.',
        'No se pudieron cargar los gerentes.': 'Managers could not be loaded.',
        'No se pudieron cargar los gerentes. Revisa tu conexión e intenta nuevamente.': 'Managers could not be loaded. Check your connection and try again.',
        '? El registro y sus referencias se conservarán; el gerente ya no podrá acceder.': '? The record and its references will be kept; the manager will no longer be able to log in.',
        '¿Desactivar a ': 'Deactivate ',
        'ADMINISTRACIÓN DE PERSONAL': 'STAFF ADMINISTRATION',
        'PERSONAL DE CAJA': 'CASHIER STAFF',
        'Agregar cajero': 'Add cashier',
        'Crear cajero': 'Create cashier',
        'Consultar cajeros': 'View cashiers',
        'Editar cajero': 'Edit cashier',
        'Guardar cajero': 'Save cashier',
        'Activar cajero': 'Activate cashier',
        'Desactivar cajero': 'Deactivate cashier',
        'Filtrar cajeros': 'Filter cashiers',
        'Buscar por nombre o usuario': 'Search by name or username',
        'No hay cajeros que coincidan con los filtros.': 'No cashiers match the filters.',
        'Aún no hay cajeros registrados.': 'No cashiers have been registered yet.',
        'No se pudieron cargar los cajeros.': 'Cashiers could not be loaded.',
        'No se pudo editar el cajero.': 'Could not edit the cashier.',
        'Sus ventas anteriores se conservarán.': 'Their previous sales will be kept.',

        // Campos, estados, modales y paginación
        'Nombre completo': 'Full name',
        'Teléfono': 'Phone',
        'Dirección': 'Address',
        'Email': 'Email',
        'Correo': 'Email',
        'Usuario': 'Username',
        'Contraseña': 'Password',
        'Déjalo vacío para conservar la actual': 'Leave blank to keep the current one',
        'Fecha de creación': 'Created',
        'Acciones': 'Actions',
        'Estado': 'Status',
        'Filtrar': 'Filter',
        'Todas': 'All',
        'Todos': 'All',
        'Activas': 'Active',
        'Inactivas': 'Inactive',
        'Activos': 'Active',
        'Inactivos': 'Inactive',
        'Activa': 'Active',
        'Inactiva': 'Inactive',
        'Activo': 'Active',
        'Inactivo': 'Inactive',
        'activos': 'active',
        'activo': 'active',
        'activas': 'active',
        'activa': 'active',
        'inactivos': 'inactive',
        'inactivo': 'inactive',
        'inactivas': 'inactive',
        'inactiva': 'inactive',
        'No disponible': 'Unavailable',
        'CONFIRMACIÓN': 'CONFIRMATION',
        'CONFIRMAR ACCIÓN': 'CONFIRM ACTION',
        '¿Continuar?': 'Continue?',
        ' del catálogo?': ' from the catalog?',
        'Desactivar': 'Deactivate',
        'Activar': 'Activate',
        'Cancelar': 'Cancel',
        'Confirmar': 'Confirm',
        'Cerrar': 'Close',
        'Cerrar confirmación': 'Close confirmation',
        'Cancelar desactivación': 'Cancel deactivation',
        'Cancelar eliminación': 'Cancel deletion',
        'Resultados de búsqueda': 'Search results',
        'BÚSQUEDA': 'SEARCH',
        'Selecciona Productos o Sucursal.': 'Select Products or Branch.',
        'Cargando inventario…': 'Loading inventory…',
        'Cargando ventas…': 'Loading sales…',
        'Cargando inventario...': 'Loading inventory...',
        'Cargando cajeros...': 'Loading cashiers...',
        'Cargando gerente...': 'Loading manager...',
        'Cargando sucursal...': 'Loading branch...',
        'Cargando': 'Loading',
        'Ventas por página': 'Sales per page',
        'Gerentes por página': 'Managers per page',
        'Productos por página': 'Products per page',
        'Sucursales por página': 'Branches per page',
        'Paginación de ventas': 'Sales pagination',
        'Paginación del inventario': 'Inventory pagination',
        'Paginación de sucursales': 'Branch pagination',
        'Paginación de gerentes': 'Manager pagination',
        'Filas por página': 'Rows per page',
        'Filas': 'Rows',
        'Página anterior': 'Previous page',
        'Página siguiente': 'Next page',
        'Anterior': 'Previous',
        'Siguiente': 'Next',
        'Página ': 'Page ',
        'Mostrando ': 'Showing ',
        'Mostrando': 'Showing',
        'Mostrar': 'Show',
        'por página': 'per page',
        'No se pudo cargar la paginación': 'Could not load pagination',
        'No se pudo cargar el contexto.': 'The context could not be loaded.',
        'No se pudo recordar el tamaño de página en este navegador.': 'Could not remember the page size in this browser.',
        'No se pudo guardar la preferencia de tema.': 'Could not save the theme preference.',
        'No se pudo guardar la preferencia de idioma.': 'Could not save the language preference.',
        'No se pudo guardar la cantidad de filas por página.': 'Could not save the number of rows per page.'
    };

    const escaparRegex = texto => texto.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const regexTraducciones = new RegExp(
        Object.keys(traducciones)
            .sort((a, b) => b.length - a.length)
            .map(escaparRegex)
            .join('|'),
        'g'
    );

    const atributos = ['aria-label', 'aria-valuetext', 'placeholder', 'title'];
    const textosOriginales = new WeakMap();
    const atributosOriginales = new WeakMap();
    const tituloOriginal = document.title;

    const traducirDesdeEspanol = (texto, idioma) => {
        if (idioma === 'es' || !texto) return texto;
        let resultado = texto.replace(regexTraducciones, coincidencia => traducciones[coincidencia] ?? coincidencia);
        resultado = resultado.replace(/(\b\d+\s*-\s*\d+\s+)de(\s+\d+)/g, '$1of$2');
        return resultado;
    };

    let traduciendo = false;
    const traducirInterfaz = idioma => {
        if (traduciendo) return;
        traduciendo = true;

        try {
            if (document.documentElement.lang !== idioma) document.documentElement.lang = idioma;
            const tituloTraducido = traducirDesdeEspanol(tituloOriginal, idioma);
            if (document.title !== tituloTraducido) document.title = tituloTraducido;

            const recorrido = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
            let nodo;
            while ((nodo = recorrido.nextNode())) {
                if (nodo.parentElement && !['SCRIPT', 'STYLE'].includes(nodo.parentElement.tagName)) {
                    if (!textosOriginales.has(nodo)) {
                        textosOriginales.set(nodo, nodo.nodeValue);
                    } else if (idioma === 'en') {
                        const esperado = traducirDesdeEspanol(textosOriginales.get(nodo), 'en');
                        if (nodo.nodeValue !== esperado) {
                            textosOriginales.set(nodo, nodo.nodeValue);
                        }
                    } else {
                        textosOriginales.set(nodo, nodo.nodeValue);
                    }

                    const textoTraducido = traducirDesdeEspanol(textosOriginales.get(nodo), idioma);
                    if (nodo.nodeValue !== textoTraducido) nodo.nodeValue = textoTraducido;
                }
            }

            document.querySelectorAll('*').forEach(elemento => {
                if (elemento.id === 'boton-idioma') return;
                if (!atributosOriginales.has(elemento)) atributosOriginales.set(elemento, new Map());
                const originales = atributosOriginales.get(elemento);

                atributos.forEach(atributo => {
                    const valor = elemento.getAttribute(atributo);
                    if (!valor) return;

                    if (!originales.has(atributo)) {
                        originales.set(atributo, valor);
                    } else if (idioma === 'en') {
                        const esperado = traducirDesdeEspanol(originales.get(atributo), 'en');
                        if (valor !== esperado) {
                            originales.set(atributo, valor);
                        }
                    } else {
                        originales.set(atributo, valor);
                    }

                    const valorTraducido = traducirDesdeEspanol(originales.get(atributo), idioma);
                    if (valor !== valorTraducido) elemento.setAttribute(atributo, valorTraducido);
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
        } finally {
            traduciendo = false;
        }
    };

    let idiomaActual = 'es';
    try {
        idiomaActual = localStorage.getItem('private-storage-idioma') === 'en' ? 'en' : 'es';
    } catch {
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
        subtree: true,
        characterData: true,
        attributes: true,
        attributeFilter: atributos
    });
})();
