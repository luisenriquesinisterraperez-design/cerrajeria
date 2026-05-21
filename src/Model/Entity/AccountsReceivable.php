<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AccountsReceivable Entity
 *
 * @property int $id
 * @property int $client_id
 * @property int|null $order_id
 * @property string $amount
 * @property string|null $description
 * @property string $status
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Client $client
 * @property \App\Model\Entity\Order $order
 * @property \App\Model\Entity\AccountPayment[] $account_payments
 * @property float $total_paid
 * @property float $balance
 */
class AccountsReceivable extends Entity
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
        'client_id' => true,
        'order_id' => true,
        'amount' => true,
        'description' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
        'client' => true,
        'order' => true,
        'account_payments' => true,
    ];

    protected function _getTotalPaid()
    {
        if (empty($this->account_payments)) {
            return 0.0;
        }
        $total = 0;
        foreach ($this->account_payments as $payment) {
            $total += (float)$payment->amount;
        }

        return (float)$total;
    }

    protected function _getBalance()
    {
        return (float)$this->amount - $this->total_paid;
    }
}
