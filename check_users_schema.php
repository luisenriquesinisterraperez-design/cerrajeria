<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/bootstrap.php';

use Cake\Datasource\ConnectionManager;

$conn = ConnectionManager::get('default');
$schema = $conn->getSchemaCollection();
$table = $schema->describe('users');

echo "COLUMNAS EN LA TABLA 'users':\n";
foreach ($table->columns() as $col) {
    echo "- $col\n";
}
