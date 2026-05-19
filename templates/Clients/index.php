<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Client> $clients
 */
?>
<header class="mb-8">
    <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Base de Clientes</h1>
    <p class="text-orange-500 font-bold uppercase text-xs tracking-widest">Gestión de contactos</p>
</header>

<div class="bg-white p-8 rounded-3xl border border-orange-100 shadow-lg mb-10">
    <h3 class="font-black text-slate-800 uppercase text-sm mb-6 flex items-center gap-2">
        <i class="fa-solid fa-user-plus text-orange-500"></i> Nuevo Cliente
    </h3>
    <?= $this->Form->create(null, ['url' => ['action' => 'add']]) ?>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
            <div class="md:col-span-2">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">ID</label>
                <input type="text" readonly placeholder="AUTO" class="w-full p-4 bg-slate-100 border rounded-2xl font-mono text-xs outline-none text-slate-500">
            </div>
            <div class="md:col-span-6">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Nombre Completo</label>
                <?= $this->Form->control('full_name', ['label' => false, 'placeholder' => 'Ej: Juan Pérez', 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-orange-500 transition-all font-bold text-slate-700']) ?>
            </div>
            <div class="md:col-span-4">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Celular</label>
                <?= $this->Form->control('phone', ['label' => false, 'placeholder' => 'Ej: 3001234567', 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-orange-500 transition-all']) ?>
            </div>
        </div>
        
        <div class="mb-6">
            <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Dirección de Entrega</label>
            <?= $this->Form->control('address', ['label' => false, 'placeholder' => 'Calle 123 #45-67, Barrio...', 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-orange-500 transition-all']) ?>
        </div>
        
        <div class="pt-4 border-t border-slate-50">
            <?= $this->Form->button(__('Registrar Cliente'), ['class' => 'w-full bg-slate-900 text-white font-black rounded-2xl py-5 uppercase shadow-lg hover:bg-slate-800 active:scale-95 transition-all text-lg']) ?>
        </div>
    <?= $this->Form->end() ?>
</div>

<div class="bg-white rounded-3xl border border-orange-100 overflow-x-auto shadow-sm">
    <table class="w-full text-left">
        <thead class="bg-slate-900 text-white text-[10px] uppercase font-bold tracking-widest">
            <tr>
                <th class="p-5">ID</th>
                <th class="p-5">Nombre Completo</th>
                <th class="p-5">Celular</th>
                <th class="p-5">Dirección</th>
                <th class="p-5 text-right">Acción</th>
            </tr>
        </thead>
        <tbody class="divide-y text-sm">
            <?php foreach ($clients as $client): ?>
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="p-5 font-mono text-xs text-slate-400 font-bold">#<?= $client->id ?></td>
                <td class="p-5 font-black text-slate-800 uppercase text-sm"><?= h($client->full_name) ?></td>
                <td class="p-5 font-bold text-slate-500 text-sm">
                    <i class="fa-solid fa-phone mr-2 text-orange-400"></i><?= h($client->phone) ?>
                </td>
                <td class="p-5 text-slate-500 italic text-xs">
                    <?= h($client->address ?: 'Sin dirección registrada') ?>
                </td>
                <td class="p-5 text-right flex justify-end gap-2 mt-2">
                    <?= $this->Html->link('<i class="fa-solid fa-pen"></i>', ['action' => 'edit', $client->id], ['escape' => false, 'class' => 'p-2 inline-block text-blue-400 hover:text-blue-600']) ?>
                    <?= $this->Form->postLink('<i class="fa-solid fa-trash"></i>', ['action' => 'delete', $client->id], ['confirm' => __('¿Eliminar cliente?'), 'escape' => false, 'class' => 'p-2 inline-block text-red-200 hover:text-red-600']) ?>
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
