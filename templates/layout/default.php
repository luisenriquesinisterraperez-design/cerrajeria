<!DOCTYPE html>
<html lang="es">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>CERRAJERÍA PRO - <?= $this->fetch('title') ?></title>
    <?= $this->Html->meta('icon') ?>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Archivo+Black&family=Barlow+Condensed:wght@400;700;900&family=Inter:wght@400;700&display=swap');
        
        :root {
            --industrial-red: #D71920;
            --industrial-yellow: #FFD200;
            --carbon-black: #111111;
            --steel-gray: #222222;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #0f0f0f;
            color: #ffffff;
        }
        
        .industrial-font { font-family: 'Barlow Condensed', sans-serif; }
        .logo-font { font-family: 'Archivo Black', sans-serif; }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--carbon-black); }
        ::-webkit-scrollbar-thumb { background: var(--industrial-red); border-radius: 0; }

        /* Estilo de Tarjetas Industrial */
        .industrial-card {
            background: var(--carbon-black);
            border-left: 4px solid var(--industrial-red);
            border-right: 1px solid rgba(255,255,255,0.05);
            border-top: 1px solid rgba(255,255,255,0.05);
            border-bottom: 1px solid rgba(255,255,255,0.05);
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .industrial-btn {
            background: var(--industrial-red);
            color: white;
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            clip-path: polygon(5% 0%, 100% 0%, 95% 100%, 0% 100%);
            transition: all 0.2s;
            padding: 10px 25px;
        }

        .industrial-btn:hover {
            background: var(--industrial-yellow);
            color: black;
            transform: scale(1.05);
        }

        .nav-item-active {
            background: linear-gradient(90deg, var(--industrial-red) 0%, transparent 100%);
            border-left: 4px solid var(--industrial-yellow);
            color: white !important;
        }

        @media (max-width: 768px) {
            .mobile-nav-bottom {
                background: var(--carbon-black);
                border-top: 2px solid var(--industrial-red);
            }
        }
    </style>
</head>
<body class="min-h-screen">

    <?php 
    $identity = $this->request->getAttribute('identity');
    $user = $identity ? $identity->getOriginalData() : null;
    $isAdmin = ($user && (!empty($user->is_superadmin) || $user->username === 'admin' || $user->role === 'admin'));
    $isStaff = ($user && !empty($user->role) && $user->role === 'staff');
    
    if ($user): 
    ?>
    <!-- Sidebar para Desktop -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-[#050505] flex flex-col z-[100] -translate-x-full md:translate-x-0 transition-transform duration-300 md:sticky md:top-0 md:h-screen border-r border-white/5">
        <div class="p-6 border-b border-white/5 bg-black">
            <div class="flex items-center gap-3">
                <div class="bg-red-600 p-2 transform -skew-x-12">
                    <i class="fa-solid fa-screwdriver-wrench text-white text-xl"></i>
                </div>
                <div class="logo-font leading-none uppercase tracking-tighter">
                    <div class="text-xl text-white">CERRA<span class="text-red-600">JERÍA</span></div>
                    <div class="text-[10px] text-yellow-400">Professional Service</div>
                </div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto p-4 flex flex-col gap-1 industrial-font text-lg tracking-wide">
            <?php
            $navItems = [
                ['Dashboard', 'index', 'fa-gauge-high', 'PANEL CONTROL', true],
                ['Orders', 'index', 'fa-cart-flatbed', 'VENTAS / PEDIDOS', true],
                ['AccountsReceivable', 'index', 'fa-file-invoice-dollar', 'CUENTAS X COBRAR', ($isAdmin || $isStaff)],
                ['DailyClosures', 'index', 'fa-cash-register', 'CIERRES DIARIOS', ($isAdmin || $isStaff)],
                ['Products', 'index', 'fa-boxes-stacked', 'CATÁLOGO PRODUCTOS', ($isAdmin || $isStaff)],
                ['DeliveryDrivers', 'index', 'fa-truck-monster', 'REPARTIDORES', ($isAdmin || $isStaff)],
                ['Clients', 'index', 'fa-users-gear', 'BASE CLIENTES', ($isAdmin || $isStaff)],
                ['Ingredients', 'index', 'fa-warehouse', 'INVENTARIO / STOCK', ($isAdmin || $isStaff)],
            ];

            foreach ($navItems as $item):
                if ($item[4]):
                    $active = $this->request->getParam('controller') == $item[0];
            ?>
                <?= $this->Html->link(
                    '<i class="fa-solid ' . $item[2] . ' w-6 text-red-600"></i> ' . $item[3],
                    ['controller' => $item[0], 'action' => $item[1]],
                    ['escape' => false, 'class' => 'flex items-center gap-3 px-4 py-3 border-b border-white/5 transition-all hover:bg-white/5 ' . ($active ? 'nav-item-active' : 'text-slate-400')]
                ) ?>
            <?php 
                endif;
            endforeach; 
            ?>

            <?php if ($isAdmin): ?>
                <div class="px-4 py-4 mt-4 text-[11px] font-black uppercase text-yellow-500 tracking-[0.3em] opacity-50 industrial-font">SYSTEM ADMIN</div>
                <?= $this->Html->link('<i class="fa-solid fa-user-shield w-6"></i> USUARIOS', ['controller' => 'Users', 'action' => 'index'], ['escape' => false, 'class' => 'flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white border-b border-white/5']) ?>
                <?= $this->Html->link('<i class="fa-solid fa-gears w-6"></i> AJUSTES STOCK', ['controller' => 'InventoryAdjustments', 'action' => 'index'], ['escape' => false, 'class' => 'flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white border-b border-white/5']) ?>
            <?php endif; ?>
        </nav>

        <div class="p-4 bg-black border-t border-white/5">
            <?= $this->Html->link('<i class="fa-solid fa-power-off mr-2"></i> CERRAR SESIÓN', ['controller' => 'Users', 'action' => 'logout'], ['escape' => false, 'class' => 'w-full block text-center py-3 bg-zinc-900 text-red-500 font-black industrial-font tracking-widest hover:bg-red-600 hover:text-white transition-all']) ?>
        </div>
    </aside>

    <!-- Mobile Header -->
    <header class="md:hidden bg-black border-b-2 border-red-600 p-4 sticky top-0 z-[110] flex justify-between items-center">
        <div class="flex items-center gap-2">
            <div class="bg-red-600 p-1 transform -skew-x-12">
                <i class="fa-solid fa-screwdriver-wrench text-white text-sm"></i>
            </div>
            <span class="logo-font text-white text-sm uppercase">CERRA<span class="text-red-600">JERÍA</span></span>
        </div>
        <button id="mobile-menu-toggle" class="text-yellow-400 text-2xl">
            <i class="fa-solid fa-bars-staggered"></i>
        </button>
    </header>
    <?php endif; ?>

    <!-- Main Content Area -->
    <main class="flex-1 md:ml-0">
        <div class="p-4 md:p-8">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>

    <!-- Overlay para móvil -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[90] hidden"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('mobile-menu-toggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');

            if (toggle) {
                toggle.addEventListener('click', () => {
                    sidebar.classList.toggle('-translate-x-full');
                    overlay.classList.toggle('hidden');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', () => {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                });
            }
        });
    </script>
</body>
</html>
