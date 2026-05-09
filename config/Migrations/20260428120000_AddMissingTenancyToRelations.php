<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddMissingTenancyToRelations extends BaseMigration
{
    public function change(): void
    {
        $tables = ['product_ingredients', 'tareas'];

        foreach ($tables as $t) {
            if ($this->hasTable($t)) {
                $table = $this->table($t);
                if (!$table->hasColumn('company_id')) {
                    $table->addColumn('company_id', 'integer', ['null' => true, 'after' => 'id'])
                          ->addColumn('branch_id', 'integer', ['null' => true, 'after' => 'company_id'])
                          ->addIndex(['company_id'])
                          ->addIndex(['branch_id'])
                          ->update();
                }
            }
        }
    }
}
