<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\AccountsReceivable> $accountsReceivable
 */
?>
<header class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Cuentas por Cobrar</h1>
        <p class="text-orange-500 font-bold uppercase text-xs tracking-widest">Gestión de deudas y créditos</p>
    </div>
    <?php if (!$isCliente): ?>
        <?= $this->Html->link('<i class="fa-solid fa-plus mr-2"></i> Nueva Deuda Manual', ['action' => 'add'], ['escape' => false, 'class' => 'bg-slate-900 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase hover:bg-orange-600 transition-all shadow-lg']) ?>
    <?php endif; ?>
</header>

<div class="bg-white rounded-3xl border border-orange-100 overflow-x-auto shadow-sm">
    <table class="w-full text-left">
        <thead class="bg-slate-900 text-white text-[10px] uppercase font-bold tracking-widest">
            <tr>
                <th class="p-5">Cliente</th>
                <th class="p-5">Total Deuda</th>
                <th class="p-5">Saldo Pendiente</th>
                <th class="p-5">Estado</th>
                <th class="p-5">Fecha</th>
                <th class="p-5 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y text-sm">
            <?php foreach ($accountsReceivable as $account): 
                $totalPaid = 0;
                foreach ($account->account_payments as $p) $totalPaid += (float)$p->amount;
                $balance = $account->amount - $totalPaid;
            ?>
            <tr class="hover:bg-slate-50 transition-colors <?= $account->status === 'pagado' ? 'opacity-60 bg-green-50/30' : '' ?>">
                <td class="p-5">
                    <div class="font-black text-slate-800 uppercase text-sm">
                        <?= $account->hasValue('client') ? h($account->client->full_name) : 'Desconocido' ?>
                    </div>
                    <div class="text-[10px] text-slate-400 font-bold italic"><?= h($account->description) ?></div>
                </td>
                <td class="p-5 font-bold text-slate-400">$<?= number_format($account->amount, 0) ?></td>
                <td class="p-5 font-black <?= $balance > 0 ? 'text-orange-600' : 'text-green-600' ?>">
                    $<?= number_format($balance, 0) ?>
                </td>
                <td class="p-5">
                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase <?= $account->status === 'pagado' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                        <?= h($account->status) ?>
                    </span>
                </td>
                <td class="p-5 text-[10px] text-slate-400 font-bold"><?= $account->created->format('d/m/Y') ?></td>
                <td class="p-5 text-right flex justify-end gap-2 mt-2">
                    <?= $this->Html->link('<i class="fa-solid fa-eye"></i> Ver', ['action' => 'view', $account->id], ['escape' => false, 'class' => 'bg-blue-600 text-white px-3 py-1.5 rounded-xl text-[10px] font-black uppercase hover:bg-yellow-400 hover:text-slate-900 transition-all shadow-sm']) ?>

                    <?php if (!$isCliente): ?>
                        <?php if ($account->status === 'pendiente'): ?>
                            <?= $this->Html->link('<i class="fa-solid fa-hand-holding-dollar"></i> Abonar', ['action' => 'payment', $account->id], ['escape' => false, 'class' => 'bg-green-600 text-white px-3 py-1.5 rounded-xl text-[10px] font-black uppercase hover:bg-green-700 transition-all shadow-sm']) ?>
                        <?php endif; ?>
                        
                        <?= $this->Form->postLink('<i class="fa-solid fa-trash"></i>', ['action' => 'delete', $account->id], ['confirm' => __('¿Eliminar este registro?'), 'escape' => false, 'class' => 'text-red-200 hover:text-red-600 p-3']) ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
