<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Product $product
 * @var mixed $ingredients
 */
?>
<header class="mb-8">
    <div class="flex items-center gap-4">
        <?= $this->Html->link('<i class="fa-solid fa-arrow-left"></i>', ['controller' => 'Products', 'action' => 'index'], ['escape' => false, 'class' => 'bg-slate-200 text-slate-600 w-10 h-10 rounded-xl flex items-center justify-center hover:bg-slate-300 transition-all']) ?>
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase italic">Configurar Receta</h1>
            <p class="text-blue-600 font-bold uppercase text-xs tracking-widest">Producto: <?= h($product->name) ?></p>
        </div>
    </div>
</header>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-6">
        <!-- Formulario para añadir -->
        <div class="bg-white p-6 rounded-3xl border border-blue-50 shadow-lg">
            <h3 class="text-xs font-black uppercase text-slate-400 mb-4 tracking-widest">Añadir Insumo a la Receta</h3>
            <?= $this->Form->create(null, ['class' => 'grid grid-cols-1 md:grid-cols-12 gap-4 items-end']) ?>
                <div class="md:col-span-4">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-2 mb-1 block">Insumo</label>
                    <?= $this->Form->control('ingredient_id', ['options' => $ingredients, 'label' => false, 'class' => 'w-full p-3 bg-slate-50 border rounded-xl outline-none focus:ring-2 focus:ring-blue-500 font-bold text-xs']) ?>
                </div>
                <div class="md:col-span-3">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-2 mb-1 block">Cant. Necesaria</label>
                    <?= $this->Form->control('quantity_required', ['label' => false, 'placeholder' => '0.00', 'class' => 'w-full p-3 bg-slate-50 border rounded-xl outline-none focus:ring-2 focus:ring-blue-500 font-black text-center text-xs']) ?>
                </div>
                <div class="md:col-span-3">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-2 mb-1 block">Actualizar Costo x Unidad ($)</label>
                    <?= $this->Form->control('cost_update', ['label' => false, 'placeholder' => 'Opcional', 'class' => 'w-full p-3 bg-blue-50 border border-blue-100 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 font-black text-center text-xs text-blue-600']) ?>
                </div>
                <div class="md:col-span-2">
                    <?= $this->Form->button(__('Añadir'), ['class' => 'w-full bg-blue-600 text-white font-black rounded-xl py-3.5 uppercase shadow-lg hover:bg-blue-700 transition-all text-[10px] tracking-widest']) ?>
                </div>
            <?= $this->Form->end() ?>
        </div>

        <!-- Tabla de la receta -->
        <div class="bg-white rounded-3xl border border-blue-50 overflow-x-auto shadow-sm">
            <table class="w-full text-left">
                <thead class="bg-slate-900 text-white text-[9px] uppercase font-bold tracking-[0.2em]">
                    <tr>
                        <th class="p-5">Insumo</th>
                        <th class="p-5 text-center">Cantidad</th>
                        <th class="p-5 text-center">Costo Línea</th>
                        <th class="p-5 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm">
                    <?php if (empty($product->product_ingredients)): ?>
                        <tr><td colspan="4" class="p-10 text-center text-slate-400 italic">No hay ingredientes en esta receta</td></tr>
                    <?php else: 
                        $totalRecipeCost = 0;
                        foreach ($product->product_ingredients as $pi): 
                            $lineCost = (float)$pi->quantity_required * (float)$pi->ingredient->cost;
                            $totalRecipeCost += $lineCost;
                        ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-5">
                                <div class="font-black text-slate-800 uppercase text-xs"><?= h($pi->ingredient->name) ?></div>
                                <div class="text-[9px] text-slate-400 font-bold uppercase">Costo Base: $<?= number_format($pi->ingredient->cost, 2) ?> / <?= h($pi->ingredient->unit) ?></div>
                            </td>
                            <td class="p-5 text-center font-black text-slate-700 text-xs"><?= number_format($pi->quantity_required, 2) ?></td>
                            <td class="p-5 text-center font-bold text-blue-600 text-xs">$<?= number_format($lineCost, 0) ?></td>
                            <td class="p-5 text-right">
                                <?= $this->Form->postLink('<i class="fa-solid fa-trash-alt"></i>', ['action' => 'delete', $pi->id], ['confirm' => __('¿Remover?'), 'escape' => false, 'class' => 'text-red-200 hover:text-red-600']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="bg-slate-900 text-white">
                            <td colspan="2" class="p-5 text-right font-black uppercase text-[10px] tracking-widest">Costo Total de Producción:</td>
                            <td class="p-5 text-center font-black text-sm">$<?= number_format($totalRecipeCost, 0) ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Análisis de Rentabilidad -->
    <div class="space-y-6">
        <div class="bg-slate-900 p-8 rounded-[2.5rem] text-white shadow-2xl relative overflow-hidden">
            <h3 class="text-[10px] font-black uppercase opacity-50 mb-8 tracking-[0.2em] flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-blue-400"></i> Análisis Financiero
            </h3>
            
            <div class="space-y-8 relative z-10">
                <div>
                    <span class="text-[9px] uppercase block opacity-60 font-black mb-1">Precio de Venta Sugerido</span>
                    <span class="text-4xl font-black text-orange-400 tracking-tighter">$<?= number_format($product->price, 0) ?></span>
                </div>
                
                <div>
                    <span class="text-[9px] uppercase block opacity-60 font-black mb-1">Costo Acumulado Insumos</span>
                    <span class="text-2xl font-black text-blue-400 tracking-tighter">$<?= number_format($totalRecipeCost ?? 0, 0) ?></span>
                </div>

                <div class="pt-8 border-t border-white/10">
                    <?php 
                    $profit = (float)$product->price - ($totalRecipeCost ?? 0);
                    $margin = ($product->price > 0) ? ($profit / $product->price) * 100 : 0;
                    ?>
                    <span class="text-[9px] uppercase block opacity-60 font-black mb-1">Utilidad Bruta x Plato</span>
                    <span class="text-5xl font-black <?= $profit > 0 ? 'text-green-400' : 'text-red-400' ?> tracking-tighter">$<?= number_format($profit, 0) ?></span>
                    
                    <div class="mt-4 inline-flex items-center gap-2 px-3 py-1 rounded-full <?= $profit > 0 ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' ?> text-[10px] font-black uppercase tracking-widest">
                        Margen: <?= number_format($margin, 1) ?>%
                    </div>
                </div>
            </div>
            
            <i class="fa-solid fa-sack-dollar absolute -bottom-10 -right-10 text-[12rem] text-white/5 rotate-12"></i>
        </div>
    </div>
</div>
