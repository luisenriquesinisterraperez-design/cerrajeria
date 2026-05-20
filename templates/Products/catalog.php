<?php
$identity = $this->request->getAttribute('identity');
$user = $identity ? $identity->getOriginalData() : null;
$clientName = $user ? $user->username : 'Cliente';
$phone = $whatsappPhone ?? '573170880796';
?>
<header class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Catálogo de Productos</h1>
        <p class="text-blue-600 font-bold uppercase text-xs tracking-widest">Solicitá tu pedido y te confirmamos</p>
    </div>
    <?php if ($user): ?>
        <div class="text-right">
            <p class="text-[10px] text-slate-400 font-bold">Hola, <span class="text-blue-600"><?= h($clientName) ?></span></p>
        </div>
    <?php endif; ?>
</header>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php foreach ($products as $product): ?>
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden hover:shadow-lg transition-shadow">
        <div class="h-40 bg-slate-100 flex items-center justify-center">
            <?php if ($product->image): ?>
                <img src="<?= $this->Url->webroot('img/products/' . $product->image) ?>" class="w-full h-full object-cover" alt="<?= h($product->name) ?>">
            <?php else: ?>
                <i class="fa-solid fa-key text-5xl text-slate-300"></i>
            <?php endif; ?>
        </div>
        <div class="p-5">
            <h3 class="font-black text-slate-800 uppercase text-sm mb-1"><?= h($product->name) ?></h3>
            <?php if ($product->description): ?>
                <p class="text-[10px] text-slate-500 mb-3 italic"><?= h($product->description) ?></p>
            <?php endif; ?>
            <div class="text-2xl font-black text-orange-600 mb-4">$<?= number_format($product->price, 0) ?></div>

            <div class="space-y-2">
                <?= $this->Form->create(null, ['url' => ['action' => 'request'], 'class' => 'm-0']) ?>
                    <?= $this->Form->hidden('product_id', ['value' => $product->id]) ?>
                    <?= $this->Form->hidden('type', ['value' => 'compra']) ?>
                    <button type="submit" class="w-full text-center py-3 bg-green-600 text-white rounded-xl font-black text-xs uppercase hover:bg-green-700 transition-all">
                        <i class="fa-solid fa-cart-shopping mr-2"></i> Solicitar Compra
                    </button>
                <?= $this->Form->end() ?>

                <?= $this->Form->create(null, ['url' => ['action' => 'request'], 'class' => 'm-0']) ?>
                    <?= $this->Form->hidden('product_id', ['value' => $product->id]) ?>
                    <?= $this->Form->hidden('type', ['value' => 'credito']) ?>
                    <button type="submit" class="w-full text-center py-3 bg-blue-600 text-white rounded-xl font-black text-xs uppercase hover:bg-blue-700 transition-all">
                        <i class="fa-solid fa-file-invoice mr-2"></i> Solicitar a Crédito
                    </button>
                <?= $this->Form->end() ?>

                <a href="https://wa.me/<?= $phone ?>?text=<?= urlencode("Hola, quiero info sobre: {$product->name}") ?>"
                   target="_blank"
                   class="block w-full text-center py-2 bg-slate-100 text-slate-600 rounded-xl font-bold text-[10px] uppercase hover:bg-slate-200 transition-all">
                    <i class="fa-brands fa-whatsapp mr-1"></i> Consultar
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
