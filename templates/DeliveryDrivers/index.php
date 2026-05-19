<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\DeliveryDriver> $deliveryDrivers
 */
?>
<header class="mb-8">
    <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Gestión de Repartidores</h1>
    <p class="text-orange-500 font-bold uppercase text-xs tracking-widest">Equipo de domicilios</p>
</header>

<div class="bg-white p-8 rounded-3xl border border-orange-100 shadow-sm mb-10">
    <h3 class="font-black text-slate-800 uppercase text-sm mb-6 flex items-center gap-2">
        <i class="fa-solid fa-user-plus text-orange-500"></i> Registrar Domiciliario
    </h3>
    <?= $this->Form->create(null, ['url' => ['action' => 'add'], 'class' => 'space-y-4']) ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="flex flex-col gap-1">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Nombre</label>
                <?= $this->Form->control('name', ['label' => false, 'placeholder' => 'Nombre', 'class' => 'p-4 bg-slate-50 border rounded-2xl outline-none focus:bg-white focus:ring-2 focus:ring-orange-500 transition-all']) ?>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Apellido</label>
                <?= $this->Form->control('last_name', ['label' => false, 'placeholder' => 'Apellido', 'class' => 'p-4 bg-slate-50 border rounded-2xl outline-none focus:bg-white focus:ring-2 focus:ring-orange-500 transition-all']) ?>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Celular</label>
                <?= $this->Form->control('phone', ['label' => false, 'placeholder' => 'Ej: 3001234567', 'class' => 'p-4 bg-slate-50 border rounded-2xl outline-none focus:bg-white focus:ring-2 focus:ring-orange-500 transition-all']) ?>
            </div>
        </div>
        <?= $this->Form->button(__('Registrar Domiciliario'), ['class' => 'w-full bg-slate-900 text-white font-black rounded-2xl py-4 uppercase hover:bg-slate-800 transition-all shadow-lg tracking-widest']) ?>
    <?= $this->Form->end() ?>
</div>

<div class="bg-white rounded-3xl border border-orange-100 overflow-x-auto shadow-sm">
    <table class="w-full text-left">
        <thead class="bg-slate-900 text-white text-[10px] uppercase font-bold tracking-widest">
            <tr>
                <th class="p-5">ID</th>
                <th class="p-5">Nombre Completo</th>
                <th class="p-5">Celular</th>
                <th class="p-5 text-right">Acción</th>
            </tr>
        </thead>
        <tbody class="divide-y text-sm">
            <?php foreach ($deliveryDrivers as $driver): ?>
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="p-5 font-mono text-xs text-slate-400 font-bold">#<?= $driver->id ?></td>
                <td class="p-5 font-black text-slate-800 uppercase text-sm"><?= h($driver->full_name) ?></td>
                <td class="p-5 font-bold text-slate-500 text-sm">
                    <i class="fa-solid fa-phone mr-2 text-orange-400"></i><?= h($driver->phone) ?>
                </td>
                <td class="p-5 text-right flex justify-end gap-2">
                    <?= $this->Html->link('<i class="fa-solid fa-pen"></i>', ['action' => 'edit', $driver->id], ['escape' => false, 'class' => 'p-2 inline-block text-blue-400 hover:text-blue-600 transition-colors']) ?>
                    <?= $this->Form->postLink('<i class="fa-solid fa-user-minus"></i>', ['action' => 'delete', $driver->id], ['confirm' => __('¿Eliminar a {0}?', $driver->full_name), 'escape' => false, 'class' => 'p-2 inline-block text-red-200 hover:text-red-600 transition-colors']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<ul class="flex items-center justify-center gap-2 list-none p-0 m-0">
    <?= $this->Paginator->prev('<i class="fa-solid fa-chevron-left"></i>', ['escape' => false, 'templates' => ['prevActive' => '<a rel="prev" href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-sm text-slate-700 flex items-center justify-center">{{text}}</a>', 'prevDisabled' => '<span class="bg-slate-100 px-4 py-3 rounded-xl border border-slate-200 font-bold text-sm text-slate-300 flex items-center justify-center">{{text}}</span>']]) ?>
    <?= $this->Paginator->numbers(['templates' => ['number' => '<a href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-xs text-slate-700 flex items-center justify-center min-w-[2.5rem]">{{text}}</a>', 'current' => '<span class="bg-blue-600 text-white px-4 py-3 rounded-xl font-bold text-xs flex items-center justify-center min-w-[2.5rem]">{{text}}</span>', 'ellipsis' => '<span class="px-2 py-3 text-slate-400 font-bold text-xs">&hellip;</span>']]) ?>
    <?= $this->Paginator->next('<i class="fa-solid fa-chevron-right"></i>', ['escape' => false, 'templates' => ['nextActive' => '<a rel="next" href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-sm text-slate-700 flex items-center justify-center">{{text}}</a>', 'nextDisabled' => '<span class="bg-slate-100 px-4 py-3 rounded-xl border border-slate-200 font-bold text-sm text-slate-300 flex items-center justify-center">{{text}}</span>']]) ?>
</ul>
<div class="mt-4 text-center text-[10px] font-bold text-slate-400">
    <?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} de {{count}} registros')) ?>
</div>
