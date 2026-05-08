<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class FixARNullabilityInOrders extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('orders');
        $table->changeColumn('accounts_receivable_id', 'integer', [
            'default' => null,
            'null' => true,
        ]);
        $table->update();
    }
}
