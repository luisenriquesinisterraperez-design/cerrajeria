<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order $order
 * @var string[]|\Cake\Collection\CollectionInterface $products
 * @var string[]|\Cake\Collection\CollectionInterface $deliveryDrivers
 */
?>
<header class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Editar Pedido</h1>
        <p class="text-orange-500 font-bold uppercase text-xs tracking-widest">Modificar detalles de venta</p>
    </div>
    <?= $this->Html->link('<i class="fa-solid fa-arrow-left mr-2"></i> Volver', ['action' => 'index'], ['escape' => false, 'class' => 'bg-slate-200 text-slate-600 px-4 py-2 rounded-xl font-bold text-xs hover:bg-slate-300 transition-all']) ?>
</header>

<div class="bg-white p-8 rounded-3xl border border-orange-100 shadow-lg">
    <?= $this->Form->create($order) ?>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
            <div class="md:col-span-4">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Producto</label>
                <?= $this->Form->control('product_id', ['options' => $products, 'label' => false, 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-orange-500 transition-all font-bold text-slate-700']) ?>
            </div>
            <div class="md:col-span-2">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Cantidad</label>
                <?= $this->Form->control('quantity', ['label' => false, 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none text-center font-bold text-slate-700 focus:ring-2 focus:ring-orange-500']) ?>
            </div>
            <div class="md:col-span-3">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Tipo</label>
                <?= $this->Form->control('type', ['options' => ['local' => 'Local', 'domicilio' => 'Domicilio'], 'label' => false, 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-orange-500']) ?>
            </div>
            <div class="md:col-span-3">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Envío ($)</label>
                <?= $this->Form->control('shipping_cost', ['label' => false, 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-orange-500 font-bold text-orange-600']) ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Nombre del Cliente</label>
                <?= $this->Form->control('customer_name', ['label' => false, 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-orange-500 transition-all']) ?>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Celular</label>
                <?= $this->Form->control('customer_phone', ['label' => false, 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-orange-500 transition-all']) ?>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Domiciliario</label>
                <?= $this->Form->control('delivery_driver_id', ['options' => $deliveryDrivers, 'empty' => true, 'label' => false, 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-orange-500']) ?>
            </div>
        </div>
        
        <div class="mb-8">
            <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Dirección de Entrega</label>
            <?= $this->Form->control('customer_address', ['label' => false, 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-orange-500 transition-all']) ?>
        </div>
        
        <div class="flex gap-4 pt-6 border-t border-slate-50">
            <?= $this->Form->button(__('Guardar Cambios'), ['class' => 'flex-1 bg-green-600 text-white font-black rounded-2xl py-4 uppercase shadow-lg hover:bg-green-700 active:scale-95 transition-all']) ?>
            <?= $this->Form->postLink(__('Eliminar Pedido'), ['action' => 'delete', $order->id], ['confirm' => __('¿Eliminar este pedido definitivamente?'), 'class' => 'px-6 bg-red-50 text-red-500 font-bold rounded-2xl py-4 uppercase hover:bg-red-100 transition-all text-xs']) ?>
        </div>
    <?= $this->Form->end() ?>
</div>

<?php if ($isAdmin): ?>
<div class="mt-10">
    <h3 class="text-xl font-black text-slate-800 uppercase mb-4 flex items-center gap-2">
        <i class="fa-solid fa-clock-rotate-left text-orange-500"></i> Historial de Cambios (Huella)
    </h3>
    <div class="bg-slate-50 rounded-3xl border border-slate-200 overflow-x-auto shadow-inner">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-200 text-slate-600 text-[10px] uppercase font-bold tracking-widest">
                <tr>
                    <th class="p-4">Fecha</th>
                    <th class="p-4">Usuario</th>
                    <th class="p-4">Detalles</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php if (!empty($order->order_logs)): ?>
                    <?php foreach ($order->order_logs as $log): ?>
                        <tr>
                            <td class="p-4 text-slate-500 font-bold text-[11px] w-40">
                                <?= $log->created->format('d/m/Y h:i A') ?>
                            </td>
                            <td class="p-4 w-40">
                                <span class="bg-white border text-slate-600 px-3 py-1 rounded-full font-black text-[9px] uppercase">
                                    <?= $log->hasValue('user') ? h($log->user->username) : 'Sistema' ?>
                                </span>
                            </td>
                            <td class="p-4 text-slate-600 italic">
                                <?= h($log->modification_details) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="p-8 text-center text-slate-400 italic font-bold">
                            No hay modificaciones registradas para este pedido.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
