<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Order Entity
 *
 * @property int $id
 * @property int $product_id
 * @property int $quantity
 * @property string $type
 * @property string $shipping_cost
 * @property string $customer_name
 * @property string $customer_phone
 * @property string|null $customer_address
 * @property int|null $delivery_driver_id
 * @property string $total
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Product $product
 * @property \App\Model\Entity\DeliveryDriver $delivery_driver
 */
class Order extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'order_group_id' => true,
        'product_id' => true,
        'quantity' => true,
        'type' => true,
        'shipping_cost' => true,
        'customer_name' => true,
        'customer_phone' => true,
        'customer_address' => true,
        'delivery_driver_id' => true,
        'total' => true,
        'payment_method' => true,
        'status' => true,
        'accounts_receivable_id' => true,
        'delivered_at' => true,
        'created' => true,
        'modified' => true,
        'product' => true,
        'delivery_driver' => true,
    ];
}
