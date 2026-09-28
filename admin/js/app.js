const STORAGE_KEY = 'punto-norte-admin-v1';
const today = new Date(Date.now() - new Date().getTimezoneOffset() * 60000).toISOString().slice(0, 10);
const seedInventory = [
  { id: 1, branch: '1', name: 'Teclado Logitech K380', sku: 'TEC-001', category: 'Teclados', quantity: 12, stockValue: 543, unitPrice: 799 },
  { id: 2, branch: '1', name: 'Mouse Logitech M185', sku: 'MOU-014', category: 'Mouse', quantity: 24, stockValue: 720, unitPrice: 349 },
  { id: 3, branch: '2', name: 'Audífonos HyperX Cloud Stinger', sku: 'AUD-028', category: 'Audio', quantity: 8, stockValue: 3120, unitPrice: 1299 },
  { id: 4, branch: '2', name: 'Teclado Redragon Kumara', sku: 'TEC-032', category: 'Teclados', quantity: 6, stockValue: 2700, unitPrice: 899 },
  { id: 5, branch: '3', name: 'Monitor LG UltraGear 24 pulgadas', sku: 'MON-041', category: 'Monitores', quantity: 5, stockValue: 18995, unitPrice: 3799 },
  { id: 6, branch: '3', name: 'SSD Kingston NV2 1 TB', sku: 'SSD-052', category: 'Almacenamiento', quantity: 11, stockValue: 10450, unitPrice: 1499 }
];
const seedSales = [
  { folio: 'V-1048', date: today, cashier: 'Ana López', productId: 1, productName: seedInventory[0].name, quantity: 1, total: 799 },
  { folio: 'V-1047', date: today, cashier: 'Carlos Ruiz', productId: 2, productName: seedInventory[1].name, quantity: 2, total: 698 },
  { folio: 'V-1046', date: today, cashier: 'Ana López', productId: 4, productName: seedInventory[3].name, quantity: 1, total: 899 },
  { folio: 'V-1045', date: today, cashier: 'María Torres', productId: 3, productName: seedInventory[2].name, quantity: 1, total: 1299 },
  { folio: 'V-1044', date: today, cashier: 'Carlos Ruiz', productId: 6, productName: seedInventory[5].name, quantity: 1, total: 1499 }
];

function loadState() {
  try {
    const saved = JSON.parse(localStorage.getItem(STORAGE_KEY));
    if (saved && Array.isArray(saved.inventory) && Array.isArray(saved.sales)) return saved;
  } catch {}
  return { inventory: structuredClone(seedInventory), sales: structuredClone(seedSales), nextProductId: 7, nextSaleNumber: 1049 };
}

const state = loadState();
const page = document.body.dataset.page;
const toast = document.createElement('div');
toast.className = 'aviso';
toast.setAttribute('role', 'status');
toast.setAttribute('aria-live', 'polite');
document.body.append(toast);
let toastTimer;
let editingId = null;
let activeAction = null;

function saveState() {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
}

function showToast(message) {
  toast.textContent = message;
  toast.classList.add('visible');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => toast.classList.remove('visible'), 2600);
}

function createDialog() {
  const dialog = document.createElement('dialog');
  dialog.id = 'action-dialog';
  dialog.setAttribute('aria-labelledby', 'dialog-title');
  dialog.innerHTML = '<form class="contenido-dialogo" id="action-form"><div class="encabezado-dialogo"><h2 id="dialog-title">Nuevo registro</h2><button class="boton-cerrar" type="button" aria-label="Cerrar"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 6 12 12M18 6 6 18"/></svg></button></div><div class="rejilla-formulario" id="dialog-fields"></div></form>';
  document.body.append(dialog);
  dialog.querySelector('.boton-cerrar').addEventListener('click', () => dialog.close());
  dialog.addEventListener('click', (event) => { if (event.target === dialog) dialog.close(); });
  dialog.querySelector('form').addEventListener('submit', submitDialog);
  return dialog;
}

const dialog = createDialog();

function addField(container, labelText, type, value = '', options = {}) {
  const label = document.createElement('label');
  label.append(document.createTextNode(labelText));
  const control = type === 'select' ? document.createElement('select') : document.createElement('input');
  control.setAttribute('aria-label', labelText);
  control.required = true;
  if (type === 'select') {
    options.items.forEach((item) => {
      const option = document.createElement('option');
      option.value = item.value;
      option.textContent = item.label;
      option.disabled = item.disabled ?? false;
      control.append(option);
    });
  } else {
    control.type = type;
    control.autocomplete = 'off';
    if (type === 'number') control.min = '0';
    control.value = value;
  }
  label.append(control);
  container.append(label);
  return control;
}

function openDialog(title, fields, onBuild) {
  activeAction = title;
  document.querySelector('#dialog-title').textContent = title;
  const container = document.querySelector('#dialog-fields');
  container.replaceChildren();
  const controls = onBuild(container, fields);
  const submit = document.createElement('button');
  submit.type = 'submit';
  submit.className = 'boton-guardar';
  submit.textContent = title === 'Registrar venta manual' ? 'Registrar venta' : 'Guardar';
  container.append(submit);
  dialog.showModal();
  controls?.focus();
}

function openProductDialog(product = null) {
  editingId = product?.id ?? null;
  openDialog(product ? 'Editar producto' : 'Agregar producto', null, (container) => {
    const values = product ? [product.name, product.sku, product.category, product.branch, product.quantity, product.stockValue] : [];
    const labels = [['Nombre del producto', 'text'], ['Código SKU', 'text'], ['Categoría', 'text'], ['No. Sucursal', 'text'], ['Cantidad', 'number'], ['Valor de stock', 'number']];
    let first;
    labels.forEach(([label, type], index) => {
      const input = addField(container, label, type, values[index] ?? '');
      if (index === 0) first = input;
    });
    return first;
  });
}

function openSaleDialog() {
  editingId = null;
  openDialog('Registrar venta manual', null, (container) => {
    const cashier = addField(container, 'Nombre del cajero', 'text');
    const options = state.inventory.map((product) => ({ value: String(product.id), label: `${product.name} (${product.quantity} disponibles)`, disabled: product.quantity < 1 }));
    const product = addField(container, 'Producto', 'select', '', { items: options });
    const quantity = addField(container, 'Cantidad', 'number', '1');
    const updateMaximum = () => {
      quantity.max = String(state.inventory.find((item) => item.id === Number(product.value))?.quantity ?? 1);
    };
    updateMaximum();
    product.addEventListener('change', updateMaximum);
    quantity.min = '1';
    return cashier;
  });
}

function renderInventory() {
  const rows = document.querySelector('#inventory-rows');
  if (!rows) return;
  const branchQuery = document.querySelector('#branch-filter').value.trim().toLocaleLowerCase('es');
  const categoryQuery = document.querySelector('#category-filter').value;
  const searchQuery = document.querySelector('#inventory-search')?.value.trim().toLocaleLowerCase('es') ?? '';
  const products = state.inventory.filter((product) => product.branch.toLocaleLowerCase('es').includes(branchQuery) && (!categoryQuery || product.category === categoryQuery) && product.name.toLocaleLowerCase('es').includes(searchQuery));
  rows.replaceChildren();
  if (!products.length) {
    const row = document.createElement('tr');
    row.innerHTML = '<td class="inventario-vacio" colspan="6">No hay productos que coincidan con estos filtros.</td>';
    rows.append(row);
    return;
  }
  products.forEach((product) => {
    const row = document.createElement('tr');
    [product.branch, product.name, product.category, product.quantity, Number(product.stockValue).toLocaleString('es-MX')].forEach((value, index) => {
      const cell = document.createElement('td');
      cell.textContent = value;
      if (index === 1) cell.title = product.name;
      row.append(cell);
    });
    const actionsCell = document.createElement('td');
    const actions = document.createElement('div');
    actions.className = 'acciones-inventario';
    [['Añadir', 'reponer'], ['Editar', 'editar'], ['Eliminar', 'eliminar']].forEach(([label, action]) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = `accion-fila-inventario ${action}`;
      button.dataset.action = action;
      button.dataset.productId = product.id;
      button.textContent = label;
      actions.append(button);
    });
    actionsCell.append(actions);
    row.append(actionsCell);
    rows.append(row);
  });
}

function renderSales() {
  const rows = document.querySelector('#sales-rows');
  if (!rows) return;
  const cashierQuery = document.querySelector('#cashier-filter').value.trim().toLocaleLowerCase('es');
  const dateQuery = document.querySelector('#date-filter').value;
  const sales = state.sales.filter((sale) => sale.cashier.toLocaleLowerCase('es').includes(cashierQuery) && (!dateQuery || sale.date === dateQuery));
  rows.replaceChildren();
  if (!sales.length) {
    const row = document.createElement('tr');
    row.innerHTML = '<td class="ventas-vacias" colspan="6">No hay ventas que coincidan con estos filtros.</td>';
    rows.append(row);
    return;
  }
  sales.forEach((sale) => {
    const product = state.inventory.find((item) => item.id === sale.productId);
    const values = [sale.folio, new Date(`${sale.date}T12:00:00`).toLocaleDateString('es-MX'), sale.cashier, sale.productName ?? product?.name ?? 'Producto eliminado', sale.quantity, `$${Number(sale.total).toLocaleString('es-MX')}`];
    const row = document.createElement('tr');
    values.forEach((value, index) => {
      const cell = document.createElement('td');
      cell.textContent = value;
      if (index === 3) cell.title = value;
      if (index === 5) cell.className = 'total-ventas';
      row.append(cell);
    });
    rows.append(row);
  });
}

function submitDialog(event) {
  event.preventDefault();
  const controls = [...document.querySelectorAll('#dialog-fields input, #dialog-fields select')];
  if (activeAction === 'Registrar venta manual') {
    const [cashierControl, productControl, quantityControl] = controls;
    const product = state.inventory.find((item) => item.id === Number(productControl.value));
    const quantity = Number(quantityControl.value);
    if (!product || quantity < 1 || quantity > product.quantity) {
      showToast('La cantidad supera las existencias disponibles.');
      return;
    }
    product.quantity -= quantity;
    state.sales.unshift({ folio: `V-${state.nextSaleNumber++}`, date: today, cashier: cashierControl.value.trim(), productId: product.id, productName: product.name, quantity, total: Number(product.unitPrice ?? product.stockValue) * quantity });
    saveState();
    renderSales();
    showToast('Venta registrada.');
  } else if (activeAction === 'Agregar producto' || activeAction === 'Editar producto') {
    const [name, sku, category, branch, quantity, stockValue] = controls.map((control) => control.value.trim());
    const existing = state.inventory.find((item) => item.id === editingId);
    const product = { id: editingId ?? state.nextProductId++, name, sku, category, branch, quantity: Number(quantity), stockValue: Number(stockValue), unitPrice: existing?.unitPrice ?? Math.round(Number(stockValue) / Math.max(Number(quantity), 1)) };
    if (existing) state.inventory.splice(state.inventory.indexOf(existing), 1, product);
    else state.inventory.push(product);
    saveState();
    renderInventory();
    showToast('Producto guardado.');
  }
  dialog.close();
  event.target.reset();
}

function renderLowStock() {
  const rows = document.querySelector('#stock-rows');
  if (!rows) return;
  const low = state.inventory.filter((product) => product.quantity <= 8).slice(0, 5);
  rows.replaceChildren();
  low.forEach((product) => {
    const row = document.createElement('tr');
    row.dataset.product = product.name;
    const dotClass = product.quantity <= 5 ? 'critico' : product.quantity <= 7 ? 'advertencia' : 'bajo';
    row.innerHTML = `<td><span class="celda-producto"><span class="punto-estado ${dotClass}"></span>${product.name}</span></td><td>Sucursal ${product.branch}</td><td class="cantidad">${product.quantity}</td>`;
    rows.append(row);
  });
  const empty = document.querySelector('#empty-state');
  if (empty) empty.style.display = low.length ? 'none' : 'block';
  renderLatestSales();
}

function renderLatestSales() {
  const list = document.querySelector('#latest-sales');
  if (!list) return;
  list.replaceChildren();
  state.sales.slice(0, 5).forEach((sale) => {
    const row = document.createElement('div');
    row.className = 'fila-venta';
    const details = document.createElement('span');
    details.textContent = `${sale.cashier} · ${sale.productName ?? 'Producto'}`;
    const total = document.createElement('strong');
    total.textContent = `$${Number(sale.total).toLocaleString('es-MX')}`;
    row.append(details, total);
    list.append(row);
  });
}

function connectPage() {
  document.querySelectorAll('[data-theme]').forEach((button) => {
    const isDark = localStorage.getItem('punto-norte-theme') === 'dark';
    document.body.classList.toggle('oscuro', isDark);
    document.querySelectorAll('[data-theme]').forEach((option) => option.setAttribute('aria-pressed', String(option.dataset.theme === (isDark ? 'dark' : 'light'))));
    button.addEventListener('click', () => {
      const dark = button.dataset.theme === 'dark';
      document.body.classList.toggle('oscuro', dark);
      localStorage.setItem('punto-norte-theme', dark ? 'dark' : 'light');
      document.querySelectorAll('[data-theme]').forEach((option) => option.setAttribute('aria-pressed', String(option === button)));
    });
  });
  document.querySelector('#notifications')?.addEventListener('click', () => showToast('Revisa los productos con poco stock.'));
  document.querySelector('#search-form')?.addEventListener('submit', (event) => {
    event.preventDefault();
    const query = document.querySelector('#search-input').value.trim().toLocaleLowerCase('es');
    if (page === 'inventory') {
      document.querySelector('#inventory-search').value = query;
      renderInventory();
      return;
    }
    if (page === 'sales') {
      document.querySelector('#cashier-filter').value = query;
      renderSales();
      return;
    }
    document.querySelectorAll('#stock-rows tr').forEach((row) => { row.hidden = !row.dataset.product.toLocaleLowerCase('es').includes(query); });
    const visible = [...document.querySelectorAll('#stock-rows tr')].some((row) => !row.hidden);
    const empty = document.querySelector('#empty-state');
    if (empty) empty.style.display = visible ? 'none' : 'block';
  });
  document.querySelector('#branch-filter')?.addEventListener('input', renderInventory);
  document.querySelector('#category-filter')?.addEventListener('change', renderInventory);
  document.querySelector('#inventory-search')?.addEventListener('input', renderInventory);
  document.querySelector('#cashier-filter')?.addEventListener('input', renderSales);
  document.querySelector('#date-filter')?.addEventListener('change', renderSales);
  document.querySelector('#register-sale')?.addEventListener('click', openSaleDialog);
  document.querySelector('#add-product')?.addEventListener('click', () => openProductDialog());
  document.querySelectorAll('[data-quick-action]').forEach((button) => button.addEventListener('click', () => showToast(`${button.dataset.quickAction}: disponible para configurar.`)));
  document.querySelector('#inventory-rows')?.addEventListener('click', (event) => {
    const button = event.target.closest('[data-action]');
    if (!button) return;
    const product = state.inventory.find((item) => item.id === Number(button.dataset.productId));
    if (!product) return;
    if (button.dataset.action === 'reponer') product.quantity++;
    else if (button.dataset.action === 'editar') { openProductDialog(product); return; }
    else if (button.dataset.action === 'eliminar') {
      state.inventory.splice(state.inventory.indexOf(product), 1);
      showToast('Producto eliminado.');
    }
    saveState();
    renderInventory();
  });
  if (page === 'dashboard') renderLowStock();
  if (page === 'inventory') renderInventory();
  if (page === 'sales') renderSales();
}

connectPage();
