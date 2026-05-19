<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Ingredient> $ingredients
 */
?>
<header class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Control de Inventario</h1>
        <p class="text-blue-600 font-bold uppercase text-xs tracking-widest">Gestión de insumos y materia prima</p>
    </div>
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <div class="bg-slate-900 text-white px-6 py-3 rounded-2xl shadow-lg">
            <span class="text-[10px] font-black text-yellow-400 uppercase tracking-widest">Inversión Total</span>
            <p class="text-2xl font-black mt-1">$<?= number_format($totalInversion, 0) ?></p>
        </div>
        <?= $this->Html->link('<i class="fa-solid fa-plus mr-2"></i> Nuevo Insumo', ['action' => 'add'], ['escape' => false, 'class' => 'bg-blue-600 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 text-center']) ?>
    </div>
</header>

<div class="bg-white rounded-3xl border border-slate-100 overflow-x-auto shadow-sm">
    <table class="w-full text-left">
        <thead class="bg-slate-900 text-white text-[10px] uppercase font-bold tracking-widest">
            <tr>
                <th class="p-5">Insumo</th>
                <th class="p-5 text-center">Costo Unitario</th>
                <th class="p-5 text-center">Stock Actual</th>
                <th class="p-5 text-center">Valor Total</th>
                <th class="p-5 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y text-sm">
            <?php foreach ($ingredients as $ingredient):
                $valorTotal = (float)$ingredient->cost * (float)$ingredient->stock;
            ?>
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="p-5">
                    <div class="font-black text-slate-800 uppercase text-sm"><?= h($ingredient->name) ?></div>
                    <div class="text-[10px] text-slate-400 font-bold uppercase"><?= h($ingredient->unit) ?></div>
                </td>
                <td class="p-5 text-center font-bold text-blue-600">
                    $<?= number_format($ingredient->cost, 2) ?>
                </td>
                <td class="p-5 text-center">
                    <span class="px-4 py-2 rounded-xl font-black text-lg <?= $ingredient->stock <= 5 ? 'bg-red-50 text-red-600' : 'bg-slate-50 text-slate-700' ?>">
                        <?= number_format($ingredient->stock, 1) ?>
                    </span>
                </td>
                <td class="p-5 text-center font-bold text-emerald-600">
                    $<?= number_format($valorTotal, 0) ?>
                </td>
                <td class="p-5 text-right">
                    <div class="flex justify-end gap-2 mt-1">
                        <?= $this->Html->link('<i class="fa-solid fa-pen"></i>', ['action' => 'edit', $ingredient->id], ['escape' => false, 'class' => 'p-3 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition-all']) ?>
                        
                        <?= $this->Html->link('<i class="fa-solid fa-book"></i>', ['controller' => 'Products', 'action' => 'index'], ['escape' => false, 'class' => 'p-3 bg-slate-50 text-slate-400 rounded-xl hover:bg-slate-200 transition-all', 'title' => 'Ver/Gestionar Recetas']) ?>

                        <?= $this->Form->postLink('<i class="fa-solid fa-trash"></i>', ['action' => 'delete', $ingredient->id], ['confirm' => __('¿Eliminar insumo?'), 'escape' => false, 'class' => 'p-3 text-red-200 hover:text-red-600']) ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot class="bg-slate-900 text-white">
            <tr>
                <td class="p-5 font-black uppercase text-yellow-400 tracking-widest">Total Inversión</td>
                <td></td>
                <td></td>
                <td class="p-5 text-center font-black text-yellow-400 text-lg">$<?= number_format($totalInversion, 0) ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<ul class="flex items-center justify-center gap-2 list-none p-0 m-0">
    <?= $this->Paginator->prev('<i class="fa-solid fa-chevron-left"></i>', ['escape' => false, 'templates' => ['prevActive' => '<a rel="prev" href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-sm text-slate-700 flex items-center justify-center">{{text}}</a>', 'prevDisabled' => '<span class="bg-slate-100 px-4 py-3 rounded-xl border border-slate-200 font-bold text-sm text-slate-300 flex items-center justify-center">{{text}}</span>']]) ?>
    <?= $this->Paginator->numbers(['templates' => ['number' => '<a href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-xs text-slate-700 flex items-center justify-center min-w-[2.5rem]">{{text}}</a>', 'current' => '<span class="bg-blue-600 text-white px-4 py-3 rounded-xl font-bold text-xs flex items-center justify-center min-w-[2.5rem]">{{text}}</span>', 'ellipsis' => '<span class="px-2 py-3 text-slate-400 font-bold text-xs">&hellip;</span>']]) ?>
    <?= $this->Paginator->next('<i class="fa-solid fa-chevron-right"></i>', ['escape' => false, 'templates' => ['nextActive' => '<a rel="next" href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-sm text-slate-700 flex items-center justify-center">{{text}}</a>', 'nextDisabled' => '<span class="bg-slate-100 px-4 py-3 rounded-xl border border-slate-200 font-bold text-sm text-slate-300 flex items-center justify-center">{{text}}</span>']]) ?>
</ul>
<div class="mt-4 text-center text-[10px] font-bold text-slate-400">
    <?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} de {{count}} insumos')) ?>
</div>
