<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="fixed inset-0 z-[100] bg-[#0f172a] flex items-center justify-center p-6">
    <!-- Fondo decorativo suave -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-blue-600/20 rounded-full blur-[100px]"></div>
        <div class="absolute -bottom-[10%] -right-[10%] w-[50%] h-[50%] bg-yellow-400/10 rounded-full blur-[100px]"></div>
    </div>

    <div class="relative w-full max-w-sm">
        <div class="text-center mb-10">
            <div class="bg-yellow-400 text-slate-900 w-20 h-20 flex items-center justify-center rounded-[2.5rem] mx-auto shadow-2xl shadow-yellow-400/20 mb-8 transform -rotate-12 border-4 border-white">
                <i class="fa-solid fa-store text-3xl"></i>
            </div>
            <h1 class="text-4xl font-black text-white uppercase italic tracking-tighter leading-none">SARRIA<span class="text-yellow-400">.</span></h1>
            <p class="text-[10px] font-black text-blue-400 tracking-[0.5em] uppercase mt-4">Cerrajería Profesional</p>
        </div>

        <div class="bg-white p-2 rounded-[3rem] shadow-2xl shadow-black/50 border border-slate-800">
            <div class="p-8">
                <?= $this->Flash->render() ?>

                <?= $this->Form->create() ?>
                <div class="space-y-5">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase ml-2 mb-2 block tracking-widest">Acceso Seguro</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300">
                                <i class="fa-solid fa-user"></i>
                            </span>
                            <?= $this->Form->control('username', [
                                'label' => false,
                                'placeholder' => 'Usuario',
                                'class' => 'w-full pl-12 p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition-all text-slate-700 font-bold',
                                'required' => true
                            ]) ?>
                        </div>
                    </div>

                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <?= $this->Form->control('password', [
                            'label' => false,
                            'placeholder' => 'Contraseña',
                            'class' => 'w-full pl-12 p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition-all text-slate-700 font-bold',
                            'required' => true,
                            'type' => 'password'
                        ]) ?>
                    </div>

                    <div class="pt-4">
                        <?= $this->Form->button(__('ENTRAR AL SISTEMA'), [
                            'class' => 'w-full bg-blue-600 text-white py-4 rounded-2xl font-black hover:bg-yellow-400 hover:text-slate-900 shadow-xl shadow-blue-600/20 active:scale-[0.98] transition-all uppercase tracking-widest text-xs'
                        ]) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>

        <div class="text-center mt-10">
            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">© <?= date('Y') ?> CERRAJERÍA SARRIA PRO Edition</p>
        </div>
    </div>
</div>
