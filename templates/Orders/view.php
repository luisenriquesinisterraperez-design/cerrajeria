<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order $order
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Order'), ['action' => 'edit', $order->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Order'), ['action' => 'delete', $order->id], ['confirm' => __('Are you sure you want to delete # {0}?', $order->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Orders'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Order'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="orders view content">
            <h3><?= h($order->type) ?></h3>
            <div class="overflow-x-auto">
            <table>
                <tr>
                    <th><?= __('Product') ?></th>
                    <td><?= $order->hasValue('product') ? $this->Html->link($order->product->name, ['controller' => 'Products', 'action' => 'view', $order->product->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Type') ?></th>
                    <td><?= h($order->type) ?></td>
                </tr>
                <tr>
                    <th><?= __('Customer Name') ?></th>
                    <td><?= h($order->customer_name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Customer Phone') ?></th>
                    <td><?= h($order->customer_phone) ?></td>
                </tr>
                <tr>
                    <th><?= __('Delivery Driver') ?></th>
                    <td><?= $order->hasValue('delivery_driver') ? $this->Html->link($order->delivery_driver->name, ['controller' => 'DeliveryDrivers', 'action' => 'view', $order->delivery_driver->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($order->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Quantity') ?></th>
                    <td><?= $this->Number->format($order->quantity) ?></td>
                </tr>
                <tr>
                    <th><?= __('Shipping Cost') ?></th>
                    <td><?= $this->Number->format($order->shipping_cost) ?></td>
                </tr>
                <tr>
                    <th><?= __('Total') ?></th>
                    <td><?= $this->Number->format($order->total) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($order->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($order->modified) ?></td>
                </tr>
            </table>
            </div>
            <div class="text">
                <strong><?= __('Customer Address') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($order->customer_address)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>