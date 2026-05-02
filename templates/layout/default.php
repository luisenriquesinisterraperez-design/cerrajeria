<!DOCTYPE html>
<html lang="es">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STOCKMASTER - <?= $this->fetch('title') ?></title>
    <?= $this->Html->meta('icon') ?>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>

    <style>
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #ef4444; }

        #sidebar ::-webkit-scrollbar-track { background: #000000; }
        #sidebar ::-webkit-scrollbar-thumb { background: #1a1a1a; }
        #sidebar ::-webkit-scrollbar-thumb:hover { background: #ef4444; }

        .img-card-top { width: 100%; height: 160px; object-fit: cover; border-radius: 1.5rem 1.5rem 0 0; }
        .img-placeholder { width: 100%; height: 160px; display: flex; align-items: center; justify-content: center; background: #f1f5f9; border-radius: 1.5rem 1.5rem 0 0; color: #94a3b8; }
        
        .switch { position: relative; display: inline-block; width: 34px; height: 20px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .4s; border-radius: 20px; }
        .slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: #ef4444; }
        input:checked + .slider:before { transform: translateX(14px); }

        .side-nav-item { display: block; padding: 0.5rem; color: #64748b; font-weight: bold; }
        .side-nav-item:hover { color: #ef4444; }
        .column-80 { width: 100%; }
    </style>
</head>
<body class="bg-white min-h-screen flex flex-col md:flex-row font-sans text-black">

    <?php 
    $identity = $this->request->getAttribute('identity');
    $user = $identity ? $identity->getOriginalData() : null;
    $isSuperAdmin = ($user && (!empty($user->is_superadmin) || $user->username === 'admin' || $user->role === 'admin'));
    $isAdmin = $isSuperAdmin;
    $isStaff = ($user && !empty($user->role) && $user->role === 'staff');
    
    if ($user): 
    ?>
    <!-- Sidebar -->
    <aside id="sidebar" class="w-full md:w-72 bg-black text-white flex flex-col shadow-2xl md:h-screen md:sticky md:top-0 z-50">
        <div class="p-8 flex items-center gap-3 border-b border-white/10">
            <div class="bg-red-600 p-2 rounded-xl shadow-lg shadow-red-600/20">
                <i class="fa-solid fa-key text-yellow-400 text-xl"></i>
            </div>
            <span class="font-black text-xl tracking-tighter uppercase">CERRA<span class="text-red-600">JERÍA</span></span>
        </div>

        <nav class="flex flex-row md:flex-col overflow-x-auto md:overflow-y-auto p-4 md:p-6 gap-2">
            <?= $this->Html->link('<i class="fa-solid fa-chart-line"></i> Resumen', ['controller' => 'Dashboard', 'action' => 'index'], ['escape' => false, 'class' => 'flex items-center gap-4 p-4 rounded-2xl transition-all ' . ($this->request->getParam('controller') == 'Dashboard' ? 'bg-red-600 text-white font-bold shadow-lg shadow-red-600/20' : 'text-slate-400 hover:bg-white/5')]) ?>
            
            <?php if ($isAdmin || $isStaff): ?>
                <?= $this->Html->link('<i class="fa-solid fa-hand-holding-dollar"></i> Cuentas x Cobrar', ['controller' => 'AccountsReceivable', 'action' => 'index'], ['escape' => false, 'class' => 'flex items-center gap-4 p-4 rounded-2xl transition-all ' . ($this->request->getParam('controller') == 'AccountsReceivable' ? 'bg-red-600 text-white font-bold shadow-lg shadow-red-600/20' : 'text-slate-400 hover:bg-white/5')]) ?>
                <?= $this->Html->link('<i class="fa-solid fa-vault"></i> Cierre de Caja', ['controller' => 'DailyClosures', 'action' => 'index'], ['escape' => false, 'class' => 'flex items-center gap-4 p-4 rounded-2xl transition-all ' . ($this->request->getParam('controller') == 'DailyClosures' ? 'bg-red-600 text-white font-bold shadow-lg shadow-red-600/20' : 'text-slate-400 hover:bg-white/5')]) ?>
                <?= $this->Html->link('<i class="fa-solid fa-box"></i> Productos', ['controller' => 'Products', 'action' => 'index'], ['escape' => false, 'class' => 'flex items-center gap-4 p-4 rounded-2xl transition-all ' . ($this->request->getParam('controller') == 'Products' ? 'bg-red-600 text-white font-bold shadow-lg shadow-red-600/20' : 'text-slate-400 hover:bg-white/5')]) ?>
            <?php endif; ?>

            <?= $this->Html->link('<i class="fa-solid fa-cart-shopping"></i> Ventas', ['controller' => 'Orders', 'action' => 'index'], ['escape' => false, 'class' => 'flex items-center gap-4 p-4 rounded-2xl transition-all ' . ($this->request->getParam('controller') == 'Orders' ? 'bg-red-600 text-white font-bold shadow-lg shadow-red-600/20' : 'text-slate-400 hover:bg-white/5')]) ?>

            <?php if ($isAdmin || $isStaff): ?>
                <?= $this->Html->link('<i class="fa-solid fa-truck-fast"></i> Repartidores', ['controller' => 'DeliveryDrivers', 'action' => 'index'], ['escape' => false, 'class' => 'flex items-center gap-4 p-4 rounded-2xl transition-all ' . ($this->request->getParam('controller') == 'DeliveryDrivers' ? 'bg-red-600 text-white font-bold shadow-lg shadow-red-600/20' : 'text-slate-400 hover:bg-white/5')]) ?>
                <?= $this->Html->link('<i class="fa-solid fa-address-book"></i> Clientes', ['controller' => 'Clients', 'action' => 'index'], ['escape' => false, 'class' => 'flex items-center gap-4 p-4 rounded-2xl transition-all ' . ($this->request->getParam('controller') == 'Clients' ? 'bg-red-600 text-white font-bold shadow-lg shadow-red-600/20' : 'text-slate-400 hover:bg-white/5')]) ?>
                <?= $this->Html->link('<i class="fa-solid fa-boxes-stacked"></i> Inventario', ['controller' => 'Ingredients', 'action' => 'index'], ['escape' => false, 'class' => 'flex items-center gap-4 p-4 rounded-2xl transition-all ' . ($this->request->getParam('controller') == 'Ingredients' ? 'bg-red-600 text-white font-bold shadow-lg shadow-red-600/20' : 'text-slate-400 hover:bg-white/5')]) ?>
            <?php endif; ?>

            <?php if ($isAdmin): ?>
                <div class="px-4 py-2 mt-4 text-[9px] font-black uppercase text-yellow-500 tracking-[0.2em] border-t border-white/10 pt-6">Administración</div>
                
                <?= $this->Html->link('<i class="fa-solid fa-users-gear"></i> Usuarios', ['controller' => 'Users', 'action' => 'index'], ['escape' => false, 'class' => 'flex items-center gap-4 p-4 rounded-2xl transition-all ' . ($this->request->getParam('controller') == 'Users' ? 'bg-red-600 text-white font-bold shadow-lg shadow-red-600/20' : 'text-slate-400 hover:bg-white/5')]) ?>

                <?= $this->Html->link('<i class="fa-solid fa-sliders"></i> Ajustes / Bajas', ['controller' => 'InventoryAdjustments', 'action' => 'index'], ['escape' => false, 'class' => 'flex items-center gap-4 p-4 rounded-2xl transition-all ' . ($this->request->getParam('controller') == 'InventoryAdjustments' ? 'bg-red-600 text-white font-bold shadow-lg shadow-red-600/20' : 'text-slate-400 hover:bg-white/5')]) ?>
                <?= $this->Html->link('<i class="fa-solid fa-file-invoice-dollar"></i> Gastos', ['controller' => 'Expenses', 'action' => 'index'], ['escape' => false, 'class' => 'flex items-center gap-4 p-4 rounded-2xl transition-all ' . ($this->request->getParam('controller') == 'Expenses' ? 'bg-red-600 text-white font-bold shadow-lg shadow-red-600/20' : 'text-slate-400 hover:bg-white/5')]) ?>
            <?php endif; ?>
        </nav>

        <div class="p-6 mt-auto hidden md:block border-t border-white/10">
            <?= $this->Html->link('<i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión', ['controller' => 'Users', 'action' => 'logout'], ['escape' => false, 'class' => 'w-full flex items-center gap-4 p-4 rounded-2xl text-red-500 hover:bg-red-600/10 font-bold transition-colors']) ?>
        </div>
    </aside>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="flex-1 p-4 md:p-10 overflow-y-auto">
        <div class="container mx-auto">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>

</body>
</html>
