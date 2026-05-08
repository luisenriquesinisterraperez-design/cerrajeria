<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class LockeryDataReset extends BaseMigration
{
    public function up(): void
    {
        // 1. Limpiar datos operativos antiguos
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->execute('TRUNCATE TABLE order_logs');
        $this->execute('TRUNCATE TABLE account_payments');
        $this->execute('TRUNCATE TABLE accounts_receivable');
        $this->execute('TRUNCATE TABLE inventory_adjustments');
        $this->execute('TRUNCATE TABLE product_ingredients');
        $this->execute('TRUNCATE TABLE orders');
        $this->execute('TRUNCATE TABLE products');
        $this->execute('TRUNCATE TABLE ingredients');
        $this->execute('TRUNCATE TABLE expenses');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

        // 2. Insertar Insumos (antiguos ingredientes)
        $ingredients = [
            ['name' => 'Llave Virgen Sencilla', 'stock' => 100, 'unit' => 'unidades', 'cost' => 500, 'created' => date('Y-m-d H:i:s'), 'modified' => date('Y-m-d H:i:s')],
            ['name' => 'Llave Virgen Seguridad', 'stock' => 50, 'unit' => 'unidades', 'cost' => 2500, 'created' => date('Y-m-d H:i:s'), 'modified' => date('Y-m-d H:i:s')],
            ['name' => 'Cilindro Estándar 60mm', 'stock' => 10, 'unit' => 'unidades', 'cost' => 15000, 'created' => date('Y-m-d H:i:s'), 'modified' => date('Y-m-d H:i:s')],
            ['name' => 'Chapa de Pomo Baño', 'stock' => 5, 'unit' => 'unidades', 'cost' => 25000, 'created' => date('Y-m-d H:i:s'), 'modified' => date('Y-m-d H:i:s')],
            ['name' => 'Lubricante Grafito', 'stock' => 12, 'unit' => 'unidades', 'cost' => 8000, 'created' => date('Y-m-d H:i:s'), 'modified' => date('Y-m-d H:i:s')],
        ];
        $this->table('ingredients')->insert($ingredients)->save();

        // 3. Insertar Servicios/Productos Pro
        $products = [
            ['name' => 'Duplicado Llave Sencilla', 'price' => 3000, 'description' => 'Copia de llave metálica estándar', 'status' => 1, 'created' => date('Y-m-d H:i:s'), 'modified' => date('Y-m-d H:i:s')],
            ['name' => 'Duplicado Llave Seguridad', 'price' => 15000, 'description' => 'Copia de llave de puntos o seguridad', 'status' => 1, 'created' => date('Y-m-d H:i:s'), 'modified' => date('Y-m-d H:i:s')],
            ['name' => 'Apertura de Puerta (Domicilio)', 'price' => 45000, 'description' => 'Servicio técnico de apertura por olvido de llaves', 'status' => 1, 'created' => date('Y-m-d H:i:s'), 'modified' => date('Y-m-d H:i:s')],
            ['name' => 'Cambio de Guarda / Cilindro', 'price' => 35000, 'description' => 'Mantenimiento y cambio de combinación', 'status' => 1, 'created' => date('Y-m-d H:i:s'), 'modified' => date('Y-m-d H:i:s')],
            ['name' => 'Instalación Chapa Seguridad', 'price' => 120000, 'description' => 'Instalación completa de cerradura principal', 'status' => 1, 'created' => date('Y-m-d H:i:s'), 'modified' => date('Y-m-d H:i:s')],
        ];
        $this->table('products')->insert($products)->save();

        // 4. Relacionar Insumos con Productos (Para descuento automático de stock)
        // Duplicado sencillo usa 1 llave sencilla
        $this->execute("INSERT INTO product_ingredients (product_id, ingredient_id, quantity_required) VALUES (1, 1, 1)");
        // Duplicado seguridad usa 1 llave seguridad
        $this->execute("INSERT INTO product_ingredients (product_id, ingredient_id, quantity_required) VALUES (2, 2, 1)");
    }

    public function down(): void
    {
        // No es necesario revertir el truncate, pero podríamos limpiar los inserts
        $this->execute('DELETE FROM products');
        $this->execute('DELETE FROM ingredients');
        $this->execute('DELETE FROM product_ingredients');
    }
}
