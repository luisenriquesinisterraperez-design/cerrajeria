<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\DailyClosure> $dailyClosures
 */
?>
<header class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Cierres de Caja</h1>
        <p class="text-orange-500 font-bold uppercase text-xs tracking-widest">Cuadre de cuentas diario</p>
    </div>
    <?= $this->Html->link('<i class="fa-solid fa-cash-register mr-2"></i> Nuevo Cierre', ['action' => 'add'], ['escape' => false, 'class' => 'bg-orange-500 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase hover:bg-orange-600 transition-all shadow-lg']) ?>
</header>

<div class="bg-white rounded-3xl border border-orange-100 overflow-x-auto shadow-sm">
    <table class="w-full text-left">
        <thead class="bg-slate-900 text-white text-[10px] uppercase font-bold tracking-widest">
            <tr>
                <th class="p-5">Fecha</th>
                <th class="p-5">Base</th>
                <th class="p-5">Esperado</th>
                <th class="p-5">Real</th>
                <th class="p-5">Diferencia</th>
                <th class="p-5 text-right">Acción</th>
            </tr>
        </thead>
        <tbody class="divide-y text-sm">
            <?php foreach ($dailyClosures as $closure): ?>
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="p-5">
                    <div class="font-black text-slate-800 uppercase text-xs"><?= $closure->date->format('d/m/Y') ?></div>
                    <div class="text-[10px] text-slate-400 font-bold"><?= $closure->created->format('h:i A') ?></div>
                </td>
                <td class="p-5 font-bold text-slate-500">$<?= number_format($closure->base_amount, 0) ?></td>
                <td class="p-5 font-bold text-blue-600">$<?= number_format($closure->expected_amount, 0) ?></td>
                <td class="p-5 font-black text-slate-800">$<?= number_format($closure->actual_amount, 0) ?></td>
                <td class="p-5">
                    <?php if ($closure->difference == 0): ?>
                        <span class="text-green-600 font-black"><i class="fa-solid fa-check-circle"></i> Cuadrado</span>
                    <?php elseif ($closure->difference > 0): ?>
                        <span class="text-blue-600 font-black"><i class="fa-solid fa-plus-circle"></i> +$<?= number_format($closure->difference, 0) ?></span>
                    <?php else: ?>
                        <span class="text-red-600 font-black"><i class="fa-solid fa-minus-circle"></i> -$<?= number_format(abs($closure->difference), 0) ?></span>
                    <?php endif; ?>
                </td>
                <td class="p-5 text-right">
                    <?= $this->Form->postLink('<i class="fa-solid fa-trash"></i>', ['action' => 'delete', $closure->id], ['confirm' => __('¿Eliminar este cierre?'), 'escape' => false, 'class' => 'text-red-200 hover:text-red-600']) ?>
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
