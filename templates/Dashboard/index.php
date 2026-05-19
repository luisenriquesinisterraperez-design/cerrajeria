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
$isStaff = ($user && $user->role === 'staff'); // Explicitly check for staff role
?>

<?php if ($isRepartidor): ?>
    <!-- DASHBOARD EXCLUSIVO PARA TÉCNICO -->
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight uppercase leading-none">Mi Panel</h1>
            <p class="text-blue-600 font-bold uppercase text-xs tracking-[0.2em] mt-2">Bienvenido, <?= h($user->username) ?></p>
        </div>

        <div class="flex items-center gap-2 bg-white p-2 rounded-2xl shadow-sm border border-slate-100">
            <?= $this->Form->create(null, ['type' => 'get', 'class' => 'flex items-center gap-2']) ?>
                <?= $this->Form->control('start_date', ['type' => 'date', 'value' => $startDate, 'label' => false, 'class' => 'text-[10px] font-black p-3 bg-slate-50 rounded-xl border-none outline-none']) ?>
                <span class="text-slate-300 font-bold">/</span>
                <?= $this->Form->control('end_date', ['type' => 'date', 'value' => $endDate, 'label' => false, 'class' => 'text-[10px] font-black p-3 bg-slate-50 rounded-xl border-none outline-none']) ?>
                <button type="submit" class="bg-blue-600 text-white p-3 rounded-xl hover:bg-yellow-400 hover:text-slate-900 transition-all shadow-lg">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </button>
                <?= $this->Html->link('<i class="fa-solid fa-rotate-left"></i>', ['action' => 'index'], ['class' => 'bg-slate-100 text-slate-400 p-3 rounded-xl hover:bg-slate-200', 'escape' => false]) ?>
            <?= $this->Form->end() ?>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="dashboard-card card-blue p-8 shadow-xl text-white relative overflow-hidden bg-blue-600">
            <p class="text-blue-100 text-[10px] font-black uppercase mb-2 tracking-widest leading-none">Servicios Período</p>
            <p class="text-5xl font-black"><?= $deliveredInPeriod ?></p>
            <p class="text-[10px] font-bold mt-4 uppercase opacity-80">Finalizados</p>
            <i class="fa-solid fa-store absolute -bottom-4 -right-4 text-7xl opacity-10 rotate-12"></i>
        </div>

        <div class="dashboard-card card-black p-8 shadow-xl text-white relative overflow-hidden bg-slate-900">
            <p class="text-blue-400/60 text-[10px] font-black uppercase mb-2 tracking-widest leading-none">Ganancia Generada</p>
            <p class="text-5xl font-black tracking-tighter text-yellow-400">$<?= number_format($totalEarned, 0) ?></p>
            <p class="text-[11px] font-bold mt-4 uppercase opacity-60 italic">Comisiones/Servicios</p>
            <i class="fa-solid fa-wallet absolute -bottom-4 -right-4 text-7xl opacity-10 rotate-12"></i>
        </div>

        <div class="dashboard-card card-yellow p-8 shadow-sm border border-slate-100 relative overflow-hidden bg-white">
            <p class="text-slate-400 text-[10px] font-black uppercase mb-2 tracking-widest leading-none">Pendientes Hoy</p>
            <p class="text-5xl font-black text-slate-800"><?= $pendingDeliveries ?></p>
            <p class="text-[10px] text-red-500 font-bold mt-4 uppercase italic">Urgentes</p>
            <i class="fa-solid fa-screwdriver-wrench absolute -bottom-4 -right-4 text-7xl text-slate-100 rotate-12"></i>
        </div>
    </div>

<?php elseif ($isAdmin): ?>
    <!-- DASHBOARD COMPLETO (CONTROL TOTAL PARA ADMINISTRADORES) -->
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Resumen de Control Total</h1>
            <p class="text-blue-600 font-bold uppercase text-xs tracking-widest">CERRAJERÍA SARRIA Administration Panel</p>
        </div>
        
        <div class="flex flex-col md:flex-row items-center gap-2">
            <div class="flex items-center gap-2 bg-white p-2 rounded-2xl shadow-sm border">
                <?= $this->Form->create(null, ['type' => 'get', 'class' => 'flex items-center gap-2']) ?>
                    <?= $this->Form->control('start_date', ['type' => 'date', 'value' => $startDate, 'label' => false, 'class' => 'text-xs font-bold p-3 outline-none border-none']) ?>
                    <span class="text-slate-300">/</span>
                    <?= $this->Form->control('end_date', ['type' => 'date', 'value' => $endDate, 'label' => false, 'class' => 'text-xs font-bold p-3 outline-none border-none']) ?>
<button type="submit" class="bg-blue-600 text-white p-3 rounded-xl hover:bg-yellow-400 hover:text-slate-900 transition-colors">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
<?= $this->Html->link('<i class="fa-solid fa-rotate-left"></i>', ['action' => 'index'], ['class' => 'bg-slate-100 text-slate-500 p-3 rounded-xl hover:bg-slate-200', 'escape' => false]) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </header>

    <!-- Fila 1: Métricas de Operación -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="dashboard-card card-blue bg-white p-6 shadow-sm border border-slate-100">
            <div class="flex justify-between items-start mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Ingresos Brutos</span>
                <i class="fa-solid fa-money-bill-trend-up text-blue-600"></i>
            </div>
            <p class="text-3xl font-black text-slate-900">$<?= number_format($totalIncome, 0) ?></p>
            <p class="text-[10px] font-bold text-blue-600 mt-1 uppercase italic">Ventas + Abonos</p>
        </div>

        <div class="dashboard-card card-red bg-white p-6 shadow-sm border border-slate-100">
            <div class="flex justify-between items-start mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Gastos Directos</span>
                <i class="fa-solid fa-arrow-down-short-wide text-red-500"></i>
            </div>
            <p class="text-3xl font-black text-slate-900">$<?= number_format($totalExpenses, 0) ?></p>
            <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase italic">Pagos & Compras</p>
        </div>

        <div class="dashboard-card card-yellow bg-white p-6 shadow-sm border border-slate-100">
            <div class="flex justify-between items-start mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Costo Insumos</span>
                <i class="fa-solid fa-box-open text-yellow-500"></i>
            </div>
            <p class="text-3xl font-black text-slate-900">$<?= number_format($totalCostOfSales, 0) ?></p>
            <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase italic">Materiales usados</p>
        </div>

        <div class="dashboard-card card-black bg-slate-900 p-6 shadow-xl relative overflow-hidden">
            <div class="flex justify-between items-start mb-2">
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Utilidad Neta</span>
                <i class="fa-solid fa-vault text-yellow-400"></i>
            </div>
            <p class="text-3xl font-black text-yellow-400">$<?= number_format($netProfit, 0) ?></p>
            <p class="text-[10px] font-bold text-white mt-1 uppercase italic">Ganancia Real</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <!-- Métodos de Pago (CONTROL DETALLADO) -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <h3 class="font-black text-slate-900 uppercase text-xs tracking-widest mb-6 border-b pb-4">Desglose de Caja</h3>
            <div class="space-y-4">
                <?php foreach ($paymentTotals as $pt): ?>
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl hover:bg-blue-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-blue-600">
                                <i class="fa-solid fa-wallet text-xs"></i>
                            </div>
                            <span class="text-xs font-black text-slate-700 uppercase"><?= h($pt->method) ?></span>
                        </div>
                        <span class="text-xs font-black text-slate-900">$<?= number_format($pt->total, 0) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Gráfico de Ventas -->
        <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="font-black text-slate-900 uppercase text-xs tracking-widest">Rendimiento</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase italic">Ventas vs Tiempo</p>
                </div>
                <div class="flex items-center gap-3 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-black text-blue-600 uppercase"><?= count($salesByDay) ?> Días medidos</span>
                </div>
            </div>
            <div class="h-[250px]">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Ranking Repartidores -->
        <div class="lg:col-span-1 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <h3 class="font-black text-slate-900 uppercase text-xs tracking-widest mb-6">Repartidores & Puntos</h3>
            <div class="space-y-6">
                <?php foreach ($driversRanking as $index => $driver): ?>
                    <div class="flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg <?= $index === 0 ? 'bg-yellow-400 text-slate-900' : 'bg-slate-100 text-slate-400' ?> flex items-center justify-center font-black text-[10px]">
                                <?= $index + 1 ?>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-800 uppercase"><?= h($driver->name) ?></p>
                                <p class="text-[9px] font-bold text-slate-400"><?= $driver->orders_count ?> pedidos</p>
                            </div>
                        </div>
                        <p class="text-[10px] font-black text-slate-900">$<?= number_format($driver->total, 0) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Productos Top -->
        <div class="lg:col-span-2 bg-slate-900 p-8 rounded-[2.5rem] shadow-xl text-white">
            <h3 class="font-black text-yellow-400 uppercase text-xs tracking-widest mb-6">Productos más Vendidos</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($topProducts as $prod): ?>
                    <div class="bg-slate-800/50 p-4 rounded-2xl border border-slate-800">
                        <div class="flex justify-between items-start mb-3">
                            <span class="text-[10px] font-black uppercase text-slate-400"><?= h($prod['name']) ?></span>
                            <span class="bg-yellow-400 text-slate-900 text-[9px] font-black px-2 py-0.5 rounded-full"><?= $prod['sold_count'] ?></span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500" style="width: <?= min(100, $prod['sold_count'] * 10) ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Alertas Stock -->
        <div class="lg:col-span-1 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <h3 class="font-black text-red-500 uppercase text-xs tracking-widest mb-6">Stock Crítico</h3>
            <div class="space-y-4">
                <?php if ($lowStock->isEmpty()): ?>
                    <p class="text-[10px] font-bold text-slate-400 text-center py-4 uppercase">Inventario OK</p>
                <?php else: ?>
                    <?php foreach ($lowStock as $ing): ?>
                        <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                            <span class="text-[10px] font-black text-slate-700 uppercase"><?= h($ing->name) ?></span>
                            <span class="text-[10px] font-black text-red-600"><?= (float)$ing->stock ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($salesByDay, 'date')) ?>,
                datasets: [{
                    label: 'Ventas ($)',
                    data: <?= json_encode(array_column($salesByDay, 'total')) ?>,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 4,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 9, weight: 'bold' } } },
                    x: { grid: { display: false }, ticks: { font: { size: 9, weight: 'bold' } } }
                }
            }
        });
    });
    </script>

<?php elseif ($isStaff): ?>
    <!-- DASHBOARD LIMITADO PARA STAFF -->
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Resumen de Operaciones</h1>
            <p class="text-blue-600 font-bold uppercase text-xs tracking-widest">CERRAJERÍA SARRIA - Acceso Limitado</p>
        </div>
        <div class="flex flex-col md:flex-row items-center gap-2">
             <?= $this->Form->create(null, ['type' => 'get', 'class' => 'flex items-center gap-2']) ?>
                <?= $this->Form->control('start_date', ['type' => 'date', 'value' => $startDate, 'label' => false, 'class' => 'text-xs font-bold p-3 outline-none border-none']) ?>
                <span class="text-slate-300">/</span>
                <?= $this->Form->control('end_date', ['type' => 'date', 'value' => $endDate, 'label' => false, 'class' => 'text-xs font-bold p-3 outline-none border-none']) ?>
                <button type="submit" class="bg-blue-600 text-white p-3 rounded-xl hover:bg-yellow-400 hover:text-slate-900 transition-colors">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <?= $this->Html->link('<i class="fa-solid fa-rotate-left"></i>', ['action' => 'index'], ['class' => 'bg-slate-100 text-slate-500 p-3 rounded-xl hover:bg-slate-200', 'escape' => false]) ?>
            <?= $this->Form->end() ?>
        </div>
    </header>

    <!-- Mostrar solo métricas no financieras para Staff -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="dashboard-card card-blue bg-white p-6 shadow-sm border border-slate-100">
            <div class="flex justify-between items-start mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Ventas Realizadas</span>
                <i class="fa-solid fa-receipt text-blue-600"></i>
            </div>
            <p class="text-3xl font-black text-slate-900"><?= number_format($totalOrders) ?></p>
            <p class="text-[10px] font-bold text-blue-600 mt-1 uppercase italic">Total del período</p>
        </div>
        <div class="dashboard-card card-yellow bg-white p-6 shadow-sm border border-slate-100">
            <div class="flex justify-between items-start mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pendientes Hoy</span>
                <i class="fa-solid fa-list-check text-yellow-500"></i>
            </div>
            <p class="text-3xl font-black text-slate-900"><?= $pendingDeliveries ?></p>
            <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase italic">Pedidos por entregar</p>
        </div>
    </div>
    <!-- No mostrar gráfico de ventas ni desglose de caja ni stock crítico -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <div class="lg:col-span-3 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <h3 class="font-black text-slate-900 uppercase text-xs tracking-widest mb-6 border-b pb-4">Resumen de Operaciones</h3>
            <p class="text-center text-slate-400 italic">No tienes acceso a información financiera detallada.</p>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-1 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <h3 class="font-black text-slate-900 uppercase text-xs tracking-widest mb-6">Repartidores & Puntos</h3>
            <div class="space-y-6">
                <?php foreach ($driversRanking as $index => $driver): ?>
                    <div class="flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg <?= $index === 0 ? 'bg-yellow-400 text-slate-900' : 'bg-slate-100 text-slate-400' ?> flex items-center justify-center font-black text-[10px]">
                                <?= $index + 1 ?>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-800 uppercase"><?= h($driver->name) ?></p>
                                <p class="text-[9px] font-bold text-slate-400"><?= $driver->orders_count ?> pedidos</p>
                            </div>
                        </div>
                        <p class="text-[10px] font-black text-slate-900">$<?= number_format($driver->total, 0) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="lg:col-span-2 bg-slate-900 p-8 rounded-[2.5rem] shadow-xl text-white">
            <h3 class="font-black text-yellow-400 uppercase text-xs tracking-widest mb-6">Productos más Vendidos</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($topProducts as $prod): ?>
                    <div class="bg-slate-800/50 p-4 rounded-2xl border border-slate-800">
                        <div class="flex justify-between items-start mb-3">
                            <span class="text-[10px] font-black uppercase text-slate-400"><?= h($prod['name']) ?></span>
                            <span class="bg-yellow-400 text-slate-900 text-[9px] font-black px-2 py-0.5 rounded-full"><?= $prod['sold_count'] ?></span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500" style="width: <?= min(100, $prod['sold_count'] * 10) ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="lg:col-span-1 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <h3 class="font-black text-red-500 uppercase text-xs tracking-widest mb-6">Stock Crítico</h3>
            <div class="space-y-4">
                <?php if ($lowStock->isEmpty()): ?>
                    <p class="text-[10px] font-bold text-slate-400 text-center py-4 uppercase">Inventario OK</p>
                <?php else: ?>
                    <?php foreach ($lowStock as $ing): ?>
                        <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                            <span class="text-[10px] font-black text-slate-700 uppercase"><?= h($ing->name) ?></span>
                            <span class="text-[10px] font-black text-red-600"><?= (float)$ing->stock ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Script for salesChart -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($salesByDay, 'date')) ?>,
                datasets: [{
                    label: 'Ventas ($)',
                    data: <?= json_encode(array_column($salesByDay, 'total')) ?>,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 4,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 9, weight: 'bold' } } },
                    x: { grid: { display: false }, ticks: { font: { size: 9, weight: 'bold' } } }
                }
            }
        });
    });
    </script>

<?php elseif ($isCliente): ?>
    <!-- DASHBOARD PARA CLIENTES -->
    <div class="flex flex-col items-center justify-center min-h-[60vh] p-8 bg-white rounded-[3rem] shadow-sm border border-slate-100 text-center">
        <div class="w-24 h-24 bg-blue-100 text-blue-600 rounded-3xl flex items-center justify-center mb-6 shadow-lg">
            <i class="fa-solid fa-handshake text-4xl"></i>
        </div>
        <h1 class="text-3xl font-black text-slate-800 uppercase tracking-tight mb-2">¡Bienvenido, <?= h($user->username) ?>!</h1>
        <p class="text-slate-500 font-bold max-w-md mx-auto mb-8">
            Desde aquí puedes consultar el estado actual de tus cuentas y el historial de tus pedidos.
        </p>
        <div class="grid grid-cols-1 md:grid-cols-1 gap-4 w-full max-w-xs">
            <?= $this->Html->link('<i class="fa-solid fa-receipt mr-2"></i> Ver Mis Cuentas', ['controller' => 'AccountsReceivable', 'action' => 'index'], ['escape' => false, 'class' => 'bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase hover:bg-blue-600 transition-all shadow-xl tracking-widest']) ?>
        </div>
    </div>

<?php else: ?>
    <!-- ACCESO DENEGADO / VISTA MÍNIMA SI NO ES ADMIN, STAFF O REPARTIDOR -->
    <div class="flex flex-col items-center justify-center min-h-screen p-6 bg-slate-100">
        <h1 class="text-4xl font-black text-slate-800 mb-4">Acceso Restringido</h1>
        <p class="text-slate-500">No tienes los permisos necesarios para ver esta página.</p>
    </div>
<?php endif; ?>
