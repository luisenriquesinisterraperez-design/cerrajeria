<!DOCTYPE html>
<html lang="es" class="h-full bg-[#0a0a0a]">
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
        @import url('https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700;900&family=Inter:wght@400;600;800&display=swap');
        
        :root {
            --industrial-red: #cc0000;
            --industrial-yellow: #ffcc00;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            -webkit-font-smoothing: antialiased;
        }
        
        .industrial-font { font-family: 'Barlow Condensed', sans-serif; }

        /* Estructura para evitar deformaciones */
        .app-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        @media (min-width: 1024px) {
            .app-wrapper {
                flex-direction: row;
            }
        }

        /* Sidebar Desktop Fijo */
        .sidebar {
            width: 280px;
            background: #000;
            flex-shrink: 0;
            display: none;
            border-right: 1px solid #1a1a1a;
        }

        @media (min-width: 1024px) {
            .sidebar {
                display: flex;
                flex-direction: column;
                position: sticky;
                top: 0;
                height: 100vh;
            }
        }

        /* Nav Items */
        .nav-link {
            display: flex;
            items-center: center;
            padding: 0.85rem 1.25rem;
            color: #999;
            font-weight: 600;
            border-left: 4px solid transparent;
            transition: all 0.2s;
        }

        .nav-link:hover {
            color: #fff;
            background: #111;
        }

        .nav-link.active {
            color: #fff;
            background: linear-gradient(90deg, #cc000033 0%, transparent 100%);
            border-left-color: var(--industrial-red);
        }

        /* Contenido */
        .main-content {
            flex: 1;
            min-width: 0;
            background: #0a0a0a;
        }

        /* Mobile Nav Drawer */
        #mobile-drawer {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 280px;
            background: #000;
            z-index: 200;
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #mobile-drawer.open {
            transform: translateX(0);
        }

        .drawer-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            backdrop-blur: 4px;
            z-index: 190;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }

        .drawer-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }
    </style>
</head>
<body class="h-full text-gray-200 overflow-x-hidden">

    <?php 
    $identity = $this->request->getAttribute('identity');
    $user = $identity ? $identity->getOriginalData() : null;
    $isAdmin = ($user && (!empty($user->is_superadmin) || $user->username === 'admin' || $user->role === 'admin'));
    $isStaff = ($user && !empty($user->role) && $user->role === 'staff');
    
    if ($user): 
    ?>
    <div class="app-wrapper">
        <!-- Sidebar Desktop -->
        <aside class="sidebar overflow-y-auto">
            <div class="p-6 bg-black border-b border-white/5">
                <div class="flex items-center gap-3 transform -skew-x-12">
                    <div class="bg-red-600 p-2">
                        <i class="fa-solid fa-screwdriver-wrench text-white text-xl"></i>
                    </div>
                    <span class="industrial-font text-2xl font-black italic tracking-tighter text-white">
                        CERRA<span class="text-red-600">JERÍA</span>
                    </span>
                </div>
            </div>

            <nav class="flex-1 py-4 flex flex-col industrial-font text-lg">
                <?php
                $navItems = [
                    ['Dashboard', 'index', 'fa-gauge-high', 'RESUMEN GENERAL', true],
                    ['Orders', 'index', 'fa-cart-shopping', 'VENTAS / PEDIDOS', true],
                    ['AccountsReceivable', 'index', 'fa-file-invoice-dollar', 'CUENTAS POR COBRAR', ($isAdmin || $isStaff)],
                    ['DailyClosures', 'index', 'fa-cash-register', 'CIERRE DE CAJA', ($isAdmin || $isStaff)],
                    ['Products', 'index', 'fa-box', 'PRODUCTOS', ($isAdmin || $isStaff)],
                    ['DeliveryDrivers', 'index', 'fa-truck-pickup', 'REPARTIDORES', ($isAdmin || $isStaff)],
                    ['Clients', 'index', 'fa-users', 'CLIENTES', ($isAdmin || $isStaff)],
                    ['Ingredients', 'index', 'fa-warehouse', 'INVENTARIO', ($isAdmin || $isStaff)],
                ];

                foreach ($navItems as $item):
                    if ($item[4]):
                        $active = $this->request->getParam('controller') == $item[0];
                ?>
                    <?= $this->Html->link(
                        '<i class="fa-solid ' . $item[2] . ' w-6 text-red-600"></i> ' . $item[3],
                        ['controller' => $item[0], 'action' => $item[1]],
                        ['escape' => false, 'class' => 'nav-link ' . ($active ? 'active' : '')]
                    ) ?>
                <?php 
                    endif;
                endforeach; 
                ?>

                <?php if ($isAdmin): ?>
                    <div class="px-6 py-4 mt-4 text-[11px] font-black text-yellow-500 tracking-[0.3em] opacity-40 uppercase">Ajustes de Sistema</div>
                    <?= $this->Html->link('<i class="fa-solid fa-user-shield w-6"></i> USUARIOS', ['controller' => 'Users', 'action' => 'index'], ['escape' => false, 'class' => 'nav-link ' . ($this->request->getParam('controller') == 'Users' ? 'active' : '')]) ?>
                    <?= $this->Html->link('<i class="fa-solid fa-gears w-6"></i> AJUSTES STOCK', ['controller' => 'InventoryAdjustments', 'action' => 'index'], ['escape' => false, 'class' => 'nav-link ' . ($this->request->getParam('controller') == 'InventoryAdjustments' ? 'active' : '')]) ?>
                <?php endif; ?>
            </nav>

            <div class="p-6 border-t border-white/5">
                <?= $this->Html->link('<i class="fa-solid fa-power-off text-red-500 mr-2"></i> SALIR', ['controller' => 'Users', 'action' => 'logout'], ['escape' => false, 'class' => 'industrial-font font-black tracking-widest text-sm hover:text-red-500 transition-colors']) ?>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="main-content flex flex-col">
            <!-- Mobile Top Header -->
            <header class="lg:hidden bg-black border-b border-red-600/50 p-4 sticky top-0 z-[150] flex justify-between items-center shadow-lg">
                <div class="flex items-center gap-2 transform -skew-x-12">
                    <div class="bg-red-600 px-2 py-1">
                        <i class="fa-solid fa-key text-white text-xs"></i>
                    </div>
                    <span class="industrial-font font-black text-lg text-white">CERRA<span class="text-red-600">JERÍA</span></span>
                </div>
                <button id="drawer-toggle" class="w-10 h-10 flex items-center justify-center bg-zinc-900 rounded-lg text-yellow-500">
                    <i class="fa-solid fa-bars-staggered text-xl"></i>
                </button>
            </header>

            <!-- Page Content -->
            <div class="p-4 md:p-10 max-w-full">
                <?= $this->Flash->render() ?>
                <?= $this->fetch('content') ?>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer Overlay -->
    <div id="drawer-overlay" class="drawer-overlay"></div>

    <!-- Mobile Drawer Menu -->
    <div id="mobile-drawer" class="overflow-y-auto">
        <div class="p-6 bg-black border-b border-white/5 flex justify-between items-center">
            <span class="industrial-font font-black text-white">MENÚ</span>
            <button id="drawer-close" class="text-gray-500"><i class="fa-solid fa-xmark text-2xl"></i></button>
        </div>
        <nav class="p-4 flex flex-col industrial-font text-xl">
            <!-- Reusing Nav Items logic for mobile -->
            <?php foreach ($navItems as $item): if ($item[4]): ?>
                <?= $this->Html->link(
                    '<i class="fa-solid ' . $item[2] . ' w-6 text-red-600"></i> ' . $item[3],
                    ['controller' => $item[0], 'action' => $item[1]],
                    ['escape' => false, 'class' => 'nav-link ' . ($this->request->getParam('controller') == $item[0] ? 'active' : '')]
                ) ?>
            <?php endif; endforeach; ?>
            
            <?php if ($isAdmin): ?>
                <div class="px-5 py-4 mt-4 text-[10px] font-black text-yellow-500 tracking-[0.3em] uppercase opacity-40">Administración</div>
                <?= $this->Html->link('<i class="fa-solid fa-user-shield w-6 text-red-600"></i> USUARIOS', ['controller' => 'Users', 'action' => 'index'], ['escape' => false, 'class' => 'nav-link']) ?>
                <?= $this->Html->link('<i class="fa-solid fa-gears w-6 text-red-600"></i> AJUSTES', ['controller' => 'InventoryAdjustments', 'action' => 'index'], ['escape' => false, 'class' => 'nav-link']) ?>
            <?php endif; ?>

            <div class="mt-8 border-t border-white/5 pt-4">
                <?= $this->Html->link('<i class="fa-solid fa-power-off text-red-500 mr-2"></i> CERRAR SESIÓN', ['controller' => 'Users', 'action' => 'logout'], ['escape' => false, 'class' => 'nav-link text-red-500']) ?>
            </div>
        </nav>
    </div>

    <?php else: ?>
        <!-- No User Layout (Login) -->
        <main class="min-h-screen flex items-center justify-center p-4">
            <div class="w-full max-w-md">
                <?= $this->Flash->render() ?>
                <?= $this->fetch('content') ?>
            </div>
        </main>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('drawer-toggle');
            const close = document.getElementById('drawer-close');
            const drawer = document.getElementById('mobile-drawer');
            const overlay = document.getElementById('drawer-overlay');

            function openDrawer() {
                drawer.classList.add('open');
                overlay.classList.add('open');
                document.body.style.overflow = 'hidden';
            }

            function closeDrawer() {
                drawer.classList.remove('open');
                overlay.classList.remove('open');
                document.body.style.overflow = '';
            }

            if(toggle) toggle.addEventListener('click', openDrawer);
            if(close) close.addEventListener('click', closeDrawer);
            if(overlay) overlay.addEventListener('click', closeDrawer);
        });
    </script>
</body>
</html>
