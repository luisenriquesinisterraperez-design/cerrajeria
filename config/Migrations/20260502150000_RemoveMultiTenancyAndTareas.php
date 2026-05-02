<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveMultiTenancyAndTareas extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        // 1. Eliminar tablas que ya no se usarán
        if ($this->hasTable('tareas')) {
            $this->table('tareas')->drop()->save();
        }
        if ($this->hasTable('branches')) {
            // Primero eliminar FKs si existen (dependiendo de la DB)
            $this->table('branches')->drop()->save();
        }
        if ($this->hasTable('companies')) {
            $this->table('companies')->drop()->save();
        }

        // 2. Quitar columnas de relación en las demás tablas
        $tablesWithTenancy = [
            'users',
            'orders',
            'clients',
            'ingredients',
            'delivery_drivers',
            'expenses',
            'daily_closures',
            'accounts_receivable',
            'account_payments',
            'order_logs',
            'inventory_adjustments'
        ];

        foreach ($tablesWithTenancy as $tableName) {
            if ($this->hasTable($tableName)) {
                $table = $this->table($tableName);
                
                if ($table->hasColumn('company_id')) {
                    $table->removeColumn('company_id');
                }
                if ($table->hasColumn('branch_id')) {
                    $table->removeColumn('branch_id');
                }
                
                $table->save();
            }
        }
    }
}
