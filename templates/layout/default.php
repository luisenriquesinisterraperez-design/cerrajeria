<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>CERRAJERÍA MASTER - <?= $this->fetch('title') ?></title>
    <?= $this->Html->meta('icon') ?>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            -webkit-font-smoothing: antialiased;
            color: #0f172a;
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

        /* Sidebar - Modern Enterprise */
        .sidebar {
            width: 280px;
            background: #f8fafc;
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
                overflow-y: auto; /* Fix: Sidebar scrollable if many items */
            }
        }

        /* Nav Links Refined */
        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            margin: 2px 16px;
            color: #475569;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-link:hover {
            color: #ef4444;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .nav-link.active {
            color: #ffffff;
            background: #ef4444;
            box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.25);
            font-weight: 600;
        }

        .nav-link i {
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
            opacity: 0.8;
        }

        .nav-link.active i {
            opacity: 1;
        }

        /* Main Content Styling */
        .main-content {
            flex: 1;
            min-width: 0;
            background: #ffffff;
        }

        /* Glass Header for Content */
        .content-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #f1f5f9;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        /* Mobile Header */
        .mobile-header {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 20px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        /* Professional Details */
        .section-tag {
            padding: 24px 24px 8px 32px;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #94a3b8;
        }

        .user-profile {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin: 16px;
            padding: 12px;
        }
    </style>
</head>
<body class="h-full overflow-x-hidden">

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
            <div class="p-8 mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-gradient-to-br from-red-500 to-red-600 text-white p-2.5 rounded-xl shadow-lg shadow-red-200">
                        <i class="fa-solid fa-key text-lg"></i>
                    </div>
                    <div>
                        <span class="font-extrabold text-lg tracking-tight text-slate-900 uppercase italic leading-none block">
                            CERRAJERÍA <span class="text-red-500">MASTER</span>
                            </span>
                            <span class="text-[9px] font-bold text-slate-400 tracking-[0.2em] uppercase">Control de Servicios</span>

                    </div>
                </div>
            </div>

            <nav class="flex-1 flex flex-col pb-8">
                <div class="section-tag text-red-500/60 font-black">Operaciones</div>
                <?php
                $navItems = [
                    ['Dashboard', 'index', 'fa-grip', 'Panel General'],
                    ['Orders', 'index', 'fa-receipt', 'Ventas & Pedidos'],
                    ['AccountsReceivable', 'index', 'fa-money-check-dollar', 'Cuentas x Cobrar', ($isAdmin || $isStaff)],
                    ['DailyClosures', 'index', 'fa-cash-register', 'Control de Caja', ($isAdmin || $isStaff)],
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
                <?php endif; endforeach; ?>

                <div class="section-tag">Administración</div>
                <?php
                $adminItems = [
                    ['Products', 'index', 'fa-shapes', 'Catálogo Pro'],
                    ['DeliveryDrivers', 'index', 'fa-motorcycle', 'Repartidores'],
                    ['Clients', 'index', 'fa-address-card', 'Clientes'],
                    ['Ingredients', 'index', 'fa-boxes-stacked', 'Inventario'],
                ];
                foreach ($adminItems as $item):
                    $active = $this->request->getParam('controller') == $item[0];
                ?>
                    <?= $this->Html->link(
                        '<i class="fa-solid ' . $item[2] . '"></i> ' . $item[3],
                        ['controller' => $item[0], 'action' => $item[1]],
                        ['escape' => false, 'class' => 'nav-link ' . ($active ? 'active' : '')]
                    ) ?>
                <?php endforeach; ?>

                <?php if ($isAdmin): ?>
                    <div class="section-tag">Sistema</div>
                    <?= $this->Html->link('<i class="fa-solid fa-shield-user"></i> Gestión Usuarios', ['controller' => 'Users', 'action' => 'index'], ['escape' => false, 'class' => 'nav-link ' . ($this->request->getParam('controller') == 'Users' ? 'active' : '')]) ?>
                    <?= $this->Html->link('<i class="fa-solid fa-gears"></i> Ajustes Stock', ['controller' => 'InventoryAdjustments', 'action' => 'index'], ['escape' => false, 'class' => 'nav-link ' . ($this->request->getParam('controller') == 'InventoryAdjustments' ? 'active' : '')]) ?>
                <?php endif; ?>
            </nav>

            <div class="mt-auto border-t border-slate-100 bg-slate-50/50 p-4">
                <div class="flex items-center gap-3 px-4 py-2">
                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold text-xs uppercase">
                        <?= substr($user->username, 0, 1) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-900 truncate"><?= h($user->username) ?></p>
                        <p class="text-[10px] text-slate-500 truncate italic"><?= h($user->role) ?></p>
                    </div>
                    <?= $this->Html->link('<i class="fa-solid fa-arrow-right-from-bracket"></i>', ['controller' => 'Users', 'action' => 'logout'], ['escape' => false, 'class' => 'text-slate-400 hover:text-red-500 transition-colors', 'title' => 'Salir']) ?>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="main-content flex flex-col">
            <!-- Mobile Header -->
            <header class="lg:hidden mobile-header shadow-sm flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="bg-red-500 text-white p-1.5 rounded-lg">
                        <i class="fa-solid fa-key text-sm"></i>
                    </div>
                    <span class="font-extrabold text-sm text-slate-900 uppercase italic tracking-tighter">CERRAJERÍA <span class="text-red-500">MASTER</span></span>
                </div>
                <button id="drawer-toggle" class="p-2 text-slate-800 bg-slate-50 rounded-lg">
                    <i class="fa-solid fa-bars-staggered text-xl"></i>
                </button>
            </header>

            <!-- Page Content -->
            <div class="p-6 md:p-12 max-w-full min-h-screen">
                <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-100 pb-8">
                    <div>
                        <h2 class="text-[10px] font-black text-red-500 uppercase tracking-[0.3em] mb-2">Sección Activa</h2>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight"><?= h($this->fetch('title')) ?></h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-slate-400 italic"><?= date('l, d M Y') ?></span>
                    </div>
                </div>
                <?= $this->Flash->render() ?>
                <?= $this->fetch('content') ?>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer -->
    <div id="drawer-overlay" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[90] opacity-0 pointer-events-none transition-opacity"></div>
    <div id="mobile-drawer" class="fixed inset-y-0 left-0 w-80 bg-white z-[100] transform -translate-x-full transition-transform ease-in-out duration-300 shadow-2xl flex flex-col">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <span class="font-black text-slate-900 uppercase text-xs tracking-widest">Navegación</span>
            <button id="drawer-close" class="text-slate-400"><i class="fa-solid fa-xmark text-2xl"></i></button>
        </div>
        <div class="flex-1 overflow-y-auto pt-4">
            <!-- Reuse Links in Drawer -->
            <div class="section-tag text-red-500/60 font-black pt-2">Operaciones</div>
            <?php foreach ($navItems as $item): if (!isset($item[4]) || $item[4]): ?>
                <?= $this->Html->link('<i class="fa-solid ' . $item[2] . '"></i> ' . $item[3], ['controller' => $item[0], 'action' => $item[1]], ['escape' => false, 'class' => 'nav-link ' . ($this->request->getParam('controller') == $item[0] ? 'active' : '')]) ?>
            <?php endif; endforeach; ?>
            
            <div class="section-tag pt-6">Administración</div>
            <?php foreach ($adminItems as $item): ?>
                <?= $this->Html->link('<i class="fa-solid ' . $item[2] . '"></i> ' . $item[3], ['controller' => $item[0], 'action' => $item[1]], ['escape' => false, 'class' => 'nav-link ' . ($this->request->getParam('controller') == $item[0] ? 'active' : '')]) ?>
            <?php endforeach; ?>

            <div class="mt-8 p-6">
                <?= $this->Html->link('<i class="fa-solid fa-power-off mr-2"></i> Cerrar Sesión', ['controller' => 'Users', 'action' => 'logout'], ['escape' => false, 'class' => 'w-full block bg-red-50 text-red-600 text-center py-4 rounded-xl font-bold text-sm']) ?>
            </div>
        </div>
    </div>

    <?php else: ?>
        <!-- Login Layout Refined -->
        <main class="min-h-screen flex items-center justify-center p-8 bg-slate-50 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-red-500"></div>
            <div class="w-full max-w-sm relative z-10">
                <div class="text-center mb-10">
                    <div class="bg-gradient-to-br from-red-500 to-red-600 text-white w-14 h-14 flex items-center justify-center rounded-2xl mx-auto shadow-2xl shadow-red-200 mb-6 transform -rotate-3">
                        <i class="fa-solid fa-key text-2xl"></i>
                    </div>
                    <h1 class="text-3xl font-black text-slate-900 uppercase italic tracking-tighter leading-none">CERRAJERÍA <span class="text-red-500">MASTER</span></h1>
                    <p class="text-[10px] font-black text-slate-400 tracking-[0.4em] uppercase mt-3">Acceso Master</p>
                </div>
                <div class="bg-white p-2 rounded-3xl shadow-2xl shadow-slate-200 border border-slate-100">
                    <div class="p-6">
                        <?= $this->Flash->render() ?>
                        <?= $this->fetch('content') ?>
                    </div>
                </div>
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
                drawer.classList.remove('-translate-x-full');
                overlay.classList.remove('opacity-0', 'pointer-events-none');
                document.body.style.overflow = 'hidden';
            }

            function closeDrawer() {
                drawer.classList.add('-translate-x-full');
                overlay.classList.add('opacity-0', 'pointer-events-none');
                document.body.style.overflow = '';
            }

            if(toggle) toggle.addEventListener('click', openDrawer);
            if(close) close.addEventListener('click', closeDrawer);
            if(overlay) overlay.addEventListener('click', closeDrawer);
        });
    </script>
</body>
</html>
