<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var mixed $deliveryDrivers
 */
?>
<header class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Nuevo Usuario</h1>
        <p class="text-blue-600 font-bold uppercase text-xs tracking-widest">Definir identidad y rol</p>
    </div>
    <?= $this->Html->link('<i class="fa-solid fa-arrow-left mr-2"></i> Volver', ['action' => 'index'], ['escape' => false, 'class' => 'bg-slate-200 text-slate-600 px-4 py-2 rounded-xl font-bold text-xs hover:bg-slate-300 transition-all']) ?>
</header>

<div class="bg-white p-10 rounded-[3rem] border border-blue-50 shadow-2xl max-w-2xl mx-auto">
    <?= $this->Form->create($user) ?>
        <div class="space-y-6">
            <h3 class="font-black text-slate-900 uppercase text-sm tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-key text-blue-500"></i> Credenciales de Acceso
            </h3>
            
            <div>
                <label class="text-[10px] font-black text-slate-400 ml-2 uppercase tracking-widest">Nombre de Usuario</label>
                <?= $this->Form->control('username', ['label' => false, 'class' => 'w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all font-bold']) ?>
            </div>

            <div>
                <label class="text-[10px] font-black text-slate-400 ml-2 uppercase tracking-widest">Contraseña</label>
                <?= $this->Form->control('password', ['label' => false, 'class' => 'w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all font-bold']) ?>
            </div>

            <div>
                <label class="text-[10px] font-black text-slate-400 ml-2 uppercase tracking-widest">Rol del Sistema</label>
                <?= $this->Form->select('role', [
                    'admin' => '👑 ADMINISTRADOR',
                    'staff' => '🛠️ STAFF / OPERADOR',
                    'repartidor' => '🏍️ REPARTIDOR',
                    'cliente' => '👤 CLIENTE'
                ], ['id' => 'role-select', 'class' => 'w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 font-black text-xs uppercase']) ?>
            </div>

            <div id="driver-link-div" class="hidden">
                <label class="text-[10px] font-black text-slate-400 ml-2 uppercase tracking-widest italic text-blue-600">Vincular Repartidor</label>
                <?= $this->Form->control('delivery_driver_id', ['options' => $deliveryDrivers, 'empty' => 'Seleccionar...', 'label' => false, 'class' => 'w-full p-4 bg-blue-50 border border-blue-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 font-bold']) ?>
            </div>

            <div id="client-link-div" class="hidden">
                <label class="text-[10px] font-black text-slate-400 ml-2 uppercase tracking-widest italic text-blue-600">Vincular Cliente</label>
                <?= $this->Form->control('client_id', ['options' => $clients, 'empty' => 'Seleccionar...', 'label' => false, 'class' => 'w-full p-4 bg-blue-50 border border-blue-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 font-bold']) ?>
            </div>
        </div>

        <div class="pt-10 mt-10 border-t border-slate-50">
            <?= $this->Form->button(__('Crear Acceso CERRAJERÍA SARRIA'), ['class' => 'w-full bg-slate-900 text-white font-black rounded-2xl py-5 uppercase shadow-xl hover:bg-yellow-400 hover:text-slate-900 transition-all text-lg tracking-widest']) ?>
        </div>
    <?= $this->Form->end() ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role-select');
        const driverDiv = document.getElementById('driver-link-div');
        const clientDiv = document.getElementById('client-link-div');

        function toggleRoles() {
            driverDiv.classList.add('hidden');
            clientDiv.classList.add('hidden');

            if (roleSelect.value === 'repartidor') {
                driverDiv.classList.remove('hidden');
            } else if (roleSelect.value === 'cliente') {
                clientDiv.classList.remove('hidden');
            }
        }

        roleSelect.addEventListener('change', toggleRoles);
        toggleRoles(); 
    });
</script>
