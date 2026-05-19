<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Ingredient $ingredient
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Ingredient'), ['action' => 'edit', $ingredient->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Ingredient'), ['action' => 'delete', $ingredient->id], ['confirm' => __('Are you sure you want to delete # {0}?', $ingredient->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Ingredients'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Ingredient'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="ingredients view content">
            <h3><?= h($ingredient->name) ?></h3>
            <div class="overflow-x-auto">
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($ingredient->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Unit') ?></th>
                    <td><?= h($ingredient->unit) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($ingredient->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Stock') ?></th>
                    <td><?= $this->Number->format($ingredient->stock) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($ingredient->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($ingredient->modified) ?></td>
                </tr>
            </table>
            </div>
            <div class="related">
                <h4><?= __('Related Product Ingredients') ?></h4>
                <?php if (!empty($ingredient->product_ingredients)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Product Id') ?></th>
                            <th><?= __('Quantity Required') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($ingredient->product_ingredients as $productIngredient) : ?>
                        <tr>
                            <td><?= h($productIngredient->id) ?></td>
                            <td><?= h($productIngredient->product_id) ?></td>
                            <td><?= h($productIngredient->quantity_required) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'ProductIngredients', 'action' => 'view', $productIngredient->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'ProductIngredients', 'action' => 'edit', $productIngredient->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'ProductIngredients', 'action' => 'delete', $productIngredient->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $productIngredient->id),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>