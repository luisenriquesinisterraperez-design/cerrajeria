<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Order> $requests
 */
?>
<header class="mb-8">
    <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Solicitudes Pendientes</h1>
    <p class="text-yellow-600 font-bold uppercase text-xs tracking-widest">Pedidos de clientes pendientes de aprobación</p>
</header>

<?php if ($requests->count() === 0): ?>
<div class="bg-white p-16 rounded-3xl border border-slate-100 shadow-sm text-center">
    <i class="fa-solid fa-inbox text-6xl text-slate-200 mb-6"></i>
    <p class="text-lg font-bold text-slate-400">No hay solicitudes pendientes</p>
    <p class="text-xs text-slate-400">Los clientes pueden solicitar productos desde el Catálogo</p>
</div>
<?php else: ?>
<div class="space-y-4">
    <?php foreach ($requests as $request): ?>
    <div class="bg-white rounded-3xl border border-yellow-200 shadow-sm p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-[10px] font-black uppercase border border-yellow-300">Pendiente</span>
                <span class="text-[10px] text-slate-400 font-bold"><?= $request->created->format('d/m/Y h:i A') ?></span>
            </div>
            <h3 class="font-black text-slate-800 uppercase text-sm">
                <?= $request->hasValue('product') ? h($request->product->name) : 'Producto #' . $request->product_id ?>
            </h3>
            <div class="flex flex-wrap gap-x-6 gap-y-1 mt-2 text-xs">
                <span class="text-slate-500"><i class="fa-solid fa-user mr-1"></i> <?= h($request->customer_name) ?></span>
                <span class="text-slate-500"><i class="fa-solid fa-phone mr-1"></i> <?= h($request->customer_phone) ?></span>
                <span class="text-orange-600 font-black">$<?= number_format($request->total, 0) ?></span>
                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase <?= $request->payment_method === 'Crédito' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' ?>">
                    <?= h($request->payment_method) ?>
                </span>
            </div>
        </div>
        <div class="flex gap-2">
            <?= $this->Form->postLink('<i class="fa-solid fa-check mr-1"></i> Aprobar', ['action' => 'approve', $request->id], ['confirm' => '¿Aprobar esta solicitud?', 'escape' => false, 'class' => 'bg-green-600 text-white px-5 py-3 rounded-xl font-black text-xs uppercase hover:bg-green-700 transition-all shadow-sm']) ?>
            <?= $this->Form->postLink('<i class="fa-solid fa-xmark mr-1"></i> Rechazar', ['action' => 'reject', $request->id], ['confirm' => '¿Rechazar solicitud?', 'escape' => false, 'class' => 'bg-red-500 text-white px-5 py-3 rounded-xl font-black text-xs uppercase hover:bg-red-600 transition-all shadow-sm']) ?>
            <?= $this->Form->postLink('<i class="fa-solid fa-trash"></i>', ['action' => 'delete', $request->id], ['confirm' => '¿Eliminar solicitud?', 'escape' => false, 'class' => 'p-3 text-slate-300 hover:text-red-500 transition-colors']) ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<ul class="flex items-center justify-center gap-2 list-none p-0 m-0 mt-8">
    <?= $this->Paginator->prev('<i class="fa-solid fa-chevron-left"></i>', ['escape' => false, 'templates' => ['prevActive' => '<a rel="prev" href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-sm text-slate-700 flex items-center justify-center">{{text}}</a>', 'prevDisabled' => '<span class="bg-slate-100 px-4 py-3 rounded-xl border border-slate-200 font-bold text-sm text-slate-300 flex items-center justify-center">{{text}}</span>']]) ?>
    <?= $this->Paginator->numbers(['templates' => ['number' => '<a href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-xs text-slate-700 flex items-center justify-center min-w-[2.5rem]">{{text}}</a>', 'current' => '<span class="bg-blue-600 text-white px-4 py-3 rounded-xl font-bold text-xs flex items-center justify-center min-w-[2.5rem]">{{text}}</span>', 'ellipsis' => '<span class="px-2 py-3 text-slate-400 font-bold text-xs">&hellip;</span>']]) ?>
    <?= $this->Paginator->next('<i class="fa-solid fa-chevron-right"></i>', ['escape' => false, 'templates' => ['nextActive' => '<a rel="next" href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-sm text-slate-700 flex items-center justify-center">{{text}}</a>', 'nextDisabled' => '<span class="bg-slate-100 px-4 py-3 rounded-xl border border-slate-200 font-bold text-sm text-slate-300 flex items-center justify-center">{{text}}</span>']]) ?>
</ul>
<div class="mt-4 text-center text-[10px] font-bold text-slate-400">
    <?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} de {{count}} solicitudes')) ?>
</div>
<?php endif; ?>
