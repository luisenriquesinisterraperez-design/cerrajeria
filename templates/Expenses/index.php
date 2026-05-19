<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Expense> $expenses
 */
?>
<header class="mb-8">
    <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Egresos y Gastos</h1>
    <p class="text-orange-500 font-bold uppercase text-xs tracking-widest">Salidas de dinero</p>
</header>

<div class="bg-white p-8 rounded-3xl border border-blue-100 mb-10 shadow-sm">
    <h3 class="font-black text-slate-800 uppercase text-sm mb-6 flex items-center gap-2">
        <i class="fa-solid fa-file-invoice-dollar text-blue-600"></i> Registrar Nuevo Gasto
    </h3>
    <?= $this->Form->create(null, ['url' => ['action' => 'add']]) ?>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2 flex flex-col gap-1">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Descripción</label>
                <?= $this->Form->control('description', ['label' => false, 'placeholder' => 'Ej: Compra de Carne', 'class' => 'p-4 bg-slate-50 border rounded-2xl outline-none focus:bg-white focus:ring-2 focus:ring-blue-600 transition-all']) ?>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Monto ($)</label>
                <?= $this->Form->control('amount', ['label' => false, 'placeholder' => '0.00', 'class' => 'p-4 bg-slate-50 border rounded-2xl outline-none focus:bg-white focus:ring-2 focus:ring-blue-600 transition-all font-black text-red-600']) ?>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Fecha</label>
                <?= $this->Form->control('date', ['type' => 'date', 'label' => false, 'class' => 'p-4 bg-slate-50 border rounded-2xl outline-none focus:bg-white focus:ring-2 focus:ring-blue-600 transition-all', 'value' => date('Y-m-d')]) ?>
            </div>
        </div>
        <div class="mt-4">
            <?= $this->Form->button(__('Registrar Gasto'), ['class' => 'w-full bg-blue-600 text-white font-black rounded-2xl py-4 uppercase hover:bg-slate-900 transition-all shadow-lg tracking-widest']) ?>
        </div>
    <?= $this->Form->end() ?>
</div>

<div class="bg-white rounded-3xl border border-blue-100 overflow-x-auto shadow-sm">
    <table class="w-full text-left">
        <thead class="bg-slate-900 text-white text-[10px] uppercase font-bold tracking-widest">
            <tr>
                <th class="p-5">Descripción</th>
                <th class="p-5">Monto</th>
                <th class="p-5">Fecha</th>
                <th class="p-5 text-right">Acción</th>
            </tr>
        </thead>
        <tbody class="divide-y text-sm">
            <?php foreach ($expenses as $expense): ?>
            <tr class="hover:bg-blue-50 transition-colors">
                <td class="p-5 font-bold uppercase text-slate-700 text-xs"><?= h($expense->description) ?></td>
                <td class="p-5 font-black text-red-600">$<?= number_format($expense->amount, 2) ?></td>
                <td class="p-5 text-[10px] text-slate-400 font-bold"><?= $expense->date->format('d/m/Y') ?></td>
                <td class="p-5 text-right">
                    <?= $this->Form->postLink('<i class="fa-solid fa-trash"></i>', ['action' => 'delete', $expense->id], ['confirm' => __('¿Eliminar este gasto?'), 'escape' => false, 'class' => 'text-red-200 hover:text-red-500 transition-colors']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<ul class="flex items-center justify-center gap-2 list-none p-0 m-0">
    <?= $this->Paginator->prev('<i class="fa-solid fa-chevron-left"></i>', ['escape' => false, 'templates' => ['prevActive' => '<a rel="prev" href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-sm text-slate-700 flex items-center justify-center">{{text}}</a>', 'prevDisabled' => '<span class="bg-slate-100 px-4 py-3 rounded-xl border border-slate-200 font-bold text-sm text-slate-300 flex items-center justify-center">{{text}}</span>']]) ?>
    <?= $this->Paginator->numbers(['templates' => ['number' => '<a href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-xs text-slate-700 flex items-center justify-center min-w-[2.5rem]">{{text}}</a>', 'current' => '<span class="bg-blue-600 text-white px-4 py-3 rounded-xl font-bold text-xs flex items-center justify-center min-w-[2.5rem]">{{text}}</span>', 'ellipsis' => '<span class="px-2 py-3 text-slate-400 font-bold text-xs">&hellip;</span>']]) ?>
    <?= $this->Paginator->next('<i class="fa-solid fa-chevron-right"></i>', ['escape' => false, 'templates' => ['nextActive' => '<a rel="next" href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-sm text-slate-700 flex items-center justify-center">{{text}}</a>', 'nextDisabled' => '<span class="bg-slate-100 px-4 py-3 rounded-xl border border-slate-200 font-bold text-sm text-slate-300 flex items-center justify-center">{{text}}</span>']]) ?>
</ul>
<div class="mt-4 text-center text-[10px] font-bold text-slate-400">
    <?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} de {{count}} registros')) ?>
</div>
