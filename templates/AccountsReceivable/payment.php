<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\AccountsReceivable $account
 */
$totalPaid = $account->total_paid;
$balance = $account->balance;
?>
<header class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Registrar Abono</h1>
        <p class="text-orange-500 font-bold uppercase text-xs tracking-widest">Cliente: <?= h($account->client->full_name) ?></p>
    </div>
    <?= $this->Html->link('<i class="fa-solid fa-arrow-left mr-2"></i> Volver', ['action' => 'index'], ['escape' => false, 'class' => 'bg-slate-200 text-slate-600 px-4 py-2 rounded-xl font-bold text-xs hover:bg-slate-300 transition-all']) ?>
</header>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <div class="bg-white p-8 rounded-3xl border border-orange-100 shadow-lg mb-8">
            <h3 class="font-black text-slate-800 uppercase text-sm mb-6">Nuevo Abono</h3>
            <?= $this->Form->create($payment) ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Monto a Abonar ($)</label>
                        <?= $this->Form->control('amount', ['label' => false, 'value' => $balance, 'class' => 'w-full p-4 bg-orange-50 border-2 border-orange-200 rounded-2xl outline-none focus:ring-2 focus:ring-orange-500 transition-all font-black text-slate-800 text-xl']) ?>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Método de Pago</label>
                        <?= $this->Form->select('payment_method', [
                            'Efectivo' => 'Efectivo 💵',
                            'Nequi' => 'Nequi 🟣',
                            'Daviplata' => 'Daviplata 🔴'
                        ], ['class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none font-bold text-slate-700', 'default' => 'Efectivo']) ?>
                    </div>
                </div>
                <?= $this->Form->button(__('Confirmar Abono'), ['class' => 'w-full bg-green-600 text-white font-black rounded-2xl py-4 uppercase shadow-lg hover:bg-green-700 transition-all']) ?>
            <?= $this->Form->end() ?>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 overflow-x-auto shadow-sm">
            <h3 class="p-6 font-black text-slate-800 uppercase text-xs border-b">Historial de Pagos</h3>
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 tracking-widest">
                    <tr>
                        <th class="p-4">Fecha</th>
                        <th class="p-4">Método</th>
                        <th class="p-4 text-right">Monto</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm">
                    <?php if (empty($account->account_payments)): ?>
                        <tr><td colspan="3" class="p-8 text-center text-slate-400 italic">No hay abonos registrados</td></tr>
                    <?php else: ?>
                        <?php foreach ($account->account_payments as $payment): ?>
                        <tr>
                            <td class="p-4 text-xs font-bold text-slate-500"><?= $payment->created->format('d/m/Y h:i A') ?></td>
                            <td class="p-4 text-xs font-black uppercase text-slate-700"><?= h($payment->payment_method) ?></td>
                            <td class="p-4 text-right font-black text-green-600">$<?= number_format($payment->amount, 0) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="space-y-4">
        <div class="bg-slate-900 p-8 rounded-3xl text-white shadow-xl">
            <p class="text-[10px] font-black uppercase opacity-50 mb-4 tracking-widest">Resumen de Deuda</p>
            <div class="space-y-4">
                <div>
                    <span class="text-[10px] uppercase block opacity-70">Total Deuda</span>
                    <span class="text-2xl font-black">$<?= number_format($account->amount, 0) ?></span>
                </div>
                <div class="text-green-400">
                    <span class="text-[10px] uppercase block opacity-70">Total Pagado</span>
                    <span class="text-2xl font-black">$<?= number_format($totalPaid, 0) ?></span>
                </div>
                <div class="pt-4 border-t border-white/10 text-orange-400">
                    <span class="text-[10px] uppercase block opacity-70">Saldo Pendiente</span>
                    <span class="text-3xl font-black">$<?= number_format($balance, 0) ?></span>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-3xl border border-orange-100">
            <h4 class="text-xs font-black text-slate-800 uppercase mb-2 italic">Concepto</h4>
            <p class="text-xs text-slate-500 leading-relaxed"><?= h($account->description) ?></p>
        </div>
    </div>
</div>
