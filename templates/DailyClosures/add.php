<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DailyClosure $dailyClosure
 * @var float $totalSales
 * @var float $totalAbonos
 * @var float $totalExpenses
 * @var float $netExpectedIncome
 * @var string $selectedDate
 */
?>
<header class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Nuevo Cierre de Caja</h1>
        <p class="text-orange-500 font-bold uppercase text-xs tracking-widest">Balance del <?= date('d/m/Y', strtotime($selectedDate)) ?></p>
    </div>
    <?= $this->Html->link('<i class="fa-solid fa-arrow-left mr-2"></i> Volver', ['action' => 'index'], ['escape' => false, 'class' => 'bg-slate-200 text-slate-600 px-4 py-2 rounded-xl font-bold text-xs hover:bg-slate-300 transition-all']) ?>
</header>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <div class="bg-white p-8 rounded-3xl border border-orange-100 shadow-lg">
            <?= $this->Form->create($dailyClosure) ?>
                <?= $this->Form->hidden('date', ['value' => $selectedDate]) ?>
                
                <div class="grid grid-cols-1 <?= $isAdmin ? 'md:grid-cols-2' : '' ?> gap-6 mb-6">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Saldo Base (Caja Inicial)</label>
                        <?= $this->Form->control('base_amount', ['label' => false, 'id' => 'base-amount', 'placeholder' => '0', 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all font-black text-slate-700']) ?>
                    </div>
                    <?php if ($isAdmin): ?>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Total en Caja Esperado</label>
                        <?= $this->Form->control('expected_amount', ['label' => false, 'id' => 'expected-amount', 'readonly' => true, 'class' => 'w-full p-4 bg-slate-100 border rounded-2xl outline-none font-black text-blue-600 cursor-not-allowed']) ?>
                    </div>
                    <?php else: ?>
                        <?= $this->Form->hidden('expected_amount', ['id' => 'expected-amount']) ?>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 <?= $isAdmin ? 'md:grid-cols-2' : '' ?> gap-6 mb-6">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Total Real Contado (Efectivo/Digital)</label>
                        <?= $this->Form->control('actual_amount', ['label' => false, 'id' => 'actual-amount', 'placeholder' => '0', 'class' => 'w-full p-4 bg-orange-50 border-2 border-orange-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all font-black text-slate-800 text-xl']) ?>
                    </div>
                    <?php if ($isAdmin): ?>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Diferencia (Sobrante/Faltante)</label>
                        <input type="text" id="difference-display" readonly class="w-full p-4 bg-slate-100 border rounded-2xl font-black text-xl outline-none cursor-not-allowed">
                        <?= $this->Form->hidden('difference', ['id' => 'difference-hidden']) ?>
                    </div>
                    <?php else: ?>
                        <?= $this->Form->hidden('difference', ['id' => 'difference-hidden']) ?>
                    <?php endif; ?>
                </div>

                <div class="mb-6">
                    <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Observaciones / Notas</label>
                    <?= $this->Form->control('observations', ['label' => false, 'placeholder' => 'Ej: Todo cuadró bien. Falta pagar una gaseosa...', 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all']) ?>
                </div>

                <?php if ($isAdmin): ?>
                <div class="pt-4 p-4 bg-slate-50 rounded-2xl mb-6 border border-slate-200">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-2">Resumen de cálculos:</p>
                    <p class="text-xs text-slate-600">
                        Esperado = Base + (Ventas Directas: $<?= number_format($totalSales, 0) ?>) + (Abonos de Fiados: $<?= number_format($totalAbonos, 0) ?>) - (Gastos: $<?= number_format($totalExpenses, 0) ?>)
                    </p>
                </div>
                <?php endif; ?>

                <div class="pt-6 border-t border-slate-50">
                    <?= $this->Form->button(__('Guardar Cierre de Caja'), ['class' => 'w-full bg-slate-950 text-white font-black rounded-2xl py-5 uppercase shadow-lg hover:bg-blue-600 transition-all text-lg tracking-widest']) ?>
                </div>
            <?= $this->Form->end() ?>
        </div>
    </div>

    <div class="space-y-4">
        <?php if ($isAdmin): ?>
        <div class="bg-white p-6 rounded-3xl border border-green-100 shadow-sm">
            <h4 class="text-[10px] font-black text-slate-400 uppercase mb-4 tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-money-bill-trend-up text-green-500"></i> Dinero Recibido Hoy
            </h4>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-500 uppercase">Ventas Directas</span>
                    <span class="font-black text-slate-800">$<?= number_format($totalSales, 0) ?></span>
                </div>
                <div class="flex justify-between items-center text-blue-600">
                    <span class="text-xs font-bold uppercase">Abonos de Deudas</span>
                    <span class="font-black">+$<?= number_format($totalAbonos, 0) ?></span>
                </div>
                <div class="flex justify-between items-center text-red-500">
                    <span class="text-xs font-bold uppercase">Gastos Pagados</span>
                    <span class="font-black">-$<?= number_format($totalExpenses, 0) ?></span>
                </div>
                <div class="pt-3 border-t border-slate-100 flex justify-between items-center">
                    <span class="text-xs font-black text-slate-800 uppercase">Ingreso Neto Real</span>
                    <span class="font-black text-orange-600">$<?= number_format($netExpectedIncome, 0) ?></span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="bg-blue-600 p-6 rounded-3xl text-white shadow-lg">
            <h4 class="text-[10px] font-black uppercase mb-2 opacity-80 tracking-widest italic">Instrucciones de Cierre</h4>
            <p class="text-[10px] leading-relaxed opacity-90">
                Por favor, cuente el dinero físico en caja y los saldos en cuentas digitales (Nequi/Daviplata) e ingrese el total en el campo <span class="font-bold underline text-white">Total Real Contado</span>.
            </p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const baseInput = document.getElementById('base-amount');
        const actualInput = document.getElementById('actual-amount');
        const expectedInput = document.getElementById('expected-amount');
        const diffDisplay = document.getElementById('difference-display');
        const diffHidden = document.getElementById('difference-hidden');

        const netDay = <?= $netExpectedIncome ?>;

        function calculate() {
            const base = parseFloat(baseInput.value) || 0;
            const actual = parseFloat(actualInput.value) || 0;
            
            const expected = base + netDay;
            if (expectedInput) expectedInput.value = expected.toFixed(2);
            
            const difference = actual - expected;
            if (diffHidden) diffHidden.value = difference.toFixed(2);
            
            if (diffDisplay) {
                diffDisplay.value = (difference >= 0 ? '+' : '') + '$' + Math.round(difference).toLocaleString();
                
                if (Math.abs(difference) < 1) { 
                    diffDisplay.className = diffDisplay.className.replace(/text-(red|blue|slate)-600/g, 'text-green-600');
                } else if (difference > 0) {
                    diffDisplay.className = diffDisplay.className.replace(/text-(green|red|slate)-600/g, 'text-blue-600');
                } else {
                    diffDisplay.className = diffDisplay.className.replace(/text-(green|blue|slate)-600/g, 'text-red-600');
                }
            }
        }

        baseInput.addEventListener('input', calculate);
        actualInput.addEventListener('input', calculate);
        
        calculate();
    });
</script>
