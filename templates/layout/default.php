<!DOCTYPE html>
<html lang="es">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>CERRAJERÍA - <?= $this->fetch('title') ?></title>
    <?= $this->Html->meta('icon') ?>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap');
        
        body { font-family: 'Inter', sans-serif; }
        
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: #f8fafc; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #ef4444; }

        #sidebar ::-webkit-scrollbar { width: 0px; }

        .formal-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .btn-primary {
            background-color: #ef4444;
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.2s;
        }
        
        .btn-primary:hover {
            background-color: #000000;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .mobile-menu-active {
                transform: translateX(0) !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen font-sans text-slate-900">

    <?php 
    $identity = $this->request->getAttribute('identity');
    $user = $identity ? $identity->getOriginalData() : null;
    $isAdmin = ($user && (!empty($user->is_superadmin) || $user->username === 'admin' || $user->role === 'admin'));
    $isStaff = ($user && !empty($user->role) && $user->role === 'staff');
    
    if ($user): 
    ?>
    <!-- Mobile Header -->
    <div class="md:hidden bg-black text-white p-4 flex justify-between items-center sticky top-0 z-[100] border-b border-red-600/30">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-key text-yellow-400"></i>
            <span class="font-black tracking-tighter uppercase text-sm">CERRA<span class="text-red-600">JERÍA</span></span>
        </div>
        <button id="mobile-menu-btn" class="text-white p-2 focus:outline-none">
            <i class="fa-solid fa-bars-staggered text-xl"></i>
        </button>
    </div>

    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-[110] hidden backdrop-blur-sm transition-opacity"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-72 bg-black text-white flex flex-col z-[120] -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out md:sticky md:top-0 md:h-screen shadow-2xl overflow-y-auto">
        <div class="p-8 hidden md:flex items-center gap-3 border-b border-white/5">
            <div class="bg-red-600 p-2 rounded-lg shadow-lg shadow-red-600/20">
                <i class="fa-solid fa-key text-yellow-400 text-lg"></i>
            </div>
            <span class="font-black text-lg tracking-tighter uppercase">CERRA<span class="text-red-600">JERÍA</span></span>
        </div>

        <div class="p-4 md:hidden border-b border-white/5 flex justify-between items-center">
             <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Menú Principal</span>
             <button id="close-sidebar" class="text-slate-400"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <nav class="flex-1 p-4 flex flex-col gap-1 mt-2">
            <?php
            $navItems = [
                ['Dashboard', 'index', 'fa-chart-line', 'Resumen', true],
                ['AccountsReceivable', 'index', 'fa-hand-holding-dollar', 'Cuentas x Cobrar', ($isAdmin || $isStaff)],
                ['DailyClosures', 'index', 'fa-vault', 'Cierre de Caja', ($isAdmin || $isStaff)],
                ['Products', 'index', 'fa-box', 'Productos', ($isAdmin || $isStaff)],
                ['Orders', 'index', 'fa-cart-shopping', 'Ventas', true],
                ['DeliveryDrivers', 'index', 'fa-truck-fast', 'Repartidores', ($isAdmin || $isStaff)],
                ['Clients', 'index', 'fa-address-book', 'Clientes', ($isAdmin || $isStaff)],
                ['Ingredients', 'index', 'fa-boxes-stacked', 'Inventario', ($isAdmin || $isStaff)],
            ];

            foreach ($navItems as $item):
                if ($item[4]):
                    $active = $this->request->getParam('controller') == $item[0];
            ?>
                <?= $this->Html->link(
                    '<i class="fa-solid ' . $item[2] . ' w-6"></i> ' . $item[3],
                    ['controller' => $item[0], 'action' => $item[1]],
                    ['escape' => false, 'class' => 'flex items-center gap-3 px-5 py-3.5 rounded-xl transition-all ' . ($active ? 'bg-red-600 text-white font-bold shadow-lg shadow-red-600/20' : 'text-slate-400 hover:text-white hover:bg-white/5')]
                ) ?>
            <?php 
                endif;
            endforeach; 
            ?>

            <?php if ($isAdmin): ?>
                <div class="px-5 py-3 mt-6 text-[10px] font-black uppercase text-yellow-500 tracking-[0.2em] border-t border-white/5 pt-6 opacity-60">Administración</div>
                
                <?= $this->Html->link('<i class="fa-solid fa-users-gear w-6"></i> Usuarios', ['controller' => 'Users', 'action' => 'index'], ['escape' => false, 'class' => 'flex items-center gap-3 px-5 py-3.5 rounded-xl transition-all ' . ($this->request->getParam('controller') == 'Users' ? 'bg-red-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5')]) ?>
                <?= $this->Html->link('<i class="fa-solid fa-sliders w-6"></i> Ajustes / Bajas', ['controller' => 'InventoryAdjustments', 'action' => 'index'], ['escape' => false, 'class' => 'flex items-center gap-3 px-5 py-3.5 rounded-xl transition-all ' . ($this->request->getParam('controller') == 'InventoryAdjustments' ? 'bg-red-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5')]) ?>
                <?= $this->Html->link('<i class="fa-solid fa-file-invoice-dollar w-6"></i> Gastos', ['controller' => 'Expenses', 'action' => 'index'], ['escape' => false, 'class' => 'flex items-center gap-3 px-5 py-3.5 rounded-xl transition-all ' . ($this->request->getParam('controller') == 'Expenses' ? 'bg-red-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5')]) ?>
            <?php endif; ?>
        </nav>

        <div class="p-6 border-t border-white/5">
            <?= $this->Html->link('<i class="fa-solid fa-right-from-bracket w-6"></i> Salir', ['controller' => 'Users', 'action' => 'logout'], ['escape' => false, 'class' => 'w-full flex items-center gap-3 px-5 py-3 rounded-xl text-red-500 hover:bg-red-600/10 font-bold transition-all text-sm']) ?>
        </div>
    </aside>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="flex-1 w-full min-w-0">
        <div class="p-4 md:p-10 max-w-[1600px] mx-auto">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const closeBtn = document.getElementById('close-sidebar');

            function toggleMenu() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
                document.body.classList.toggle('overflow-hidden');
            }

            if(btn) btn.addEventListener('click', toggleMenu);
            if(overlay) overlay.addEventListener('click', toggleMenu);
            if(closeBtn) closeBtn.addEventListener('click', toggleMenu);

            // Close menu on nav click (mobile)
            const navLinks = sidebar.querySelectorAll('a');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 768) toggleMenu();
                });
            });
        });
    </script>
</body>
</html>
