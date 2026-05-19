<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DeliveryDriver $deliveryDriver
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Delivery Driver'), ['action' => 'edit', $deliveryDriver->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Delivery Driver'), ['action' => 'delete', $deliveryDriver->id], ['confirm' => __('Are you sure you want to delete # {0}?', $deliveryDriver->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Delivery Drivers'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Delivery Driver'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="deliveryDrivers view content">
            <h3><?= h($deliveryDriver->name) ?></h3>
            <div class="overflow-x-auto">
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($deliveryDriver->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Last Name') ?></th>
                    <td><?= h($deliveryDriver->last_name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Phone') ?></th>
                    <td><?= h($deliveryDriver->phone) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($deliveryDriver->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($deliveryDriver->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($deliveryDriver->modified) ?></td>
                </tr>
            </table>
            </div>
            <div class="related">
                <h4><?= __('Related Orders') ?></h4>
                <?php if (!empty($deliveryDriver->orders)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Product Id') ?></th>
                            <th><?= __('Quantity') ?></th>
                            <th><?= __('Type') ?></th>
                            <th><?= __('Shipping Cost') ?></th>
                            <th><?= __('Customer Name') ?></th>
                            <th><?= __('Customer Phone') ?></th>
                            <th><?= __('Customer Address') ?></th>
                            <th><?= __('Total') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Modified') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($deliveryDriver->orders as $order) : ?>
                        <tr>
                            <td><?= h($order->id) ?></td>
                            <td><?= h($order->product_id) ?></td>
                            <td><?= h($order->quantity) ?></td>
                            <td><?= h($order->type) ?></td>
                            <td><?= h($order->shipping_cost) ?></td>
                            <td><?= h($order->customer_name) ?></td>
                            <td><?= h($order->customer_phone) ?></td>
                            <td><?= h($order->customer_address) ?></td>
                            <td><?= h($order->total) ?></td>
                            <td><?= h($order->created) ?></td>
                            <td><?= h($order->modified) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Orders', 'action' => 'view', $order->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Orders', 'action' => 'edit', $order->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'Orders', 'action' => 'delete', $order->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $order->id),
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