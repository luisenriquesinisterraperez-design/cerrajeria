<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddARToOrders extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('orders');
        $table->addColumn('accounts_receivable_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addIndex([
            'accounts_receivable_id',
        
            ], [
            'name' => 'BY_ACCOUNTS_RECEIVABLE_ID',
            'unique' => false,
        ]);
        $table->update();
    }
}
