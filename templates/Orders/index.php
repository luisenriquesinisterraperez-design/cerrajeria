<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Order> $orders
 * @var mixed $products
 * @var mixed $deliveryDrivers
 * @var mixed $clients
 * @var bool $isAdmin
 */
$user = $this->request->getAttribute('identity')->getOriginalData();
$isRepartidor = ($user->role === 'repartidor');
?>

<?php if (!$isRepartidor): ?>
    <header class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Registro de Ventas</h1>
            <p class="text-blue-600 font-bold uppercase text-xs tracking-widest">Control diario de pedidos</p>
        </div>
        <?php if ($isAdmin): ?>
            <?= $this->Html->link('<i class="fa-solid fa-shoe-prints mr-2"></i> Ver Auditoría (Huella)', ['controller' => 'OrderLogs', 'action' => 'index'], ['escape' => false, 'class' => 'bg-orange-100 text-orange-600 px-4 py-2 rounded-xl font-bold text-xs hover:bg-orange-200 transition-all']) ?>
        <?php endif; ?>
    </header>

    <div class="bg-white p-8 rounded-3xl border border-blue-100 shadow-lg mb-10">
        <h3 class="font-black text-slate-800 uppercase text-sm mb-6 flex items-center gap-2">
            <i class="fa-solid fa-cart-plus text-blue-600"></i> Nuevo Pedido Multi-Producto
        </h3>
        <?= $this->Form->create(null, ['url' => ['action' => 'add'], 'id' => 'order-form']) ?>
            <!-- Datos del Cliente -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 pb-8 border-b border-slate-50">
                <div class="relative autocomplete-wrap" data-autocomplete="clients">
                    <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Nombre del Cliente</label>
                    <input type="text" name="customer_name" id="customer-name"
                        value="Consumidor Final"
                        placeholder="Buscar o escribir nombre..."
                        class="w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 autocomplete-input required-field"
                        autocomplete="off" required>
                    <i class="fa-solid fa-chevron-down absolute right-4 bottom-5 text-slate-300 pointer-events-none"></i>
                    <ul class="autocomplete-dropdown hidden"></ul>
                </div>
                <div class="relative">
                    <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Celular</label>
                    <?= $this->Form->text('customer_phone', [
                        'placeholder' => 'Ej: 3001234567 (opcional)',
                        'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 required-field',
                        'id' => 'customer-phone',
                        'list' => 'phones-list',
                        'autocomplete' => 'off',
                    ]) ?>
                    <i class="fa-solid fa-chevron-down absolute right-4 bottom-5 text-slate-300 pointer-events-none"></i>
                    <datalist id="phones-list">
                        <?php foreach ($clients as $c): ?>
                            <option value="<?= h($c->phone) ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <div id="venta-direccion-container">                    <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Dirección</label>
                    <?= $this->Form->text('customer_address', ['placeholder' => 'Calle, Barrio...', 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 required-field', 'id' => 'customer-address']) ?>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Método de Pago</label>
                    <?= $this->Form->select('payment_method', [
                        'Efectivo' => 'Efectivo 💵',
                        'Nequi' => 'Nequi 🟣',
                        'Daviplata' => 'Daviplata 🔴',
                        'Cuenta' => 'Cuenta/Transf 🏦',
                        'Crédito' => 'Crédito / Fiado 📝'
                    ], ['class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none font-bold text-slate-700 focus:ring-2 focus:ring-blue-500', 'default' => 'Efectivo']) ?>
                </div>
            </div>

            <!-- Selector de Productos (CARRITO) -->
            <div class="bg-slate-50 p-6 rounded-[2rem] mb-8 cart-required">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <div class="md:col-span-5 relative autocomplete-wrap" data-autocomplete="products">
                        <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Buscar Producto</label>
                        <input type="text" id="cart-product-search"
                            placeholder="Buscar producto..."
                            class="w-full p-4 bg-white border rounded-2xl outline-none font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 autocomplete-input required-field"
                            autocomplete="off">
                        <i class="fa-solid fa-chevron-down absolute right-4 bottom-5 text-slate-300 pointer-events-none"></i>
                        <ul class="autocomplete-dropdown hidden"></ul>
                        <input type="hidden" id="cart-product-id" value="">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Cantidad</label>
                        <?= $this->Form->number('temp_quantity', ['id' => 'cart-quantity', 'class' => 'w-full p-4 bg-white border rounded-2xl outline-none text-center font-bold text-slate-700 focus:ring-2 focus:ring-blue-500', 'value' => 1, 'min' => 1]) ?>
                    </div>
                    <div class="md:col-span-5">
                        <button type="button" id="btn-add-to-cart" class="w-full bg-blue-600 text-white font-black rounded-2xl py-4 uppercase shadow-lg hover:bg-blue-700 transition-all active:scale-95 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-plus-circle"></i> AGREGAR AL PEDIDO
                        </button>
                    </div>
                </div>

                <!-- Tabla del Carrito -->
                <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white">
                    <table class="w-full text-left border-collapse" id="cart-table">
                        <thead class="bg-slate-100 text-[9px] font-black uppercase text-slate-500 tracking-widest">
                            <tr>
                                <th class="p-4">Producto</th>
                                <th class="p-4 text-center">Cantidad</th>
                                <th class="p-4 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="cart-body">
                            <!-- Items dinámicos -->
                            <tr id="empty-cart-msg">
                                <td colspan="3" class="p-8 text-center text-slate-400 italic text-xs font-bold">No hay productos agregados todavía</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Configuración Final -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Tipo de Venta</label>
                    <?= $this->Form->select('type', ['local' => 'Punto Físico (Local)', 'domicilio' => 'Servicio a Domicilio 🛵'], ['class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-blue-500', 'id' => 'venta-tipo']) ?>
                </div>
                <div id="venta-envio-container">
                    <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Costo de Envío ($)</label>
                    <?= $this->Form->number('shipping_cost', ['class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 zero-empty required-field', 'placeholder' => '0', 'min' => 0]) ?>
                </div>
                <div id="venta-domiciliario-container">
                    <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Asignar Repartidor</label>
                    <?= $this->Form->select('delivery_driver_id', $deliveryDrivers, ['empty' => 'Seleccionar Repartidor...', 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 required-field', 'id' => 'delivery-driver']) ?>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100">
                <?= $this->Form->button(' CONFIRMAR Y FINALIZAR VENTA ', [
                    'id' => 'btn-submit-order',
                    'type' => 'button',
                    'class' => 'btn-finalize w-full bg-green-600 text-white font-black rounded-3xl py-6 uppercase shadow-2xl hover:bg-green-700 transition-all text-xl tracking-widest active:scale-95 disabled:opacity-50 disabled:pointer-events-none'
                ]) ?>
            </div>
        <?= $this->Form->end() ?>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderForm = document.getElementById('order-form');
        const cartBody = document.getElementById('cart-body');
        const emptyMsg = document.getElementById('empty-cart-msg');
        const btnAdd = document.getElementById('btn-add-to-cart');
        const btnSubmit = document.getElementById('btn-submit-order');
        
        const prodSearch = document.getElementById('cart-product-search');
        const prodIdHidden = document.getElementById('cart-product-id');
        const qtyInput = document.getElementById('cart-quantity');

        const tipoSelect = document.getElementById('venta-tipo');
        const domiciliarioContainer = document.getElementById('venta-domiciliario-container');
        const envioContainer = document.getElementById('venta-envio-container');
        const direccionContainer = document.getElementById('venta-direccion-container');
        
        const customerNameInput = document.getElementById('customer-name');
        const customerPhoneInput = document.getElementById('customer-phone');
        const customerAddressInput = document.getElementById('customer-address');

        // Datos para autocompletado
        const clientsData = <?= json_encode($clients) ?>;
        const productsData = <?= json_encode($products) ?>;
        let cartItems = [];

        function renderCart() {
            if (cartItems.length === 0) {
                emptyMsg.style.display = '';
                btnSubmit.disabled = true;
            } else {
                emptyMsg.style.display = 'none';
                btnSubmit.disabled = false;
            }

            // Limpiar filas viejas (excepto el mensaje vacío)
            Array.from(cartBody.querySelectorAll('tr:not(#empty-cart-msg)')).forEach(tr => tr.remove());

            cartItems.forEach((item, index) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-50 transition-colors';
                tr.innerHTML = `
                    <td class="p-4 font-bold text-slate-700 text-sm">
                        ${item.productName}
                        <input type="hidden" name="items[${index}][product_id]" value="${item.productId}">
                        <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                    </td>
                    <td class="p-4 text-center font-black text-blue-600">${item.quantity}</td>
                    <td class="p-4 text-right">
                        <button type="button" class="text-red-400 hover:text-red-600 transition-colors btn-remove" data-index="${index}">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                `;
                cartBody.appendChild(tr);
            });

            // Rebind remove buttons
            document.querySelectorAll('.btn-remove').forEach(btn => {
                btn.onclick = function() {
                    const idx = this.getAttribute('data-index');
                    cartItems.splice(idx, 1);
                    renderCart();
                };
            });
        }

        // === AUTOCOMPLETADO GENÉRICO ===
        function setupAutocomplete(wrapEl, items, onSelect) {
            const input = wrapEl.querySelector('.autocomplete-input');
            const dropdown = wrapEl.querySelector('.autocomplete-dropdown');
            let selectedIndex = -1;
            let filtered = [];

            function render(filter) {
                const q = filter.toLowerCase().trim();
                if (!q) {
                    filtered = items.slice(0, 100);
                } else {
                    filtered = items.filter(item => {
                        const label = item.label || item;
                        return label.toLowerCase().includes(q);
                    }).slice(0, 100);
                }
                if (filtered.length === 0 || (filtered.length === 1 && filtered[0].label === input.value)) {
                    dropdown.classList.add('hidden');
                    return;
                }
                dropdown.innerHTML = filtered.map((item, i) => {
                    const label = item.label || item;
                    const selected = i === selectedIndex ? 'bg-blue-100' : '';
                    return `<li class="px-4 py-3 cursor-pointer hover:bg-blue-50 font-bold text-xs text-slate-700 border-b border-slate-100 ${selected}" data-index="${i}">${label}</li>`;
                }).join('');
                dropdown.classList.remove('hidden');
            }

            function select(index) {
                const item = filtered[index];
                if (!item) return;
                const label = item.label || item;
                input.value = label;
                input.dispatchEvent(new Event('input', { bubbles: true }));
                if (onSelect) onSelect(item);
                dropdown.classList.add('hidden');
            }

            input.addEventListener('focus', function() {
                selectedIndex = -1;
                render('');
            });

            input.addEventListener('input', function() {
                selectedIndex = -1;
                if (this.value) {
                    if (onSelect) onSelect(null);
                }
                render(this.value);
            });

            input.addEventListener('keydown', function(e) {
                if (dropdown.classList.contains('hidden')) return;
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    selectedIndex = Math.min(selectedIndex + 1, filtered.length - 1);
                    render(input.value);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    selectedIndex = Math.max(selectedIndex - 1, 0);
                    render(input.value);
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (selectedIndex >= 0) select(selectedIndex);
                } else if (e.key === 'Escape') {
                    dropdown.classList.add('hidden');
                }
            });

            dropdown.addEventListener('click', function(e) {
                const li = e.target.closest('li');
                if (li) select(parseInt(li.dataset.index));
            });

            document.addEventListener('click', function(e) {
                if (!wrapEl.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        }

        // === AUTOCOMPLETADO DE CLIENTES ===
        setupAutocomplete(
            document.querySelector('[data-autocomplete="clients"]'),
            clientsData.map(c => ({ label: c.full_name, phone: c.phone, address: c.address })),
            function(item) {
                if (item) {
                    customerPhoneInput.value = item.phone || '';
                    customerAddressInput.value = item.address || '';
                    customerPhoneInput.dispatchEvent(new Event('input', { bubbles: true }));
                    customerAddressInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }
        );

        // === AUTOCOMPLETADO DE PRODUCTOS ===
        setupAutocomplete(
            document.querySelector('[data-autocomplete="products"]'),
            Object.entries(productsData).map(([id, name]) => ({ label: name, id: id })),
            function(item) {
                prodIdHidden.value = item ? item.id : '';
            }
        );

        btnAdd.addEventListener('click', function() {
            const productId = prodIdHidden.value;
            const productName = prodSearch.value;
            const quantity = parseInt(qtyInput.value);

            if (!productId || quantity < 1) {
                if (!productId && prodSearch.value) {
                    alert('Seleccione un producto válido de la lista');
                }
                return;
            }

            const existing = cartItems.find(i => i.productId === productId);
            if (existing) {
                existing.quantity += quantity;
            } else {
                cartItems.push({ productId, productName, quantity });
            }

            qtyInput.value = 1;
            prodSearch.value = '';
            prodIdHidden.value = '';
            prodSearch.dispatchEvent(new Event('input', { bubbles: true }));
            prodSearch.focus();
            renderCart();
        });

        btnSubmit.onclick = function() {
            if (cartItems.length === 0) {
                alert('Agregue al menos un producto');
                return;
            }
            if (!customerNameInput.value) {
                alert('Nombre del cliente es obligatorio');
                return;
            }
            if (customerNameInput.value !== 'Consumidor Final' && !customerPhoneInput.value) {
                alert('Si el cliente no es Consumidor Final, el celular es obligatorio');
                return;
            }
            if (!customerPhoneInput.value) {
                customerPhoneInput.value = 'N/A';
            }
            orderForm.submit();
        };

        function toggleDomicilioFields() {
            if (tipoSelect.value === 'local') {
                domiciliarioContainer.classList.add('hidden');
                envioContainer.classList.add('hidden');
                direccionContainer.classList.add('hidden');
            } else {
                domiciliarioContainer.classList.remove('hidden');
                envioContainer.classList.remove('hidden');
                direccionContainer.classList.remove('hidden');
            }
            document.querySelectorAll('.required-field').forEach(function(field) {
                if (field.value && field.value.toString().trim()) {
                    field.classList.add('field-filled');
                } else {
                    field.classList.remove('field-filled');
                }
            });
        }

        tipoSelect.addEventListener('change', toggleDomicilioFields);
        toggleDomicilioFields();
        renderCart();

        // === HIGHLIGHT CAMPOS REQUERIDOS ===
        document.querySelectorAll('.required-field').forEach(function(field) {
            function toggleHighlight() {
                if (field.value && field.value.toString().trim()) {
                    field.classList.add('field-filled');
                } else {
                    field.classList.remove('field-filled');
                }
            }
            toggleHighlight();
            field.addEventListener('input', toggleHighlight);
            field.addEventListener('change', toggleHighlight);
        });
        // Highlight carrito: al cambiar cartItems
        function highlightCart() {
            const cartArea = document.querySelector('.cart-required');
            if (cartArea) {
                if (cartItems.length > 0) {
                    cartArea.classList.add('field-filled');
                } else {
                    cartArea.classList.remove('field-filled');
                }
            }
        }
        const origRender = renderCart;
        renderCart = function() {
            origRender();
            highlightCart();
        };
        highlightCart();
    });
</script>
<style>
.autocomplete-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 50;
    max-height: 220px;
    overflow-y: auto;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
    margin-top: 4px;
}
.autocomplete-dropdown li:last-child {
    border-bottom: none !important;
}
.autocomplete-dropdown li:hover,
.autocomplete-dropdown li.bg-blue-100 {
    background-color: #eff6ff !important;
}
.required-field {
    border-color: #f59e0b !important;
    background-color: #fffbeb !important;
    transition: all 0.3s ease;
}
.required-field.field-filled {
    border-color: #10b981 !important;
    background-color: #ecfdf5 !important;
}
.required-field:focus {
    border-color: #3b82f6 !important;
    background-color: #fff !important;
}
select.required-field.field-filled {
    border-color: #10b981 !important;
    background-color: #ecfdf5 !important;
}
.cart-required {
    border: 2px solid #f59e0b;
    transition: all 0.3s ease;
}
.cart-required.field-filled {
    border-color: #10b981;
}
.zero-empty {
    color: transparent !important;
}
.zero-empty:focus, .zero-empty:not(:placeholder-shown) {
    color: inherit !important;
}
.btn-finalize:not(:disabled) {
    animation: pulse-glow 2s ease-in-out infinite;
    box-shadow: 0 0 25px -5px rgba(16, 185, 129, 0.5);
}
.btn-finalize:not(:disabled):hover {
    animation: none;
    box-shadow: 0 0 40px -5px rgba(16, 185, 129, 0.7);
}
@keyframes pulse-glow {
    0%, 100% { box-shadow: 0 0 20px -5px rgba(16, 185, 129, 0.4); transform: scale(1); }
    50% { box-shadow: 0 0 35px -5px rgba(16, 185, 129, 0.7); transform: scale(1.01); }
}
</style>
<?php else: ?>
    <header class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Mis Entregas Asignadas</h1>
            <p class="text-blue-600 font-bold uppercase text-xs tracking-widest italic">Gestiona tus pedidos en tiempo real</p>
        </div>
        
        <?php if (isset($driverEarnings)): ?>
            <div class="bg-slate-950 text-white p-4 rounded-[2rem] shadow-2xl flex items-center gap-4 border border-blue-500/20">
                <div class="bg-blue-600 p-2 w-10 h-10 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-sack-dollar text-white"></i>
                </div>
                <div>
                    <p class="text-[8px] font-black uppercase opacity-60 leading-none mb-1">Ganancia Acumulada</p>
                    <p class="text-xl font-black tracking-tighter text-blue-400">$<?= number_format($driverEarnings, 0) ?></p>
                </div>
            </div>
        <?php endif; ?>
    </header>
<?php endif; ?>

<div class="bg-white rounded-3xl border border-orange-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-900 text-white text-[10px] uppercase font-bold tracking-widest">
                <tr>
                    <th class="p-5">Producto</th>
                    <th class="p-5">Cliente / Datos</th>
                    <th class="p-5">Tipo / Pago</th>
                    <?php if ($isAdmin): ?>
                        <th class="p-5">Total</th>
                    <?php endif; ?>
                    <th class="p-5">Fecha y Hora</th>
                    <th class="p-5 text-right">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y text-sm">
                <?php 
                $groupedOrders = [];
                foreach ($orders as $order) {
                    $groupId = $order->order_group_id ?: 'SINGLE-' . $order->id;
                    if (!isset($groupedOrders[$groupId])) {
                        $groupedOrders[$groupId] = [
                            'info' => $order,
                            'items' => [],
                            'total' => 0
                        ];
                    }
                    $groupedOrders[$groupId]['items'][] = $order;
                    $groupedOrders[$groupId]['total'] += $order->total;
                }

                foreach ($groupedOrders as $groupId => $group): 
                    $mainOrder = $group['info'];
                    $subtotalProductos = 0;
                    $envioUnico = 0;
                    foreach ($group['items'] as $item) {
                        $subtotalProductos += ($item->total - $item->shipping_cost);
                        $envioUnico += $item->shipping_cost;
                    }
                ?>
                <tr class="hover:bg-orange-50 transition-colors">
                    <td class="p-4">
                        <div class="space-y-1">
                            <?php foreach ($group['items'] as $item): ?>
                                <div class="flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-[10px] font-black"><?= $item->quantity ?>x</span>
                                    <span class="font-bold text-slate-700 text-xs"><?= $item->hasValue('product') ? h($item->product->name) : '---' ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="font-bold text-slate-700 text-xs"><?= h($mainOrder->customer_name) ?></div>
                        <div class="text-[10px] text-slate-500 mt-0.5">
                            <i class="fa-solid fa-phone text-[9px] mr-1"></i> <?= h($mainOrder->customer_phone) ?> 
                            <?php if ($mainOrder->customer_address): ?>
                                <br><span class="text-blue-500"><i class="fa-solid fa-map-marker-alt text-[9px] mr-1 mt-1"></i> <?= h($mainOrder->customer_address) ?></span>
                            <?php endif; ?>
                            <?php if ($mainOrder->hasValue('delivery_driver')): ?>
                                <br><span class="text-orange-600 font-bold"><i class="fa-solid fa-motorcycle text-[9px] mr-1 mt-1"></i> <?= h($mainOrder->delivery_driver->full_name) ?></span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="p-4 text-xs">
                        <div class="mb-2">
                            <?php 
                            $statusColors = [
                                'recibido' => 'bg-slate-100 text-slate-600',
                                'pendiente' => 'bg-yellow-100 text-yellow-700 border border-yellow-300',
                                'en cocina' => 'bg-orange-100 text-orange-600',
                                'en camino' => 'bg-blue-100 text-blue-600',
                                'entregado' => 'bg-green-100 text-green-600'
                            ];
                            $color = $statusColors[$mainOrder->status] ?? 'bg-slate-100 text-slate-600';
                            ?>
                            <span class="px-3 py-1 rounded-full <?= $color ?> font-black uppercase block text-center text-[9px] mb-1">
                                <?= h($mainOrder->status) ?>
                            </span>
                        </div>

                        <!-- Botones de Flujo (aplican a todo el grupo) -->
                        <div class="flex gap-1 justify-center">
                            <?php if ($mainOrder->status === 'recibido'): ?>
                                <?= $this->Form->create(null, ['url' => ['action' => 'updateStatusGroup', $groupId, 'en cocina'], 'class' => 'inline']) ?>
                                    <button type="submit" class="bg-orange-500 text-white p-3 rounded-lg hover:bg-orange-600 text-sm" title="Mover todo a Cocina"><i class="fa-solid fa-fire-burner"></i></button>
                                <?= $this->Form->end() ?>
                            <?php elseif ($mainOrder->status === 'en cocina'): ?>
                                <?= $this->Form->create(null, ['url' => ['action' => 'updateStatusGroup', $groupId, 'en camino'], 'class' => 'inline']) ?>
                                    <button type="submit" class="bg-blue-500 text-white p-3 rounded-lg hover:bg-blue-600 text-sm" title="Enviar todo"><i class="fa-solid fa-motorcycle"></i></button>
                                <?= $this->Form->end() ?>
                            <?php elseif ($mainOrder->status === 'en camino'): ?>
                                <?= $this->Form->create(null, ['url' => ['action' => 'updateStatusGroup', $groupId, 'entregado'], 'class' => 'inline']) ?>
                                    <button type="submit" class="bg-green-500 text-white p-3 rounded-lg hover:bg-green-600 text-sm" title="Entregar todo"><i class="fa-solid fa-house-circle-check"></i></button>
                                <?= $this->Form->end() ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="p-4 text-xs">
                        <span class="px-3 py-1 rounded-full <?= $mainOrder->type === 'domicilio' ? 'bg-blue-100 text-blue-600 border border-blue-200' : 'bg-green-100 text-green-600 border border-green-200' ?> font-bold uppercase block mb-1 text-center">
                            <?= h($mainOrder->type) ?>
                        </span>
                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 font-bold uppercase block text-center border border-slate-200">
                            <?= h($mainOrder->payment_method) ?>
                        </span>
                    </td>
                    <?php if ($isAdmin): ?>
                        <td class="p-4">
                            <div class="text-[10px] text-slate-400 font-bold">Prod: $<?= number_format($subtotalProductos, 0) ?></div>
                            <?php if ($envioUnico > 0): ?>
                                <div class="text-[10px] text-blue-500 font-bold">Envío: $<?= number_format($envioUnico, 0) ?></div>
                            <?php endif; ?>
                            <div class="font-black text-orange-600 text-sm mt-1">$<?= number_format($subtotalProductos + $envioUnico, 0) ?></div>
                        </td>
                    <?php endif; ?>
                    <td class="p-4 text-[10px] text-slate-500 font-bold"><?= $mainOrder->created->format('d/m/Y h:i A') ?></td>
                    <td class="p-4 text-right flex justify-end gap-3 mt-1">
                        <?php if ($isAdmin || $isStaff): ?>
                            <?= $this->Html->link('<i class="fa-solid fa-print"></i>', ['action' => 'printTicketGroup', $groupId], ['escape' => false, 'target' => '_blank', 'class' => 'p-2 inline-block text-blue-500 hover:text-blue-700', 'title' => 'Imprimir Ticket Grupo']) ?>
                            <div class="flex flex-col gap-1">
                                <?php foreach ($group['items'] as $item): ?>
                                    <div class="flex gap-2 items-center justify-end">
                                        <span class="text-[8px] text-slate-400 font-bold">Item #<?= $item->id ?></span>
                                        <?php if ($isAdmin || $isStaff): ?>
                                            <?= $this->Html->link('<i class="fa-solid fa-pen"></i>', ['action' => 'edit', $item->id], ['escape' => false, 'class' => 'p-2 inline-block text-slate-400 hover:text-slate-600 text-[10px]']) ?>
                                            <?= $this->Form->postLink('<i class="fa-solid fa-trash"></i>', ['action' => 'delete', $item->id], ['confirm' => __('¿Eliminar item?'), 'escape' => false, 'class' => 'p-2 inline-block text-red-200 hover:text-red-500 text-[10px]']) ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<ul class="flex items-center justify-center gap-2 list-none p-0 m-0">
    <?= $this->Paginator->prev('<i class="fa-solid fa-chevron-left"></i>', ['escape' => false, 'templates' => ['prevActive' => '<a rel="prev" href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-sm text-slate-700 flex items-center justify-center">{{text}}</a>', 'prevDisabled' => '<span class="bg-slate-100 px-4 py-3 rounded-xl border border-slate-200 font-bold text-sm text-slate-300 flex items-center justify-center">{{text}}</span>']]) ?>
    <?= $this->Paginator->numbers(['templates' => ['number' => '<a href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-xs text-slate-700 flex items-center justify-center min-w-[2.5rem]">{{text}}</a>', 'current' => '<span class="bg-blue-600 text-white px-4 py-3 rounded-xl font-bold text-xs flex items-center justify-center min-w-[2.5rem]">{{text}}</span>', 'ellipsis' => '<span class="px-2 py-3 text-slate-400 font-bold text-xs">&hellip;</span>']]) ?>
    <?= $this->Paginator->next('<i class="fa-solid fa-chevron-right"></i>', ['escape' => false, 'templates' => ['nextActive' => '<a rel="next" href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-sm text-slate-700 flex items-center justify-center">{{text}}</a>', 'nextDisabled' => '<span class="bg-slate-100 px-4 py-3 rounded-xl border border-slate-200 font-bold text-sm text-slate-300 flex items-center justify-center">{{text}}</span>']]) ?>
</ul>
<div class="mt-4 text-center text-[10px] font-bold text-slate-400">
    <?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} de {{count}} pedidos')) ?>
</div>