<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>SISTEMA GESTIÓN - <?= $this->fetch('title') ?></title>
    <?= $this->Html->meta('icon') ?>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        body { 
            font-family: 'Inter', sans-serif; 
            -webkit-font-smoothing: antialiased;
            color: #1e293b;
        }
        
        /* Layout Structure */
        .app-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        @media (min-width: 1024px) {
            .app-container {
                flex-direction: row;
            }
        }

        /* Sidebar - Ultra Professional Light */
        .sidebar {
            width: 260px;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            flex-shrink: 0;
            display: none;
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

        /* Modern Nav Links */
        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 3px solid transparent;
        }

        .nav-link:hover {
            color: #ef4444;
            background: #fef2f2;
        }

        .nav-link.active {
            color: #ef4444;
            background: #fef2f2;
            font-weight: 700;
            border-right-color: #ef4444;
        }

        .nav-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            min-width: 0;
        }

        /* Mobile Components */
        .mobile-header {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 20px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        /* Drawer for Mobile */
        #mobile-drawer {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 280px;
            background: #ffffff;
            z-index: 100;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            box-shadow: 20px 0 50px rgba(0,0,0,0.1);
        }

        #mobile-drawer.open {
            transform: translateX(0);
        }

        .drawer-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
            z-index: 90;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }

        .drawer-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        /* Professional Cards */
        .pro-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02), 0 1px 2px rgba(0,0,0,0.04);
        }

        .section-label {
            padding: 24px 24px 8px 24px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #94a3b8;
        }
    </style>
</head>
<body class="h-full">

    <?php 
    $identity = $this->request->getAttribute('identity');
    $user = $identity ? $identity->getOriginalData() : null;
    $isAdmin = ($user && (!empty($user->is_superadmin) || $user->username === 'admin' || $user->role === 'admin'));
    $isStaff = ($user && !empty($user->role) && $user->role === 'staff');
    
    if ($user): 
    ?>
    <div class="app-container">
        <!-- Sidebar Desktop -->
        <aside class="sidebar">
            <div class="p-8 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-red-500 text-white p-2 rounded-lg shadow-sm shadow-red-200">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                    </div>
                    <span class="font-extrabold text-xl tracking-tight text-slate-900 uppercase italic">
                        Cerra<span class="text-red-500">jería</span>
                    </span>
                </div>
            </div>

            <nav class="flex-1 flex flex-col">
                <?php
                $navItems = [
                    ['Dashboard', 'index', 'fa-chart-pie', 'Resumen'],
                    ['Orders', 'index', 'fa-shopping-cart', 'Ventas'],
                    ['AccountsReceivable', 'index', 'fa-file-invoice-dollar', 'Cuentas x Cobrar', ($isAdmin || $isStaff)],
                    ['DailyClosures', 'index', 'fa-cash-register', 'Cierres de Caja', ($isAdmin || $isStaff)],
                    ['Products', 'index', 'fa-tag', 'Productos', ($isAdmin || $isStaff)],
                    ['DeliveryDrivers', 'index', 'fa-truck', 'Repartidores', ($isAdmin || $isStaff)],
                    ['Clients', 'index', 'fa-users', 'Clientes', ($isAdmin || $isStaff)],
                    ['Ingredients', 'index', 'fa-box-open', 'Inventario', ($isAdmin || $isStaff)],
                ];

                foreach ($navItems as $item):
                    if (!isset($item[4]) || $item[4]):
                        $active = $this->request->getParam('controller') == $item[0];
                ?>
                    <?= $this->Html->link(
                        '<i class="fa-solid ' . $item[2] . '"></i> ' . $item[3],
                        ['controller' => $item[0], 'action' => $item[1]],
                        ['escape' => false, 'class' => 'nav-link ' . ($active ? 'active' : '')]
                    ) ?>
                <?php 
                    endif;
                endforeach; 
                ?>

                <?php if ($isAdmin): ?>
                    <div class="section-label">Configuración</div>
                    <?= $this->Html->link('<i class="fa-solid fa-user-gear"></i> Usuarios', ['controller' => 'Users', 'action' => 'index'], ['escape' => false, 'class' => 'nav-link ' . ($this->request->getParam('controller') == 'Users' ? 'active' : '')]) ?>
                    <?= $this->Html->link('<i class="fa-solid fa-sliders"></i> Ajustes Stock', ['controller' => 'InventoryAdjustments', 'action' => 'index'], ['escape' => false, 'class' => 'nav-link ' . ($this->request->getParam('controller') == 'InventoryAdjustments' ? 'active' : '')]) ?>
                <?php endif; ?>
            </nav>

            <div class="p-6 border-t border-slate-100">
                <?= $this->Html->link('<i class="fa-solid fa-sign-out-alt"></i> Cerrar Sesión', ['controller' => 'Users', 'action' => 'logout'], ['escape' => false, 'class' => 'flex items-center gap-2 text-sm font-semibold text-slate-400 hover:text-red-500 transition-colors px-4']) ?>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="main-content flex flex-col">
            <!-- Mobile Top Header -->
            <header class="lg:hidden mobile-header shadow-sm flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="bg-red-500 text-white p-1.5 rounded-md text-xs">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <span class="font-extrabold text-sm text-slate-900 uppercase italic">Cerra<span class="text-red-500">jería</span></span>
                </div>
                <button id="drawer-toggle" class="p-2 text-slate-600 hover:bg-slate-50 rounded-lg">
                    <i class="fa-solid fa-bars-staggered text-xl"></i>
                </button>
            </header>

            <!-- Page Content -->
            <div class="p-4 md:p-10 max-w-full">
                <div class="mb-6 lg:hidden">
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest"><?= h($this->fetch('title')) ?></h2>
                </div>
                <?= $this->Flash->render() ?>
                <?= $this->fetch('content') ?>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer Overlay -->
    <div id="drawer-overlay" class="drawer-overlay"></div>

    <!-- Mobile Drawer Menu -->
    <div id="mobile-drawer" class="flex flex-col">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <span class="font-bold text-slate-900 uppercase text-xs">Menú Principal</span>
            <button id="drawer-close" class="text-slate-400"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>
        <nav class="flex-1 py-4 flex flex-col">
            <!-- Re-rendering items for drawer -->
            <?php foreach ($navItems as $item): if (!isset($item[4]) || $item[4]): ?>
                <?= $this->Html->link(
                    '<i class="fa-solid ' . $item[2] . '"></i> ' . $item[3],
                    ['controller' => $item[0], 'action' => $item[1]],
                    ['escape' => false, 'class' => 'nav-link ' . ($this->request->getParam('controller') == $item[0] ? 'active' : '')]
                ) ?>
            <?php endif; endforeach; ?>
            
            <?php if ($isAdmin): ?>
                <div class="section-label">Configuración</div>
                <?= $this->Html->link('<i class="fa-solid fa-user-gear"></i> Usuarios', ['controller' => 'Users', 'action' => 'index'], ['escape' => false, 'class' => 'nav-link']) ?>
                <?= $this->Html->link('<i class="fa-solid fa-sliders"></i> Ajustes', ['controller' => 'InventoryAdjustments', 'action' => 'index'], ['escape' => false, 'class' => 'nav-link']) ?>
            <?php endif; ?>

            <div class="mt-auto p-6 border-t border-slate-100">
                <?= $this->Html->link('<i class="fa-solid fa-sign-out-alt"></i> Salir', ['controller' => 'Users', 'action' => 'logout'], ['escape' => false, 'class' => 'text-red-500 font-bold text-sm']) ?>
            </div>
        </nav>
    </div>

    <?php else: ?>
        <!-- No User Layout (Login) -->
        <main class="min-h-screen flex items-center justify-center p-6 bg-slate-50">
            <div class="w-full max-w-sm">
                <div class="text-center mb-8">
                    <div class="bg-red-500 text-white w-12 h-12 flex items-center justify-center rounded-xl mx-auto shadow-lg mb-4">
                        <i class="fa-solid fa-key text-xl"></i>
                    </div>
                    <h1 class="text-2xl font-extrabold text-slate-900 uppercase italic">Cerra<span class="text-red-500">jería</span></h1>
                    <p class="text-slate-400 text-xs font-medium mt-1">SISTEMA PROFESIONAL DE GESTIÓN</p>
                </div>
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
