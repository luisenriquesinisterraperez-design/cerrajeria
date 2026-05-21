<?php
declare(strict_types=1);

namespace App\Model\Table;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\I18n\DateTime;
use Cake\Log\Log;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Exception;

class OrdersTable extends Table
{
    use LocatorAwareTrait;

    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('orders');
        $this->setDisplayField('type');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Products', [
            'foreignKey' => 'product_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('DeliveryDrivers', [
            'foreignKey' => 'delivery_driver_id',
        ]);
        $this->belongsTo('AccountsReceivable', [
            'foreignKey' => 'accounts_receivable_id',
        ]);
        $this->hasMany('OrderLogs', [
            'foreignKey' => 'order_id',
            'dependent' => false,
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('product_id')
            ->notEmptyString('product_id', 'Debe seleccionar un producto');

        $validator
            ->integer('quantity')
            ->notEmptyString('quantity')
            ->greaterThan('quantity', 0, 'La cantidad debe ser al menos 1');

        $validator
            ->scalar('status')
            ->notEmptyString('status')
            ->add('status', 'validValue', [
                'rule' => ['inList', ['recibido', 'preparando', 'en camino', 'entregado', 'cancelado', 'pendiente']],
                'message' => 'El estado del pedido no es válido',
            ]);

        $validator
            ->decimal('shipping_cost')
            ->allowEmptyString('shipping_cost');

        $validator
            ->scalar('customer_name')
            ->notEmptyString('customer_name', 'El nombre del cliente es obligatorio');

        $validator
            ->scalar('customer_phone')
            ->notEmptyString('customer_phone', 'El celular es obligatorio');

        $validator
            ->scalar('customer_address')
            ->allowEmptyString('customer_address');

        $validator
            ->integer('delivery_driver_id')
            ->allowEmptyString('delivery_driver_id');

        $validator
            ->integer('accounts_receivable_id')
            ->allowEmptyString('accounts_receivable_id');

        $validator
            ->scalar('payment_method')
            ->notEmptyString('payment_method', 'Debe seleccionar un método de pago');

        return $validator;
    }

    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        // 1. Calcular el total
        if ($entity->isNew() || $entity->isDirty('product_id') || $entity->isDirty('quantity') || $entity->isDirty('shipping_cost')) {
            try {
                $product = $this->Products->get($entity->product_id);
                $entity->total = ($product->price * $entity->quantity) + (float)($entity->shipping_cost ?? 0);
            } catch (Exception $e) {
                Log::error('Error calculando total para pedido: ' . $e->getMessage());
            }
        }

        // --- RESTAURAR INVENTARIO SI ES EDICIÓN ---
        if (!$entity->isNew() && ($entity->isDirty('product_id') || $entity->isDirty('quantity'))) {
            // Solo restauramos si el estado original NO era cancelado (si era cancelado, ya estaba restaurado)
            $originalStatus = $entity->getOriginal('status');
            if ($originalStatus !== 'cancelado') {
                $oldProductId = $entity->getOriginal('product_id');
                $oldQuantity = $entity->getOriginal('quantity');

                // Si getOriginal no devolvió nada (caso raro), usamos los valores actuales como fallback
                $oldProductId = $oldProductId ?: $entity->product_id;
                $oldQuantity = $oldQuantity ?? $entity->quantity;

                Log::info("Restoring OLD inventory due to edit (Order ID {$entity->id}): Product ID {$oldProductId}, Qty {$oldQuantity}");
                $this->_adjustInventory($oldProductId, (int)$oldQuantity, 'add');
            }

            // Si el nuevo estado no es cancelado, necesitamos restar lo nuevo en afterSave
            if ($entity->status !== 'cancelado') {
                $entity->set('inventory_needs_update', true);
            } else {
                $entity->set('inventory_needs_update', false);
            }
        } else {
            // Marcar si necesitamos ajustar inventario en afterSave
            $needsUpdate = $entity->isNew() || $entity->isDirty('product_id') || $entity->isDirty('quantity');
            $entity->set('inventory_needs_update', $needsUpdate && !in_array($entity->status, ['cancelado', 'pendiente']));
        }

        // --- MANEJO DE CAMBIOS DE ESTADO ---
        if ($entity->isDirty('status')) {
            $oldStatus = $entity->getOriginal('status');
            $newStatus = $entity->status;

            // 1. De Activo a Cancelado -> Restaurar stock
            if ($oldStatus !== 'cancelado' && $newStatus === 'cancelado') {
                Log::info("Order status changed to cancelled. Restoring inventory for Order ID: {$entity->id}");
                $this->_adjustInventory($entity->product_id, (int)$entity->quantity, 'add');
                $entity->set('inventory_needs_update', false);
            }
            // 2. De Cancelado a Activo -> Restar stock
            elseif ($oldStatus === 'cancelado' && $newStatus !== 'cancelado') {
                Log::info("Order status changed from cancelled to active. Subtracting inventory for Order ID: {$entity->id}");
                $entity->set('inventory_needs_update', true);
            }
            // 3. De Pendiente a Recibido -> Restar stock (no se restó al crear)
            elseif ($oldStatus === 'pendiente' && $newStatus === 'recibido') {
                Log::info("Order approved (pendiente -> recibido). Subtracting inventory for Order ID: {$entity->id}");
                $entity->set('inventory_needs_update', true);
            }

            // Registrar cambio de estado en la huella (excepto si es nuevo registro)
            if (!$entity->isNew()) {
                $this->logOrderChange($entity, $oldStatus, $newStatus, 'status', $options);
            }
        }
    }

    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        // Aplicar el descuento del inventario actual
        if ($entity->get('inventory_needs_update')) {
            Log::info("SUBTRACTING INVENTORY (AfterSave, Order ID {$entity->id}): Product ID {$entity->product_id}, Qty {$entity->quantity}");
            $this->_adjustInventory($entity->product_id, (int)$entity->quantity, 'subtract');
            // Evitar que se ejecute de nuevo si se guarda la misma entidad
            $entity->set('inventory_needs_update', false);
        }
    }

    public function beforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        // Restore inventory if order was not cancelled
        if ($entity->status !== 'cancelado') {
            Log::info("Restoring inventory for Order ID: {$entity->id} before deletion.");
            $this->_adjustInventory($entity->product_id, (int)$entity->quantity, 'add');
        }

        // Handle associated AccountsReceivable
        $accountsReceivableTable = $this->getTableLocator()->get('AccountsReceivable');
        $accounts = $accountsReceivableTable->find()->where(['order_id' => $entity->id])->all();
        foreach ($accounts as $account) {
            $accountsReceivableTable->delete($account);
        }
    }

    /**
     * Registra cambios significativos en el historial
     */
    protected function logOrderChange($entity, $oldValue, $newValue, $field, $options = []): void
    {
        try {
            $logsTable = $this->getTableLocator()->get('OrderLogs');

            // Obtener el usuario de las opciones (pasado desde el controller)
            $user = $options['user'] ?? null;
            $userId = $user ? $user->id : 1; // Default admin
            $username = $user ? $user->username : 'Sistema';

            $details = "Cambio de {$field}: de '{$oldValue}' a '{$newValue}' (vía " . $username . ')';
            if ($field === 'status') {
                $details = "Estado actualizado: de '{$oldValue}' a '{$newValue}' (por " . $username . ')';
            }

            $log = $logsTable->newEntity([
                'order_id' => $entity->id,
                'user_id' => $userId,
                'modification_details' => $details,
                'created' => new DateTime(),
            ]);
            $logsTable->save($log);
        } catch (Exception $e) {
            Log::error('Error registrando huella en OrdersTable: ' . $e->getMessage());
        }
    }

    /**
     * Método centralizado para ajustar inventario
     */
    protected function _adjustInventory($productId, $quantity, $mode = 'subtract'): void
    {
        $productIngredientsTable = $this->getTableLocator()->get('ProductIngredients');
        $ingredientsTable = $this->getTableLocator()->get('Ingredients');

        Log::info("_adjustInventory CALLED: product_id={$productId}, qty={$quantity}, mode={$mode}");

        $recipes = $productIngredientsTable->find()
            ->where(['product_id' => $productId])
            ->all();

        Log::info('_adjustInventory: found ' . count($recipes) . " recipes for product {$productId}");

        if ($recipes->isEmpty()) {
            Log::debug("No hay receta configurada para el producto ID {$productId}");

            return;
        }

        foreach ($recipes as $recipe) {
            try {
                $ingredient = $ingredientsTable->get($recipe->ingredient_id);
                $amount = (float)$recipe->quantity_required * $quantity;

                $oldStock = (float)$ingredient->stock;
                if ($mode === 'subtract') {
                    $ingredient->stock = $oldStock - $amount;
                } else {
                    $ingredient->stock = $oldStock + $amount;
                }

                if (!$ingredientsTable->save($ingredient)) {
                    Log::error("Error al guardar stock del insumo ID {$ingredient->id}: " . json_encode($ingredient->getErrors()));
                } else {
                    Log::info("STOCK ACTUALIZADO ({$mode}): {$ingredient->name} | Antes: {$oldStock} | Ahora: {$ingredient->stock} (Pedido: {$productId} x{$quantity})");
                }
            } catch (Exception $e) {
                Log::error("Excepción ajustando inventario (Insumo ID {$recipe->ingredient_id}): " . $e->getMessage());
            }
        }
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['product_id'], 'Products'), ['errorField' => 'product_id']);
        $rules->add($rules->existsIn(['delivery_driver_id'], 'DeliveryDrivers'), ['errorField' => 'delivery_driver_id']);

        return $rules;
    }
}
