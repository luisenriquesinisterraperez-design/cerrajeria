<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\User> $users
 */
?>
<header class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Usuarios del Sistema</h1>
        <p class="text-blue-600 font-bold uppercase text-xs tracking-widest">Control de accesos y roles</p>
    </div>
    <?= $this->Html->link('<i class="fa-solid fa-user-plus mr-2"></i> Nuevo Usuario', ['action' => 'add'], ['escape' => false, 'class' => 'bg-slate-900 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase hover:bg-blue-600 transition-all shadow-lg']) ?>
</header>

<div class="bg-white rounded-3xl border border-slate-100 overflow-x-auto shadow-sm">
    <table class="w-full text-left">
        <thead class="bg-slate-900 text-white text-[10px] uppercase font-bold tracking-widest">
            <tr>
                <th class="p-5">ID</th>
                <th class="p-5">Usuario</th>
                <th class="p-5 text-center">Rol</th>
                <th class="p-5">Fecha Registro</th>
                <th class="p-5 text-right">Acción</th>
            </tr>
        </thead>
        <tbody class="divide-y text-sm">
            <?php foreach ($users as $user): ?>
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="p-5 font-mono text-xs text-slate-400 font-bold">#<?= $user->id ?></td>
                <td class="p-5 font-black text-slate-800 uppercase text-sm"><?= h($user->username) ?></td>
                <td class="p-5 text-center">
                    <?php if (!empty($user->is_superadmin) || $user->username === 'admin' || $user->role === 'admin'): ?>
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase bg-slate-900 text-white border border-slate-800 tracking-tighter">
                            👑 Administrador
                        </span>
                    <?php elseif ($user->role === 'staff'): ?>
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase bg-emerald-100 text-emerald-700 tracking-tighter">
                            🛠️ Staff / Vendedor
                        </span>
                    <?php elseif ($user->role === 'repartidor'): ?>
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase bg-blue-100 text-blue-700 tracking-tighter">
                            🏍️ Repartidor
                        </span>
                    <?php else: ?>
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase bg-slate-100 text-slate-500 tracking-tighter">
                            <?= h($user->role) ?>
                        </span>
                    <?php endif; ?>
                </td>
                <td class="p-5">
                    <p class="text-[10px] text-slate-400 font-bold"><?= $user->created ? $user->created->format('d/m/Y') : '---' ?></p>
                </td>
                <td class="p-5 text-right flex justify-end gap-3 mt-1">
                    <?= $this->Html->link('<i class="fa-solid fa-pen"></i>', ['action' => 'edit', $user->id], ['escape' => false, 'class' => 'p-2 inline-block text-blue-400 hover:text-blue-600']) ?>
                    <?= $this->Form->postLink('<i class="fa-solid fa-trash"></i>', ['action' => 'delete', $user->id], ['confirm' => __('¿Eliminar usuario?'), 'escape' => false, 'class' => 'p-2 inline-block text-red-200 hover:text-red-600']) ?>
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
