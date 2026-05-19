<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Expense $expense
 */
?>
<header class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Editar Gasto</h1>
        <p class="text-orange-500 font-bold uppercase text-xs tracking-widest">Corrección de registros contables</p>
    </div>
    <?= $this->Html->link('<i class="fa-solid fa-arrow-left mr-2"></i> Volver', ['action' => 'index'], ['escape' => false, 'class' => 'bg-slate-200 text-slate-600 px-4 py-2 rounded-xl font-bold text-xs hover:bg-slate-300 transition-all']) ?>
</header>

<div class="bg-white p-8 rounded-3xl border border-orange-100 shadow-lg max-w-2xl">
    <?= $this->Form->create($expense) ?>
        <div class="space-y-6">
            <div>
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Descripción del Gasto</label>
                <?= $this->Form->control('description', ['label' => false, 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-blue-600 transition-all font-bold text-slate-700']) ?>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Monto del Gasto ($)</label>
                <?= $this->Form->control('amount', ['label' => false, 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-blue-600 transition-all font-black text-red-600']) ?>
                </div>

                <div class="flex flex-col gap-1">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Fecha</label>
                <?= $this->Form->control('date', ['type' => 'date', 'label' => false, 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-blue-600 transition-all']) ?>
                </div>
                </div>

                <div class="pt-8 mt-8 border-t border-slate-50 flex gap-4">
                <?= $this->Form->button(__('Actualizar Gasto'), ['class' => 'flex-1 bg-blue-600 text-white font-black rounded-2xl py-4 uppercase shadow-lg hover:bg-slate-900 transition-all']) ?>
                <?= $this->Form->postLink(__('Eliminar'), ['action' => 'delete', $expense->id], ['confirm' => __('¿Eliminar este gasto?'), 'class' => 'px-6 bg-red-50 text-red-500 font-bold rounded-2xl py-4 uppercase hover:bg-red-100 transition-all text-xs']) ?>
                </div>
        </div>
    <?= $this->Form->end() ?>
</div>
