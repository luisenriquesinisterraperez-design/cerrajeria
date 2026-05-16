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
        $ingredients = $this->paginate($query);

        $this->set(compact('ingredients'));
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
        if ($this->request->is(['patch', 'post', 'put'])) {
            $ingredient = $this->Ingredients->patchEntity($ingredient, $this->request->getData());
            if ($this->Ingredients->save($ingredient)) {
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
