<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Ingredients Controller
 *
 * @property \App\Model\Table\IngredientsTable $Ingredients
 */
class IngredientsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Ingredients->find()->orderBy(['Ingredients.name' => 'ASC']);

        $totalInversion = 0;
        foreach ($query->all() as $ing) {
            $totalInversion += (float)$ing->cost * (float)$ing->stock;
        }

        $ingredients = $this->paginate($query);

        $this->set(compact('ingredients', 'totalInversion'));
    }

    /**
     * View method
     *
     * @param string|null $id Ingredient id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $ingredient = $this->Ingredients->get($id, contain: ['ProductIngredients']);
        $this->set(compact('ingredient'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $ingredient = $this->Ingredients->newEmptyEntity();
        if ($this->request->is('post')) {
            $ingredient = $this->Ingredients->patchEntity($ingredient, $this->request->getData());
            if ($this->Ingredients->save($ingredient)) {
                $this->Flash->success(__('The ingredient has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The ingredient could not be saved. Please, try again.'));
        }
        $this->set(compact('ingredient'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Ingredient id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $ingredient = $this->Ingredients->get($id, contain: [
            'ProductIngredients' => ['Products']
        ]);
        $oldStock = (float)$ingredient->stock;
        $oldCost = (float)$ingredient->cost;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $ingredient = $this->Ingredients->patchEntity($ingredient, $data);
            if ($this->Ingredients->save($ingredient)) {
                $newStock = (float)$ingredient->stock;
                $diff = $newStock - $oldStock;

                if (abs($diff) > 0.001) {
                    $type = $diff > 0 ? 'alta' : 'baja';
                    $adjustmentsTable = $this->fetchTable('InventoryAdjustments');
                    $adj = $adjustmentsTable->newEntity([
                        'ingredient_id' => $ingredient->id,
                        'quantity' => abs($diff),
                        'type' => $type,
                        'reason' => 'Edición manual desde gestión de insumos',
                        'observations' => 'Stock anterior: ' . number_format($oldStock, 2) . ' → Nuevo: ' . number_format($newStock, 2),
                    ]);
                    $adjustmentsTable->save($adj);

                    $identity = $this->request->getAttribute('identity');
                    $user = $identity ? $identity->getOriginalData() : null;
                    $this->logAudit(
                        $user ? $user->id : 1,
                        "AJUSTE STOCK: El usuario " . ($user ? $user->username : 'Sistema') . " cambió el stock de \"{$ingredient->name}\" de {$oldStock} a {$newStock} ({$type} {$diff})"
                    );
                }

                $this->Flash->success(__('El insumo ha sido actualizado.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo actualizar el insumo. Por favor, intente de nuevo.'));
        }
        $this->set(compact('ingredient'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Ingredient id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $ingredient = $this->Ingredients->get($id);
        $ingredientName = $ingredient->name;
        
        try {
            // 1. Eliminar relaciones en recetas
            $productIngredientsTable = $this->fetchTable('ProductIngredients');
            $productIngredientsTable->deleteAll(['ingredient_id' => $id]);

            // 2. Eliminar historial de ajustes de este insumo
            $adjustmentsTable = $this->fetchTable('InventoryAdjustments');
            $adjustmentsTable->deleteAll(['ingredient_id' => $id]);

            if ($this->Ingredients->delete($ingredient)) {
                $identity = $this->request->getAttribute('identity');
                $user = $identity ? $identity->getOriginalData() : null;
                $this->logAudit(
                    $user ? $user->id : 1,
                    "ELIMINACIÓN: El usuario " . ($user ? $user->username : 'Sistema') . " eliminó el insumo \"{$ingredientName}\""
                );
                $this->Flash->success(__('El insumo y sus relaciones han sido eliminados correctamente.'));
            } else {
                $this->Flash->error(__('No se pudo eliminar el insumo. Por favor, intente de nuevo.'));
            }
        } catch (\Exception $e) {
            $this->Flash->error(__('Error de base de datos: ') . $e->getMessage());
        }

        return $this->redirect(['action' => 'index']);
    }
}
