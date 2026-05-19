<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\InventoryAdjustment> $inventoryAdjustments
 */
?>
<header class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Ajustes & Bajas</h1>
        <p class="text-blue-600 font-bold uppercase text-xs tracking-widest">Control de mermas y correcciones</p>
    </div>
    <?= $this->Html->link('<i class="fa-solid fa-plus mr-2"></i> Registrar Baja/Ajuste', ['action' => 'add'], ['escape' => false, 'class' => 'bg-blue-600 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20']) ?>
</header>

<div class="bg-white rounded-3xl border border-slate-100 overflow-x-auto shadow-sm">
    <table class="w-full text-left">
        <thead class="bg-slate-900 text-white text-[10px] uppercase font-bold tracking-widest">
            <tr>
                <th class="p-5">Insumo</th>
                <th class="p-5 text-center">Tipo</th>
                <th class="p-5 text-center">Cantidad</th>
                <th class="p-5">Motivo / Razón</th>
                <th class="p-5">Fecha</th>
                <th class="p-5 text-right">Acción</th>
            </tr>
        </thead>
        <tbody class="divide-y text-sm">
            <?php foreach ($inventoryAdjustments as $adjustment): ?>
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="p-5">
                    <div class="font-black text-slate-800 uppercase text-sm">
                        <?= $adjustment->hasValue('ingredient') ? h($adjustment->ingredient->name) : 'Desconocido' ?>
                    </div>
                </td>
                <td class="p-5 text-center">
                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase <?= $adjustment->type === 'baja' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' ?>">
                        <?= h($adjustment->type) ?>
                    </span>
                </td>
                <td class="p-5 text-center font-black text-slate-700">
                    <?= $adjustment->type === 'baja' ? '-' : '+' ?><?= number_format($adjustment->quantity, 1) ?>
                </td>
                <td class="p-5">
                    <div class="text-xs font-bold text-slate-600 uppercase"><?= h($adjustment->reason) ?></div>
                    <div class="text-[10px] text-slate-400 italic"><?= h($adjustment->observations) ?></div>
                </td>
                <td class="p-5 text-[10px] text-slate-400 font-bold"><?= $adjustment->created->format('d/m/Y H:i') ?></td>
                <td class="p-5 text-right">
                    <?= $this->Form->postLink('<i class="fa-solid fa-trash"></i>', ['action' => 'delete', $adjustment->id], ['confirm' => __('¿Eliminar registro y revertir stock?'), 'escape' => false, 'class' => 'text-red-200 hover:text-red-600']) ?>
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
