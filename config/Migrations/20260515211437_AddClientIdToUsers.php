<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddClientIdToUsers extends BaseMigration
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
        $table = $this->table('users');
        $table->addColumn('client_id', 'integer', [
            'default' => null,
            'null' => true,
        ]);
        $table->addIndex([
            'client_id',
        ], [
            'name' => 'BY_CLIENT_ID',
            'unique' => false,
        ]);
        $table->update();
    }
}
