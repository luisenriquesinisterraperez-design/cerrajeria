<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\OrderLog> $orderLogs
 */
?>
<header class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Auditoría de Pedidos</h1>
        <p class="text-orange-500 font-bold uppercase text-xs tracking-widest">Historial de modificaciones y eliminaciones</p>
    </div>
    <?= $this->Html->link('<i class="fa-solid fa-arrow-left mr-2"></i> Volver a Ventas', ['controller' => 'Orders', 'action' => 'index'], ['escape' => false, 'class' => 'bg-slate-200 text-slate-600 px-4 py-2 rounded-xl font-bold text-xs hover:bg-slate-300 transition-all']) ?>
</header>

<div class="bg-white rounded-3xl border border-orange-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-900 text-white text-[10px] uppercase font-bold tracking-widest">
                <tr>
                    <th class="p-5">Fecha</th>
                    <th class="p-5">Usuario</th>
                    <th class="p-5">Pedido #</th>
                    <th class="p-5">Detalles de la Modificación</th>
                </tr>
            </thead>
            <tbody class="divide-y text-sm">
                <?php foreach ($orderLogs as $log): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-4 text-slate-500 font-bold text-[11px]">
                            <?= $log->created->format('d/m/Y h:i A') ?>
                        </td>
                        <td class="p-4">
                            <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full font-black text-[10px] uppercase">
                                <?= $log->hasValue('user') ? h($log->user->username) : 'Sistema' ?>
                            </span>
                        </td>
                        <td class="p-4 font-black text-slate-400">
                            #<?= h($log->order_id) ?>
                            <span class="ml-2 bg-red-100 text-red-600 text-[8px] px-2 py-0.5 rounded-full uppercase font-black italic">Registro</span>
                        </td>
                        <td class="p-4 text-slate-700 leading-relaxed italic">
                            <?= h($log->modification_details) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($orderLogs->count() === 0): ?>
                    <tr>
                        <td colspan="4" class="p-10 text-center text-slate-400 italic font-bold">
                            No hay registros de auditoría disponibles.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<ul class="flex items-center justify-center gap-2 list-none p-0 m-0">
    <?= $this->Paginator->prev('<i class="fa-solid fa-chevron-left"></i>', ['escape' => false, 'templates' => ['prevActive' => '<a rel="prev" href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-sm text-slate-700 flex items-center justify-center">{{text}}</a>', 'prevDisabled' => '<span class="bg-slate-100 px-4 py-3 rounded-xl border border-slate-200 font-bold text-sm text-slate-300 flex items-center justify-center">{{text}}</span>']]) ?>
    <?= $this->Paginator->numbers(['templates' => ['number' => '<a href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-xs text-slate-700 flex items-center justify-center min-w-[2.5rem]">{{text}}</a>', 'current' => '<span class="bg-blue-600 text-white px-4 py-3 rounded-xl font-bold text-xs flex items-center justify-center min-w-[2.5rem]">{{text}}</span>', 'ellipsis' => '<span class="px-2 py-3 text-slate-400 font-bold text-xs">&hellip;</span>']]) ?>
    <?= $this->Paginator->next('<i class="fa-solid fa-chevron-right"></i>', ['escape' => false, 'templates' => ['nextActive' => '<a rel="next" href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-sm text-slate-700 flex items-center justify-center">{{text}}</a>', 'nextDisabled' => '<span class="bg-slate-100 px-4 py-3 rounded-xl border border-slate-200 font-bold text-sm text-slate-300 flex items-center justify-center">{{text}}</span>']]) ?>
</ul>
