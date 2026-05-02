<?php
/**
 * @var \App\View\AppView $this
 * @var bool $isRepartidor
 * @var int $deliveredInPeriod
 * @var float $totalEarned
 * @var int $pendingDeliveries
 * @var float $totalIncome
 * @var float $totalExpenses
 * @var float $totalCostOfSales
 * @var float $totalShipping
 * @var int $totalOrders
 * @var float $netProfit
 * @var array $salesByDay
 * @var array $driversRanking
 * @var array $topProducts
 * @var iterable<\App\Model\Entity\Ingredient> $lowStock
 * @var array $paymentTotals
 * @var string $startDate
 * @var string $endDate
 */
$user = $this->request->getAttribute('identity')->getOriginalData();
$isSuperAdmin = ($user && (!empty($user->is_superadmin) || $user->username === 'admin'));
$isAdminEmpresa = ($user && $user->role === 'admin_empresa');
$isAdmin = ($user && ($user->role === 'admin' || $isAdminEmpresa || $isSuperAdmin));
$isStaff = ($user && $user->role === 'staff');
?>

<?php if ($isRepartidor): ?>
    <!-- DASHBOARD EXCLUSIVO PARA REPARTIDOR -->
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight uppercase leading-none">Mi Panel</h1>
            <p class="text-blue-600 font-bold uppercase text-xs tracking-[0.2em] mt-2">Bienvenido, <?= h($user->username) ?></p>
        </div>

        <div class="flex items-center gap-2 bg-white p-2 rounded-2xl shadow-sm border border-slate-100">
            <?= $this->Form->create(null, ['type' => 'get', 'class' => 'flex items-center gap-2']) ?>
                <?= $this->Form->control('start_date', ['type' => 'date', 'value' => $startDate, 'label' => false, 'class' => 'text-[10px] font-black p-2 bg-slate-50 rounded-xl border-none outline-none']) ?>
                <span class="text-slate-300 font-bold">/</span>
                <?= $this->Form->control('end_date', ['type' => 'date', 'value' => $endDate, 'label' => false, 'class' => 'text-[10px] font-black p-2 bg-slate-50 rounded-xl border-none outline-none']) ?>
                <button type="submit" class="bg-blue-600 text-white p-2.5 rounded-xl hover:bg-blue-700 transition-all shadow-lg">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </button>
                <?= $this->Html->link('<i class="fa-solid fa-rotate-left"></i>', ['action' => 'index'], ['class' => 'bg-slate-100 text-slate-400 p-2.5 rounded-xl hover:bg-slate-200', 'escape' => false]) ?>
            <?= $this->Form->end() ?>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-blue-600 p-8 rounded-[2.5rem] shadow-xl shadow-blue-500/20 text-white relative overflow-hidden">
            <p class="text-blue-100 text-[10px] font-black uppercase mb-2 tracking-widest leading-none">Entregas Período</p>
            <p class="text-5xl font-black"><?= $deliveredInPeriod ?></p>
            <p class="text-[10px] font-bold mt-4 uppercase opacity-80">Pedidos completados</p>
            <i class="fa-solid fa-house-circle-check absolute -bottom-4 -right-4 text-7xl opacity-10 rotate-12"></i>
        </div>

        <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl text-white relative overflow-hidden">
            <p class="text-blue-400/60 text-[10px] font-black uppercase mb-2 tracking-widest leading-none">Ganancia Período</p>
            <p class="text-5xl font-black tracking-tighter text-blue-400">$<?= number_format($totalEarned, 0) ?></p>
            <p class="text-[10px] font-bold mt-4 uppercase opacity-60 italic">Por domicilios realizados</p>
            <i class="fa-solid fa-sack-dollar absolute -bottom-4 -right-4 text-7xl opacity-10 rotate-12"></i>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 relative overflow-hidden">
            <p class="text-slate-400 text-[10px] font-black uppercase mb-2 tracking-widest leading-none">Pendientes Hoy</p>
            <p class="text-5xl font-black text-slate-800"><?= $pendingDeliveries ?></p>
            <p class="text-[10px] text-orange-500 font-bold mt-4 uppercase italic">Ventas por entregar</p>
            <i class="fa-solid fa-motorcycle absolute -bottom-4 -right-4 text-7xl text-slate-100 rotate-12"></i>
        </div>
    </div>

    <div class="flex justify-center">
        <?= $this->Html->link('<i class="fa-solid fa-list-check mr-3"></i> IR A MIS ENTREGAS', ['controller' => 'Orders', 'action' => 'index'], ['escape' => false, 'class' => 'w-full md:w-auto bg-slate-950 text-white px-10 py-6 rounded-2xl font-black text-sm uppercase shadow-2xl hover:bg-blue-600 transition-all text-center tracking-widest']) ?>
    </div>

<?php else: ?>
    <!-- DASHBOARD COMPLETO PARA ADMINISTRADOR / STAFF -->
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Resumen de Operación</h1>
            <p class="text-red-600 font-bold uppercase text-xs tracking-widest">CERRAJERÍA MASTER Professional Edition</p>
        </div>
        
        <div class="flex flex-col md:flex-row items-center gap-2">
            <?php if ($isAdmin): ?>
            <div class="flex items-center gap-2 bg-white p-2 rounded-2xl shadow-sm border">
                <?= $this->Form->create(null, ['type' => 'get', 'class' => 'flex items-center gap-2']) ?>
                    <?= $this->Form->control('start_date', ['type' => 'date', 'value' => $startDate, 'label' => false, 'class' => 'text-xs font-bold p-2 outline-none border-none']) ?>
                    <span class="text-slate-300">/</span>
                    <?= $this->Form->control('end_date', ['type' => 'date', 'value' => $endDate, 'label' => false, 'class' => 'text-xs font-bold p-2 outline-none border-none']) ?>
                    <button type="submit" class="bg-blue-600 text-white p-2 rounded-xl hover:bg-blue-700 transition-colors">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <?= $this->Html->link('<i class="fa-solid fa-rotate-left"></i>', ['action' => 'index'], ['class' => 'bg-slate-100 text-slate-500 p-2 rounded-xl hover:bg-slate-200', 'escape' => false]) ?>
                <?= $this->Form->end() ?>
            </div>
            <?php endif; ?>
        </div>
    </header>

    <?php if ($isAdmin): ?>
    <!-- Matriz Financiera (Solo Admin) -->
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 text-center">
            <p class="text-slate-400 text-[9px] font-black uppercase mb-2 tracking-widest leading-none">Ingresos</p>
            <p class="text-xl font-black text-slate-800">$<?= number_format($totalIncome, 0) ?></p>
            <p class="text-[8px] text-green-500 font-bold mt-1 uppercase leading-none italic">Recibido</p>
        </div>
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 text-center">
            <p class="text-slate-400 text-[9px] font-black uppercase mb-2 tracking-widest leading-none">Domicilios</p>
            <p class="text-xl font-black text-blue-600">$<?= number_format($totalShipping, 0) ?></p>
            <p class="text-[8px] text-blue-400 font-bold mt-1 uppercase leading-none italic">Envíos</p>
        </div>
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 text-center">
            <p class="text-slate-400 text-[9px] font-black uppercase mb-2 tracking-widest leading-none">Insumos</p>
            <p class="text-xl font-black text-slate-800">$<?= number_format($totalCostOfSales, 0) ?></p>
            <p class="text-[8px] text-orange-500 font-bold mt-1 uppercase leading-none italic">Materias</p>
        </div>
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 text-center">
            <p class="text-slate-400 text-[9px] font-black uppercase mb-2 tracking-widest leading-none">Gastos</p>
            <p class="text-xl font-black text-slate-800">$<?= number_format($totalExpenses, 0) ?></p>
            <p class="text-[8px] text-red-500 font-bold mt-1 uppercase leading-none italic">Fijos</p>
        </div>
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 text-center">
            <p class="text-slate-400 text-[9px] font-black uppercase mb-2 tracking-widest leading-none">Pedidos</p>
            <p class="text-xl font-black text-slate-800"><?= $totalOrders ?></p>
            <p class="text-[8px] text-slate-500 font-bold mt-1 uppercase leading-none italic">Ventas</p>
        </div>
        <div class="bg-slate-900 p-6 rounded-[2rem] shadow-xl col-span-2 lg:col-span-1 text-center">
            <p class="text-blue-400/60 text-[9px] font-black uppercase mb-2 tracking-widest leading-none">Utilidad Neta</p>
            <p class="text-2xl font-black text-white">$<?= number_format($netProfit, 0) ?></p>
            <p class="text-[8px] text-green-400 font-bold mt-1 uppercase leading-none tracking-tighter italic">Real Limpia</p>
        </div>
    </div>
    <?php else: ?>
    <!-- Métrica Simplificada para Staff (Solo Pedidos) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-blue-600 p-8 rounded-[3rem] text-white shadow-xl flex items-center justify-between overflow-hidden relative">
            <div class="relative z-10">
                <p class="text-blue-100 text-[10px] font-black uppercase mb-2 tracking-widest">Ventas del Día</p>
                <p class="text-6xl font-black"><?= $totalOrders ?></p>
                <p class="text-[10px] font-bold mt-4 uppercase opacity-80">Órdenes gestionadas</p>
            </div>
            <i class="fa-solid fa-cart-shopping absolute -bottom-10 -right-10 text-[12rem] text-blue-500/20 rotate-12"></i>
        </div>
        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm flex items-center justify-center">
            <div class="text-center">
                <p class="text-slate-400 text-[10px] font-black uppercase mb-4 tracking-widest leading-none">Accesos Rápidos</p>
                <div class="flex gap-4">
                    <?= $this->Html->link('<i class="fa-solid fa-plus-circle mr-2"></i> Nueva Venta', ['controller' => 'Orders', 'action' => 'index'], ['escape' => false, 'class' => 'bg-slate-900 text-white px-6 py-4 rounded-2xl font-black text-xs uppercase hover:bg-blue-600 transition-all shadow-lg']) ?>
                    <?= $this->Html->link('<i class="fa-solid fa-boxes-stacked mr-2"></i> Inventario', ['controller' => 'Ingredients', 'action' => 'index'], ['escape' => false, 'class' => 'bg-slate-100 text-slate-600 px-6 py-4 rounded-2xl font-black text-xs uppercase hover:bg-slate-200 transition-all']) ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($isAdmin): ?>
    <!-- Disponibilidad por Cuenta (Solo Admin) -->
    <div class="mb-10">
        <h3 class="font-black text-slate-800 uppercase text-[10px] mb-4 flex items-center gap-2 tracking-[0.1em]">
            <i class="fa-solid fa-wallet text-slate-400"></i> Distribución de Efectivo
        </h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <?php 
            $methods = [
                'Efectivo' => ['icon' => '💵', 'color' => 'bg-emerald-50 text-green-700 border-green-100'],
                'Nequi' => ['icon' => '🟣', 'color' => 'bg-purple-50 text-purple-700 border-purple-100'],
                'Daviplata' => ['icon' => '🔴', 'color' => 'bg-rose-50 text-red-700 border-red-100'],
                'Cuenta' => ['icon' => '🏦', 'color' => 'bg-blue-50 text-blue-700 border-blue-100']
            ];
            foreach ($methods as $name => $style): 
                $amount = 0;
                foreach ($paymentTotals as $pt) { if ($pt->method === $name) { $amount = (float)$pt->total; break; } }
            ?>
            <div class="<?= $style['color'] ?> p-5 rounded-3xl border flex flex-col items-center justify-center shadow-sm">
                <span class="text-xl mb-1"><?= $style['icon'] ?></span>
                <p class="text-[9px] font-black uppercase opacity-70 mb-1 leading-none"><?= $name ?></p>
                <p class="text-lg font-black leading-none">$<?= number_format($amount, 0) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-10">
        <div class="lg:col-span-8">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <h3 class="font-black text-slate-800 uppercase text-[10px] mb-8 flex items-center gap-3 tracking-widest">
                    <i class="fa-solid fa-chart-line text-blue-500"></i> Tendencia de Caja
                </h3>
                <div class="h-[280px]"><canvas id="mainChart"></canvas></div>
            </div>
        </div>

        <!-- Panel de Inventario Crítico -->
        <div class="lg:col-span-4">
            <h3 class="font-black text-red-600 uppercase text-[10px] mb-4 flex items-center gap-2 tracking-[0.1em]">
                <i class="fa-solid fa-triangle-exclamation"></i> Prioridad: Stock Crítico
            </h3>
            <div class="bg-white p-6 rounded-[2rem] border border-red-100 shadow-xl shadow-red-500/5 space-y-3">
                <?php if (empty($lowStock)): ?>
                    <p class="text-[10px] text-slate-400 font-bold uppercase italic text-center py-4">Todo el inventario está al día</p>
                <?php else: ?>
                    <?php foreach ($lowStock as $item): ?>
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-2xl border border-red-100">
                        <div>
                            <p class="text-[10px] font-black text-slate-800 uppercase leading-none mb-1"><?= h($item->name) ?></p>
                            <p class="text-[8px] text-red-500 font-bold uppercase leading-none"><?= h($item->unit) ?></p>
                        </div>
                        <span class="text-lg font-black text-red-600"><?= number_format($item->stock, 1) ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Catálogo de Productos Más Vendidos -->
    <div class="mb-10">
        <h3 class="font-black text-slate-800 uppercase text-[10px] mb-6 flex items-center gap-2 tracking-[0.1em]">
            <i class="fa-solid fa-fire text-orange-500"></i> Top de Ventas (Productos más pedidos)
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <?php foreach ($topProducts as $i => $product): ?>
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden group hover:shadow-xl transition-all text-center p-4">
                <p class="text-[10px] font-black text-slate-800 uppercase truncate mb-1"><?= h($product['name']) ?></p>
                <span class="text-[10px] font-bold text-blue-600"><?= number_format($product['sold_count'] ?? 0, 0) ?> unidades</span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm flex flex-col">
        <h3 class="font-black text-slate-800 uppercase text-[10px] mb-6 tracking-widest flex items-center gap-2">
            <i class="fa-solid fa-trophy text-yellow-500"></i> Desempeño Logístico
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($driversRanking as $i => $driver): ?>
            <div class="flex items-center gap-3 p-3 rounded-2xl <?= $driver->is_local ? 'bg-blue-600 text-white' : 'bg-slate-50 border border-slate-100 text-slate-800' ?>">
                <div class="w-7 h-7 rounded-full flex items-center justify-center font-black <?= $driver->is_local ? 'bg-white/20 text-white' : ($i === 0 ? 'bg-yellow-100 text-yellow-600' : 'bg-slate-200 text-slate-500') ?> text-[10px]">
                    <?= $i + 1 ?>
                </div>
                <div class="flex-1">
                    <p class="text-[10px] font-black uppercase leading-none mb-1"><?= h($driver->name) ?></p>
                    <p class="text-[8px] <?= $driver->is_local ? 'text-blue-100' : 'text-slate-400' ?> font-bold uppercase leading-none">
                        <?= $driver->orders_count ?> pedidos 
                        <?php if ($isAdmin && !$driver->is_local): ?>
                            • <span class="text-blue-500 font-black">Envío: $<?= number_format($driver->shipping_earned, 0) ?></span>
                        <?php endif; ?>
                    </p>
                </div>
                <?php if ($isAdmin): ?>
                <div class="text-right">
                    <p class="text-[10px] font-black <?= $driver->is_local ? 'text-white' : 'text-blue-600' ?> leading-none">$<?= number_format($driver->total, 0) ?></p>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('mainChart').getContext('2d');
            const labels = <?= json_encode(array_column($salesByDay, 'date')) ?>;
            const data = <?= json_encode(array_column($salesByDay, 'total')) ?>;
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels.length > 0 ? labels : ['Sin Datos'],
                    datasets: [{
                        label: 'Ventas',
                        data: data.length > 0 ? data : [0],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 4,
                        pointRadius: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { font: { size: 9 }, callback: v => '$' + v.toLocaleString() } }, x: { ticks: { font: { size: 9 } } } }
                }
            });
        });
    </script>
<?php endif; ?>
