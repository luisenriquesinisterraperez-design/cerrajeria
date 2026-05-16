<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * InventoryAdjustments Controller
 *
 * @property \App\Model\Table\InventoryAdjustmentsTable $InventoryAdjustments
 */
class InventoryAdjustmentsController extends AppController
{
    public function index()
    {
        $query = $this->InventoryAdjustments->find()
            ->contain(['Ingredients'])
            ->orderBy(['InventoryAdjustments.created' => 'DESC']);
            
        $inventoryAdjustments = $this->paginate($query);

        $this->set(compact('inventoryAdjustments'));
    }

    public function add()
    {
        $inventoryAdjustment = $this->InventoryAdjustments->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $inventoryAdjustment = $this->InventoryAdjustments->patchEntity($inventoryAdjustment, $data);
            
            if ($this->InventoryAdjustments->save($inventoryAdjustment)) {
                // Actualizar el stock en la tabla de Ingredientes
                $ingredientsTable = $this->fetchTable('Ingredients');
                $ingredient = $ingredientsTable->get($inventoryAdjustment->ingredient_id);
                
                if ($inventoryAdjustment->type === 'baja') {
                    $ingredient->stock -= $inventoryAdjustment->quantity;
                } else {
                    $ingredient->stock += $inventoryAdjustment->quantity;
                }
                
                $ingredientsTable->save($ingredient);

                $this->Flash->success(__('El ajuste de inventario ha sido registrado y el stock actualizado.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo registrar el ajuste. Por favor, intente de nuevo.'));
        }
        $ingredients = $this->InventoryAdjustments->Ingredients->find('list', ['limit' => 200])->all();
        $this->set(compact('inventoryAdjustment', 'ingredients'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $inventoryAdjustment = $this->InventoryAdjustments->get($id);
        
        $ingredientName = $inventoryAdjustment->ingredient->name ?? "ID #{$inventoryAdjustment->ingredient_id}";
        $adjType = $inventoryAdjustment->type;
        $adjQty = $inventoryAdjustment->adjustment_quantity ?? $inventoryAdjustment->quantity;

        // Revertir el stock antes de borrar el registro
        $ingredientsTable = $this->fetchTable('Ingredients');
        $ingredient = $ingredientsTable->get($inventoryAdjustment->ingredient_id);
        
        if ($inventoryAdjustment->type === 'baja') {
            $ingredient->stock += $adjQty;
        } else {
            $ingredient->stock -= $adjQty;
        }
        $ingredientsTable->save($ingredient);

        if ($this->InventoryAdjustments->delete($inventoryAdjustment)) {
            $identity = $this->request->getAttribute('identity');
            $user = $identity ? $identity->getOriginalData() : null;
            $this->logAudit(
                $user ? $user->id : 1,
                "ELIMINACIÓN: El usuario " . ($user ? $user->username : 'Sistema') . " eliminó un ajuste de inventario ({$adjType}) de \"{$ingredientName}\" por {$adjQty}"
            );
            $this->Flash->success(__('El registro ha sido eliminado y el stock revertido.'));
        } else {
            $this->Flash->error(__('No se pudo eliminar el registro.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
