<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\AccountsReceivable $account
 * @var \Cake\Collection\CollectionInterface $products
 */
$totalPaid = $account->total_paid;
$balance = $account->balance;
?>

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Detalle de Deuda</h1>
        <p class="text-blue-600 font-bold uppercase text-xs tracking-widest">
            Cliente: <?= h($account->client->full_name) ?> | Saldo: $<?= number_format($balance, 0) ?>
        </p>
        <p class="text-[10px] text-slate-400 font-bold italic mt-1"><?= h($account->description) ?></p>
    </div>
    <div class="flex gap-2">
        <?= $this->Html->link('<i class="fa-solid fa-arrow-left mr-2"></i> Volver', ['action' => 'index'], ['escape' => false, 'class' => 'bg-slate-100 text-slate-600 px-6 py-3 rounded-2xl font-black text-xs uppercase hover:bg-slate-200 transition-all']) ?>
        <?php if (!$isCliente && $balance > 0): ?>
            <?= $this->Html->link('<i class="fa-solid fa-hand-holding-dollar mr-2"></i> Registrar Abono', ['action' => 'payment', $account->id], ['escape' => false, 'class' => 'bg-green-600 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase hover:bg-green-700 transition-all shadow-lg']) ?>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Columna Izquierda: Productos en la deuda -->
    <div class="lg:col-span-2 space-y-8">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                <h3 class="font-black text-slate-900 uppercase text-sm tracking-widest">Productos & Servicios Cargados</h3>
                <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-[10px] font-black uppercase">
                    <?= count($account->orders) ?> Items
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-slate-400 text-[10px] uppercase font-bold tracking-widest">
                        <tr>
                            <th class="p-5">Producto/Servicio</th>
                            <th class="p-5">Cant.</th>
                            <th class="p-5">Precio</th>
                            <th class="p-5">Subtotal</th>
                            <th class="p-5">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-sm">
                        <?php foreach ($account->orders as $order): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-5">
                                <div class="font-black text-slate-800 uppercase"><?= h($order->product->name) ?></div>
                                <div class="text-[10px] text-slate-400 italic"><?= h($order->status) ?></div>
                            </td>
                            <td class="p-5 font-bold"><?= $order->quantity ?></td>
                            <td class="p-5 text-slate-400">$<?= number_format($order->product->price, 0) ?></td>
                            <td class="p-5 font-black text-slate-900">$<?= number_format($order->total, 0) ?></td>
                            <td class="p-5 text-[10px] font-bold text-slate-400"><?= $order->created->format('d/m/y H:i') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($account->orders)): ?>
                        <tr>
                            <td colspan="5" class="p-10 text-center text-slate-400 font-bold italic">
                                Esta deuda fue creada manualmente o no tiene productos asociados.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="bg-slate-900 text-white">
                        <tr>
                            <td colspan="3" class="p-5 font-black uppercase text-right">Total Deuda Acumulada:</td>
                            <td colspan="2" class="p-5 font-black text-lg">$<?= number_format($account->amount, 0) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <?php if (!$isCliente): ?>
            <!-- Formulario para agregar más productos -->
            <div class="bg-white rounded-[2.5rem] shadow-lg border-2 border-yellow-400 overflow-hidden">
                <div class="p-8 bg-yellow-400 flex items-center gap-4">
                    <div class="bg-slate-900 text-white p-3 rounded-2xl shadow-lg">
                        <i class="fa-solid fa-plus-circle text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-900 uppercase text-sm tracking-widest leading-none">Cargar más a esta Deuda</h3>
                        <p class="text-[10px] text-slate-800 font-bold uppercase mt-1">Fiar producto adicional al cliente</p>
                    </div>
                </div>
                <div class="p-8">
                    <?= $this->Form->create() ?>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                        <div class="md:col-span-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 mb-2 block">Seleccionar Insumo/Servicio</label>
                            <?= $this->Form->control('product_id', ['options' => $products, 'label' => false, 'class' => 'w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none focus:ring-2 focus:ring-blue-600 transition-all']) ?>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 mb-2 block">Cantidad</label>
                            <?= $this->Form->control('quantity', ['type' => 'number', 'value' => 1, 'min' => 1, 'label' => false, 'class' => 'w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none focus:ring-2 focus:ring-blue-600 transition-all']) ?>
                        </div>
                        <div>
                            <?= $this->Form->button(__('Cargar a Deuda'), ['class' => 'w-full bg-slate-900 text-white font-black rounded-2xl py-4 uppercase shadow-xl hover:bg-blue-600 transition-all tracking-widest text-xs']) ?>
                        </div>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Columna Derecha: Resumen de Pagos -->
    <div class="space-y-6">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8">
            <h3 class="font-black text-slate-900 uppercase text-xs tracking-widest mb-6 border-b pb-4">Historial de Abonos</h3>
            <div class="space-y-4">
                <?php foreach ($account->account_payments as $payment): ?>
                    <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <div>
                            <p class="text-[10px] font-black text-slate-800 uppercase">$<?= number_format($payment->amount, 0) ?></p>
                            <p class="text-[9px] text-slate-400 font-bold"><?= $payment->created->format('d/m/y') ?> - <?= h($payment->payment_method) ?></p>
                        </div>
                        <i class="fa-solid fa-circle-check text-green-500"></i>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($account->account_payments)): ?>
                    <div class="text-center py-6">
                        <p class="text-[10px] font-bold text-slate-300 uppercase italic">Sin abonos registrados</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="mt-8 pt-6 border-t border-slate-50 flex flex-col gap-3">
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-black text-slate-400 uppercase">Total Pagado:</span>
                    <span class="text-xs font-black text-green-600">$<?= number_format($totalPaid, 0) ?></span>
                </div>
                <div class="flex justify-between items-center p-4 bg-blue-600 rounded-2xl text-white shadow-lg">
                    <span class="text-[10px] font-black uppercase">Saldo Restante:</span>
                    <span class="text-lg font-black">$<?= number_format($balance, 0) ?></span>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden">
            <h3 class="font-black text-yellow-400 uppercase text-xs tracking-widest mb-4">Información del Cliente</h3>
            <div class="space-y-3 relative z-10">
                <div>
                    <p class="text-[9px] text-slate-500 uppercase font-black">Nombre Completo</p>
                    <p class="text-sm font-black"><?= h($account->client->full_name) ?></p>
                </div>
                <div>
                    <p class="text-[9px] text-slate-500 uppercase font-black">Teléfono / Celular</p>
                    <p class="text-sm font-black"><?= h($account->client->phone) ?></p>
                </div>
                <div>
                    <p class="text-[9px] text-slate-500 uppercase font-black">Dirección de Entrega</p>
                    <p class="text-xs font-bold text-slate-300"><?= h($account->client->address ?: 'No registrada') ?></p>
                </div>
            </div>
            <i class="fa-solid fa-user-tag absolute -bottom-6 -right-6 text-8xl opacity-10 rotate-12"></i>
        </div>
    </div>
</div>
