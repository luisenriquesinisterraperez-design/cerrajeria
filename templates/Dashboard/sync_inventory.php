<?php
/**
 * @var \App\View\AppView $this
 * @var array $report
 */
?>
<header class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Sincronizar Inventario</h1>
        <p class="text-orange-500 font-bold uppercase text-xs tracking-widest">Cuadrar stock con pedidos confirmados</p>
    </div>
    <?= $this->Html->link('<i class="fa-solid fa-arrow-left mr-2"></i> Volver al Dashboard', ['action' => 'index'], ['escape' => false, 'class' => 'bg-slate-100 text-slate-600 px-6 py-3 rounded-2xl font-black text-xs uppercase hover:bg-slate-200 transition-all']) ?>
</header>

<div class="bg-white rounded-3xl border border-slate-200 overflow-x-auto shadow-sm mb-6">
    <div class="p-5 border-b border-slate-100 flex items-center gap-2 bg-slate-50">
        <i class="fa-solid fa-circle-info text-blue-500"></i>
        <p class="text-xs font-bold text-slate-600">
            <span class="text-blue-600 font-black">Stock Correcto</span> = Stock Inicial − Usado en Pedidos.
            Si la <span class="text-red-600 font-black">Diferencia</span> es positiva, hay stock de más (no se descontó).
        </p>
    </div>
    <table class="w-full text-left">
        <thead class="bg-slate-900 text-white text-[9px] uppercase font-bold tracking-[0.2em]">
            <tr>
                <th class="p-5">Insumo</th>
                <th class="p-5 text-center">Stock Actual</th>
                <th class="p-5 text-center">Usado en Pedidos</th>
                <th class="p-5 text-center">Stock Inicial Ref.</th>
                <th class="p-5 text-center">Stock Correcto</th>
                <th class="p-5 text-center">Diferencia</th>
                <th class="p-5 text-center">Unidad</th>
            </tr>
        </thead>
        <tbody class="divide-y text-sm">
            <?php if (empty($report)): ?>
                <tr><td colspan="7" class="p-10 text-center text-slate-400 italic">No hay insumos registrados</td></tr>
            <?php else: ?>
                <?php foreach ($report as $r): 
                    $hasDiff = abs($r['diff']) > 0.01;
                ?>
                <tr class="hover:bg-slate-50 transition-colors <?= $hasDiff ? 'bg-red-50' : '' ?>">
                    <td class="p-5">
                        <div class="font-black text-slate-800 uppercase text-xs"><?= h($r['name']) ?></div>
                    </td>
                    <td class="p-5 text-center font-black <?= $r['current_stock'] <= 0 ? 'text-red-600' : 'text-slate-800' ?>">
                        <?= number_format($r['current_stock'], 1) ?>
                    </td>
                    <td class="p-5 text-center font-bold text-orange-600">
                        -<?= number_format($r['total_used'], 1) ?>
                    </td>
                    <td class="p-5 text-center font-bold text-slate-400">
                        <?= number_format($r['initial_stock'], 1) ?>
                    </td>
                    <td class="p-5 text-center font-black <?= $r['correct_stock'] < 0 ? 'text-red-600' : 'text-green-600' ?>">
                        <?= number_format($r['correct_stock'], 1) ?>
                    </td>
                    <td class="p-5 text-center">
                        <?php if ($hasDiff): ?>
                            <span class="font-black <?= $r['diff'] > 0 ? 'text-red-600' : 'text-blue-600' ?>">
                                <?= $r['diff'] > 0 ? '+' : '' ?><?= number_format($r['diff'], 1) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-green-500 font-black">✓</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-5 text-center text-slate-400 font-bold text-[10px] uppercase">
                        <?= h($r['unit']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if (!empty($report)): 
    $descuadrados = array_filter($report, fn($r) => abs($r['diff']) > 0.01);
?>
    <div class="flex items-center justify-between p-6 bg-slate-900 rounded-3xl text-white">
        <div>
            <p class="text-[10px] font-black uppercase opacity-60">Insumos con descuadre</p>
            <p class="text-2xl font-black"><?= count($descuadrados) ?> / <?= count($report) ?></p>
        </div>
        <?php if (!empty($descuadrados)): ?>
            <?= $this->Form->create(null, ['url' => ['action' => 'syncInventory']]) ?>
                <button type="submit" class="bg-orange-500 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase hover:bg-orange-600 transition-all shadow-xl tracking-widest"
                    onclick="return confirm('¿Corregir el stock de TODOS los insumos según los pedidos confirmados? Esta operación no se puede deshacer.');">
                    <i class="fa-solid fa-check-circle mr-2"></i> Corregir Todo el Inventario
                </button>
            <?= $this->Form->end() ?>
        <?php else: ?>
            <p class="text-green-400 font-black"><i class="fa-solid fa-check-circle mr-2"></i> Inventario Cuadrado</p>
        <?php endif; ?>
    </div>

    <!-- Debug info -->
    <details class="mt-6 p-4 bg-slate-100 rounded-2xl text-xs font-mono">
        <summary class="font-black text-slate-500 cursor-pointer">Ver datos crudos de la consulta SQL</summary>
        <pre class="mt-4 text-slate-600 overflow-x-auto"><?php
            echo htmlspecialchars(print_r($report, true));
        ?></pre>
    </details>
<?php endif; ?>
