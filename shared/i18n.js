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
        'Email': 'Email',
        'Dirección': 'Address'
        ,'ADMINISTRACIÓN DE SEDE': 'BRANCH ADMINISTRATION'
        ,'ADMINISTRACIÓN DE SEDES': 'BRANCH ADMINISTRATION'
        ,'ADMINISTRACION DE SEDE': 'BRANCH ADMINISTRATION'
        ,'ADMINISTRACION DE SEDES': 'BRANCH ADMINISTRATION'
        ,'ADMINISTRACIÓN DE PERSONAL': 'STAFF ADMINISTRATION'
        ,'PERSONAL DE CAJA': 'CASHIER STAFF'
        ,'Mi sucursal': 'My branch'
        ,'Eliminar del catálogo': 'Remove from catalog'
        ,'Esta acción no se puede deshacer.': 'This action cannot be undone.'
        ,'Poco stock': 'Low stock'
        ,' con stock bajo': ' low-stock products'
        ,'Navegación del administrador': 'Administrator navigation'
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
        ,'Activar cajero': 'Activate cashier',
        'Desactivar cajero': 'Deactivate cashier',
        'Cambiar tema': 'Change theme',
        'Abrir notificaciones': 'Open notifications',
        'Cerrar detalle de venta': 'Close sale details',
        'Cerrar formulario de gerente': 'Close manager form',
        'Cancelar desactivación': 'Cancel deactivation',
        'Cancelar eliminación': 'Cancel deletion',
        'Gerentes por página': 'Managers per page',
        'Productos por página': 'Products per page',
        'Sucursales por página': 'Branches per page',
        'Paginación de ventas': 'Sales pagination',
        'Email': 'Email'
        ,'No hay gerentes que coincidan con la búsqueda': 'No managers match the search'
        ,'No hay gerentes que mostrar': 'There are no managers to display'
        ,'Gerente desactivado correctamente': 'Manager deactivated successfully'
        ,'Gerente guardado correctamente': 'Manager saved successfully'
        ,'No se pudo desactivar el gerente.': 'Could not deactivate the manager.'
        ,'No se pudo guardar el gerente.': 'Could not save the manager.'
        ,'No se pudo comunicar con el servidor. Intenta nuevamente.': 'Could not communicate with the server. Please try again.'
        ,'No se pudo cargar la paginación': 'Could not load pagination'
        ,'Caja': 'Cash register'
        ,'Fecha de creación': 'Created'
        ,'Agregar cajero': 'Add cashier'
        ,'Crear cajero': 'Create cashier'
        ,'Consultar cajeros': 'View cashiers'
        ,'Editar cajero': 'Edit cashier'
        ,'Buscar por nombre o usuario': 'Search by name or username'
        ,'Buscar productos': 'Search products'
        ,'Buscar productos por nombre o código...': 'Search products by name or code...'
        ,'Buscar productos por nombre o código': 'Search products by name or code'
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

        // Inicio de sesión
        ,'Ingresa tu usuario': 'Enter your username'
        ,'Tu contraseña temporal venció. Pide que te reenvíen el acceso o usa "¿Olvidaste tu contraseña?".': 'Your temporary password has expired. Ask for your access to be resent or use "Forgot your password?".'

        // Alta de usuarios con contraseña temporal
        ,'Se generará una contraseña temporal y se enviará al correo del gerente junto con su usuario.': 'A temporary password will be generated and sent to the manager\'s email along with their username.'
        ,'Se generará una contraseña temporal y se enviará al correo del cajero junto con su usuario.': 'A temporary password will be generated and sent to the cashier\'s email along with their username.'
        ,'Gerente creado. Le enviamos su contraseña temporal por correo.': 'Manager created. We emailed them their temporary password.'
        ,'Gerente creado, pero no se pudo enviar el correo con su contraseña temporal.': 'Manager created, but the email with their temporary password could not be sent.'
        ,'Cajero creado. Le enviamos su contraseña temporal por correo.': 'Cashier created. We emailed them their temporary password.'
        ,'Cajero creado, pero no se pudo enviar el correo con su contraseña temporal.': 'Cashier created, but the email with their temporary password could not be sent.'
        ,'Cajero creado, pero no recibió su correo de acceso.': 'Cashier created, but they did not receive their access email.'
        ,'No se pudo enviar el correo': 'The email could not be sent'
        ,': la cuenta se creó correctamente, pero no pudimos enviarle el correo con su contraseña temporal. Puedes intentarlo de nuevo; se generará una contraseña temporal nueva.': ': the account was created successfully, but we could not send the email with their temporary password. You can try again; a new temporary password will be generated.'
        ,'El cajero se creó correctamente, pero no pudimos enviarle el correo con su contraseña temporal. ¿Quieres intentarlo de nuevo? Se generará una contraseña temporal nueva.': 'The cashier was created successfully, but we could not send the email with their temporary password. Do you want to try again? A new temporary password will be generated.'
        ,'¿Quieres intentarlo otra vez?': 'Do you want to try again?'
        ,'No se pudo comunicar con el servidor.': 'Could not communicate with the server.'
        ,'Reenviar correo': 'Resend email'
        ,'Cerrar aviso': 'Close notice'
        ,'AVISO': 'NOTICE'
        ,'Correo reenviado con una nueva contraseña temporal.': 'Email resent with a new temporary password.'
        ,'Correo reenviado correctamente.': 'Email resent successfully.'
        ,'Correo reenviado correctamente': 'Email resent successfully'
        ,'No se pudo reenviar el correo.': 'The email could not be resent.'
        ,'No se pudo enviar el correo. Intenta de nuevo en unos minutos.': 'The email could not be sent. Try again in a few minutes.'
        ,'No se pudo reenviar el acceso.': 'Access could not be resent.'
        ,'El gerente está inactivo. Actívalo antes de reenviar el acceso.': 'The manager is inactive. Activate them before resending access.'
        ,'El cajero está inactivo. Actívalo antes de reenviar el acceso.': 'The cashier is inactive. Activate them before resending access.'
        ,'Este gerente ya activó su cuenta. Si olvidó su contraseña, puede usar "¿Olvidaste tu contraseña?".': 'This manager has already activated their account. If they forgot their password, they can use "Forgot your password?".'
        ,'Este cajero ya activó su cuenta. Si olvidó su contraseña, puede usar "¿Olvidaste tu contraseña?".': 'This cashier has already activated their account. If they forgot their password, they can use "Forgot your password?".'
        ,'El gerente no tiene un correo electrónico válido.': 'The manager does not have a valid email address.'
        ,'El cajero no tiene un correo electrónico válido.': 'The cashier does not have a valid email address.'
        ,'El gerente no existe.': 'The manager does not exist.'
        ,'No se encontró el cajero.': 'The cashier was not found.'

        // Cambio obligatorio de contraseña
        ,'Crea tu contraseña': 'Create your password'
        ,'Crear contraseña': 'Create password'
        ,'Primer inicio de sesión': 'First login'
        ,'Por tu seguridad, reemplaza la contraseña temporal por una que solo tú conozcas.': 'For your security, replace the temporary password with one that only you know.'
        ,'Debe tener al menos 8 caracteres y ser distinta a la temporal.': 'It must have at least 8 characters and be different from the temporary one.'
        ,'He leído y acepto los ': 'I have read and accept the '
        ,'términos de uso y el aviso de privacidad': 'terms of use and privacy notice'
        ,'Guardar y entrar': 'Save and continue'
        ,'Volver al inicio de sesión': 'Back to log in'
        ,'Debes aceptar los términos de uso y el aviso de privacidad para continuar.': 'You must accept the terms of use and privacy notice to continue.'
        ,'Contraseña guardada.': 'Password saved.'
        ,'Entrando al sistema...': 'Signing in...'
        ,'No se pudo guardar la contraseña.': 'The password could not be saved.'
        ,'¡Listo! Tu contraseña se guardó correctamente.': 'Done! Your password was saved successfully.'
        ,'Tu sesión expiró. Inicia sesión de nuevo con tu contraseña temporal.': 'Your session expired. Log in again with your temporary password.'
        ,'La solicitud no es válida. Recarga la página.': 'The request is not valid. Reload the page.'
        ,'Tu contraseña temporal ya no es válida. Inicia sesión de nuevo o pide que te reenvíen el acceso.': 'Your temporary password is no longer valid. Log in again or ask for your access to be resent.'
        ,'La nueva contraseña debe ser distinta a la contraseña temporal.': 'The new password must be different from the temporary password.'
        ,'Tu contraseña se actualizó. Inicia sesión de nuevo.': 'Your password was updated. Log in again.'

        // Términos de uso y aviso de privacidad
        ,'Términos y privacidad': 'Terms and privacy'
        ,'Términos de uso y aviso de privacidad': 'Terms of use and privacy notice'
        ,'Términos de uso': 'Terms of use'
        ,'Aviso de privacidad': 'Privacy notice'
        ,'Índice del documento': 'Table of contents'
        ,'1. Uso del sistema': '1. Use of the system'
        ,'PrivateStorage es un sistema destinado a la gestión de productos, inventarios, sucursales y ventas. El usuario se compromete a utilizarlo únicamente para las actividades autorizadas.': 'PrivateStorage is a system for managing products, inventory, branches and sales. Users agree to use it only for authorized activities.'
        ,'2. Responsabilidad del usuario': '2. User responsibility'
        ,'El usuario es responsable del uso de su cuenta y de mantener la confidencialidad de sus credenciales. No debe compartir su contraseña con otras personas.': 'Users are responsible for the use of their account and for keeping their credentials confidential. They must not share their password with anyone else.'
        ,'3. Uso adecuado de la información': '3. Proper use of information'
        ,'La información registrada en el sistema debe ser verdadera y utilizarse únicamente para los fines correspondientes a las actividades de la empresa.': 'Information recorded in the system must be truthful and used only for purposes related to the company\'s activities.'
        ,'4. Seguridad': '4. Security'
        ,'El usuario debe informar sobre cualquier actividad sospechosa o acceso no autorizado a su cuenta.': 'Users must report any suspicious activity or unauthorized access to their account.'
        ,'Responsable de tus datos personales': 'Data controller'
        ,', con domicilio en Av. México 245, Col. Centro, C.P. 28200, Manzanillo, Colima, México, es responsable del tratamiento de los datos personales que nos proporcionas como persona usuaria de PrivateStorage, conforme a la Ley Federal de Protección de Datos Personales en Posesión de los Particulares.': ', located at Av. México 245, Col. Centro, C.P. 28200, Manzanillo, Colima, Mexico, is responsible for processing the personal data you provide as a PrivateStorage user, in accordance with the Federal Law on the Protection of Personal Data Held by Private Parties.'
        ,'Datos que recabamos': 'Data we collect'
        ,'Nombre completo.': 'Full name.'
        ,'Usuario de acceso.': 'Login username.'
        ,'Correo electrónico.': 'Email address.'
        ,'Puesto (gerente o cajero) y sucursal asignada.': 'Position (manager or cashier) and assigned branch.'
        ,'Registros de actividad en el sistema, como ventas, movimientos de inventario y fechas de acceso.': 'Activity records in the system, such as sales, inventory movements and login dates.'
        ,'No recabamos datos personales sensibles.': 'We do not collect sensitive personal data.'
        ,'Para qué usamos tus datos': 'How we use your data'
        ,'Crear y administrar tu cuenta de acceso a PrivateStorage.': 'To create and manage your PrivateStorage account.'
        ,'Enviarte tus datos de acceso, contraseñas temporales y avisos de seguridad, como el restablecimiento de contraseña.': 'To send you your login details, temporary passwords and security notices, such as password resets.'
        ,'Registrar las operaciones de venta e inventario que realizas en tu sucursal.': 'To record the sales and inventory operations you perform at your branch.'
        ,'Mantener la seguridad del sistema y llevar un control de las operaciones realizadas.': 'To keep the system secure and maintain a record of the operations performed.'
        ,'Transferencias': 'Transfers'
        ,'Tus datos no se comparten con terceros. Solo los conocen los proveedores de alojamiento web y de correo electrónico que prestan el servicio a PrivateStorage, y únicamente para ese fin, o las autoridades competentes cuando la ley lo exija.': 'Your data is not shared with third parties. It is only accessible to the web hosting and email providers that serve PrivateStorage, solely for that purpose, or to the competent authorities when required by law.'
        ,'Tus derechos (ARCO)': 'Your rights (ARCO)'
        ,'Puedes acceder a tus datos, rectificarlos, cancelarlos u oponerte a su uso, así como revocar tu consentimiento, enviando una solicitud a ': 'You can access, rectify or cancel your data, or object to its use, and you can also withdraw your consent, by sending a request to '
        ,'. Incluye tu nombre, tu usuario, una descripción clara de lo que solicitas y una copia de una identificación oficial. Te responderemos en un plazo máximo de 20 días hábiles.': '. Include your name, your username, a clear description of your request and a copy of an official ID. We will reply within 20 business days at most.'
        ,'Cambios a este aviso': 'Changes to this notice'
        ,'Cualquier cambio a estos términos o a este aviso de privacidad se publicará en esta misma página.': 'Any changes to these terms or to this privacy notice will be published on this page.'
        ,'Última actualización: 18 de noviembre de 2026.': 'Last updated: November 18, 2026.'
        ,'← Volver al inicio de sesión': '← Back to log in'
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
