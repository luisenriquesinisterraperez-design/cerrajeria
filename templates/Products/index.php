<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Product> $products
 */
?>
<header class="mb-8">
    <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Catálogo de Menú</h1>
    <p class="text-orange-500 font-bold uppercase text-xs tracking-widest">Administrar productos</p>
</header>

<div class="bg-white p-8 rounded-3xl border border-orange-100 shadow-lg mb-10">
    <h3 class="font-black text-slate-800 uppercase text-sm mb-6 flex items-center gap-2">
        <i class="fa-solid fa-plus-circle text-orange-500"></i> Nuevo Producto
    </h3>
    <?= $this->Form->create(null, ['url' => ['action' => 'add'], 'type' => 'file']) ?>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-4">
            <div class="md:col-span-2">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Referencia</label>
                <input type="text" readonly placeholder="AUTO" class="w-full p-4 bg-slate-100 border rounded-2xl font-mono text-xs outline-none text-slate-500">
            </div>
            <div class="md:col-span-7">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Nombre del Producto</label>
                <?= $this->Form->control('name', ['label' => false, 'placeholder' => 'Ej: Super Burger', 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-orange-500 transition-all font-bold text-slate-700']) ?>
            </div>
            <div class="md:col-span-3">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Precio ($)</label>
                <?= $this->Form->control('price', ['label' => false, 'placeholder' => '0', 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-orange-500 transition-all font-bold text-orange-600']) ?>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
            <div class="md:col-span-8">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Descripción / Detalles</label>
                <?= $this->Form->control('description', ['label' => false, 'placeholder' => 'Ej: Carne 150g, queso, lechuga...', 'class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-orange-500 transition-all']) ?>
            </div>
            <div class="md:col-span-4">
                <label class="text-[10px] font-bold text-slate-400 ml-2 uppercase">Imagen del Producto</label>
                <?= $this->Form->file('image_file', ['class' => 'w-full p-4 bg-slate-50 border rounded-2xl outline-none focus:ring-2 focus:ring-orange-500 transition-all text-xs cursor-pointer']) ?>
            </div>
        </div>
        
        <div class="pt-4 border-t border-slate-50">
            <?= $this->Form->button(__('Añadir al Menú'), ['class' => 'w-full bg-orange-600 text-white font-black rounded-2xl py-5 uppercase shadow-lg hover:bg-orange-700 active:scale-95 transition-all text-lg']) ?>
        </div>
    <?= $this->Form->end() ?>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
    <?php foreach ($products as $product): ?>
    <div class="bg-white rounded-2xl border <?= $product->status ? 'border-orange-100' : 'border-red-200 opacity-75' ?> shadow-sm relative group overflow-hidden flex flex-col">
        <!-- Imagen Cuadrada Contenida -->
        <div class="relative w-full h-40 bg-slate-100 overflow-hidden">
            <!-- Botones de Acción -->
            <div class="absolute top-2 right-2 flex gap-1 z-20 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <?= $this->Html->link('<i class="fa-solid fa-pen text-sm"></i>', ['action' => 'edit', $product->id], ['escape' => false, 'class' => 'bg-white text-blue-600 w-9 h-9 rounded-lg shadow-md flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all']) ?>
                <?= $this->Form->postLink('<i class="fa-solid fa-trash-alt text-sm"></i>', ['action' => 'delete', $product->id], ['confirm' => __('¿Eliminar {0}?', $product->name), 'escape' => false, 'class' => 'bg-white text-red-600 w-9 h-9 rounded-lg shadow-md flex items-center justify-center hover:bg-red-600 hover:text-white transition-all']) ?>
            </div>

            <?php if ($product->image): ?>
                <img src="<?= $this->Url->webroot('img/products/' . $product->image) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="<?= h($product->name) ?>">
            <?php else: ?>
                <div class="w-full h-full flex items-center justify-center">
                    <i class="fa-solid fa-burger text-4xl text-slate-200"></i>
                </div>
            <?php endif; ?>
            
            <div class="absolute bottom-2 left-2 bg-black/40 backdrop-blur-sm text-white px-2 py-0.5 rounded text-[8px] font-black font-mono uppercase">
                REF-<?= str_pad((string)$product->id, 3, '0', STR_PAD_LEFT) ?>
            </div>
        </div>

        <div class="p-4 flex-1 flex flex-col">
            <div class="flex justify-between items-start mb-1">
                <h4 class="font-black text-slate-800 uppercase leading-tight text-sm truncate pr-2" title="<?= h($product->name) ?>"><?= h($product->name) ?></h4>
                <?php if ($isAdmin): ?>
                    <span class="text-sm font-black text-orange-600">$<?= number_format($product->price, 0) ?></span>
                <?php endif; ?>
            </div>
            
            <p class="text-[10px] text-slate-500 mb-4 line-clamp-2 h-7 italic leading-tight">
                <?= h($product->description ?: 'Sin descripción') ?>
            </p>
            
            <div class="mt-auto pt-3 border-t border-slate-50 flex justify-between items-center">
                <span class="text-[9px] font-black uppercase <?= $product->status ? 'text-green-500' : 'text-red-500' ?>">
                    <?= $product->status ? 'Disponible' : 'Agotado' ?>
                </span>
                
                <?= $this->Form->create(null, ['url' => ['action' => 'toggleStatus', $product->id], 'id' => 'status-form-' . $product->id, 'class' => 'm-0']) ?>
                    <label class="switch scale-75 origin-right">
                        <input type="checkbox" <?= $product->status ? 'checked' : '' ?> onchange="document.getElementById('status-form-<?= $product->id ?>').submit();">
                        <span class="slider"></span>
                    </label>
                <?= $this->Form->end() ?>
            </div>

            <!-- Botón Configurar Receta -->
            <div class="mt-3">
                <?php if (empty($product->product_ingredients)): ?>
                    <div class="text-[8px] bg-red-50 text-red-500 font-bold p-1 rounded mb-1 text-center uppercase">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Sin receta (No descuenta stock)
                    </div>
                <?php endif; ?>
                <?= $this->Html->link('<i class="fa-solid fa-mortar-pestle mr-1"></i> Receta', ['controller' => 'ProductIngredients', 'action' => 'recipe', $product->id], ['escape' => false, 'class' => 'block w-full text-center py-2 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase hover:bg-orange-600 transition-colors shadow-sm']) ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<ul class="flex items-center justify-center gap-2 list-none p-0 m-0">
    <?= $this->Paginator->prev('<i class="fa-solid fa-chevron-left"></i>', ['escape' => false, 'templates' => ['prevActive' => '<a rel="prev" href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-sm text-slate-700 flex items-center justify-center">{{text}}</a>', 'prevDisabled' => '<span class="bg-slate-100 px-4 py-3 rounded-xl border border-slate-200 font-bold text-sm text-slate-300 flex items-center justify-center">{{text}}</span>']]) ?>
    <?= $this->Paginator->numbers(['templates' => ['number' => '<a href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-xs text-slate-700 flex items-center justify-center min-w-[2.5rem]">{{text}}</a>', 'current' => '<span class="bg-blue-600 text-white px-4 py-3 rounded-xl font-bold text-xs flex items-center justify-center min-w-[2.5rem]">{{text}}</span>', 'ellipsis' => '<span class="px-2 py-3 text-slate-400 font-bold text-xs">&hellip;</span>']]) ?>
    <?= $this->Paginator->next('<i class="fa-solid fa-chevron-right"></i>', ['escape' => false, 'templates' => ['nextActive' => '<a rel="next" href="{{url}}" class="bg-white px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-sm text-slate-700 flex items-center justify-center">{{text}}</a>', 'nextDisabled' => '<span class="bg-slate-100 px-4 py-3 rounded-xl border border-slate-200 font-bold text-sm text-slate-300 flex items-center justify-center">{{text}}</span>']]) ?>
</ul>
<div class="mt-4 text-center text-[10px] font-bold text-slate-400">
    <?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} de {{count}} productos')) ?>
</div>
