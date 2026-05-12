<?php
require __DIR__ . '/vendor/autoload.php';

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Locator\TableLocator;
use Authentication\PasswordHasher\DefaultPasswordHasher;

// Bootstrap
require __DIR__ . '/config/bootstrap.php';

$tableLocator = new TableLocator();
$usersTable = $tableLocator->get('Users');

$user = $usersTable->find()->where(['username' => 'admin'])->first();

if (!$user) {
    echo "USUARIO 'admin' NO ENCONTRADO EN LA BASE DE DATOS.\n";
} else {
    echo "USUARIO 'admin' ENCONTRADO.\n";
    print_r($user->toArray());
    
    $hasher = new DefaultPasswordHasher();
    if ($hasher->check('1234', $user->password)) {
        echo "LA CLAVE '1234' ES CORRECTA.\n";
    } else {
        echo "LA CLAVE '1234' ES INCORRECTA.\n";
    }
}
