<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Log\Log;

class ProductIngredientsController extends AppController
{
    public function recipe($productId = null)
    {
        if (!$productId) {
            return $this->redirect(['controller' => 'Products', 'action' => 'index']);
        }

        $product = $this->fetchTable('Products')->get($productId, [
            'contain' => ['ProductIngredients' => ['Ingredients']],
        ]);

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            if (isset($data['edit_id'])) {
                // Editar cantidad de un ingrediente existente
                $productIngredient = $this->ProductIngredients->get($data['edit_id']);
                $productIngredient = $this->ProductIngredients->patchEntity($productIngredient, $data);
                if ($this->ProductIngredients->save($productIngredient)) {
                    $this->Flash->success(__('Cantidad actualizada.'));
                } else {
                    Log::error('PI EDIT ERR: ' . json_encode($productIngredient->getErrors()));
                    $msg = 'No se pudo actualizar la cantidad.';
                    $errors = $productIngredient->getErrors();
                    if (!empty($errors)) {
                        $flat = [];
                        array_walk_recursive($errors, function ($v) use (&$flat) {
                            $flat[] = $v;
                        });
                        $msg .= ' ' . implode(' | ', $flat);
                    }
                    $this->Flash->error(__($msg));
                }
            } else {
                // Añadir nuevo ingrediente
                $productIngredient = $this->ProductIngredients->newEmptyEntity();
                $productIngredient = $this->ProductIngredients->patchEntity($productIngredient, $data);
                $productIngredient->product_id = $productId;

                Log::info('PI SAVE DATA: ' . json_encode($data) . ' | Entity: ' . json_encode($productIngredient->toArray()));

                if ($this->ProductIngredients->save($productIngredient)) {
                    $this->Flash->success(__('Ingrediente añadido a la receta.'));
                } else {
                    Log::error('PI SAVE ERR: ' . json_encode($productIngredient->getErrors()));
                    $msg = 'No se pudo añadir el ingrediente.';
                    $errors = $productIngredient->getErrors();
                    if (!empty($errors)) {
                        $flat = [];
                        array_walk_recursive($errors, function ($v) use (&$flat) {
                            $flat[] = $v;
                        });
                        $msg .= ' ' . implode(' | ', $flat);
                    }
                    $this->Flash->error(__($msg));
                }
            }

            return $this->redirect(['action' => 'recipe', $productId]);
        }

        $ingredients = $this->ProductIngredients->Ingredients->find('list', ['limit' => 200])->all();
        $ingredientCosts = [];
        $costsData = $this->ProductIngredients->Ingredients->find()
            ->select(['id', 'cost', 'unit'])
            ->all();
        foreach ($costsData as $i) {
            $ingredientCosts[$i->id] = ['cost' => $i->cost, 'unit' => $i->unit];
        }
        $this->set(compact('product', 'ingredients', 'ingredientCosts'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $productIngredient = $this->ProductIngredients->get($id, contain: ['Ingredients']);
        $productId = $productIngredient->product_id;
        $ingredientName = $productIngredient->ingredient->name ?? "ID #{$productIngredient->ingredient_id}";

        if ($this->ProductIngredients->delete($productIngredient)) {
            $identity = $this->request->getAttribute('identity');
            $user = $identity ? $identity->getOriginalData() : null;
            $this->logAudit(
                $user ? $user->id : 1,
                'ELIMINACIÓN: El usuario ' . ($user ? $user->username : 'Sistema') . " removió el ingrediente \"{$ingredientName}\" de la receta del producto #{$productId}",
            );
            $this->Flash->success(__('Ingrediente removido de la receta.'));
        }

        return $this->redirect(['action' => 'recipe', $productId]);
    }
}
