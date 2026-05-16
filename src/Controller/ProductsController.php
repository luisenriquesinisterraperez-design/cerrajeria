<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Utility\Text;
use Exception;

class ProductsController extends AppController
{
    public function index()
    {
        $query = $this->Products->find()
            ->contain(['ProductIngredients'])
            ->orderBy(['Products.id' => 'DESC']);

        $products = $this->paginate($query);
        $this->set(compact('products'));
    }

    public function add()
    {
        $product = $this->Products->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            $image = $this->request->getData('image_file');
            if ($image && $image->getError() === 0) {
                $extension = pathinfo($image->getClientFilename(), PATHINFO_EXTENSION);
                $filename = Text::slug($data['name']) . '-' . time() . '.' . $extension;
                $targetPath = WWW_ROOT . 'img' . DS . 'products' . DS . $filename;
                $image->moveTo($targetPath);
                $data['image'] = $filename;
            }

            $product = $this->Products->patchEntity($product, $data);
            if ($this->Products->save($product)) {
                $this->Flash->success(__('El producto ha sido guardado.'));
            } else {
                $this->Flash->error(__('El producto no pudo ser guardado. Inténtalo de nuevo.'));
            }
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null)
    {
        $product = $this->Products->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            
            $image = $this->request->getData('image_file');
            if ($image && $image->getError() === 0) {
                $extension = pathinfo($image->getClientFilename(), PATHINFO_EXTENSION);
                $filename = Text::slug($data['name']) . '-' . time() . '.' . $extension;
                $targetPath = WWW_ROOT . 'img' . DS . 'products' . DS . $filename;
                $image->moveTo($targetPath);
                $data['image'] = $filename;
            }

            $product = $this->Products->patchEntity($product, $data);
            if ($this->Products->save($product)) {
                $this->Flash->success(__('El producto ha sido actualizado.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('El producto no pudo ser actualizado. Inténtalo de nuevo.'));
        }
        $this->set(compact('product'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        try {
            $product = $this->Products->get($id);
            $productName = $product->name;
            if ($this->Products->delete($product)) {
                $identity = $this->request->getAttribute('identity');
                $user = $identity ? $identity->getOriginalData() : null;
                $this->logAudit(
                    $user ? $user->id : 1,
                    "ELIMINACIÓN: El usuario " . ($user ? $user->username : 'Sistema') . " eliminó el producto \"{$productName}\""
                );
                $this->Flash->success(__('Producto eliminado.'));
            }
        } catch (Exception $e) {
            $this->Flash->error(__('No se puede eliminar el producto porque tiene ventas asociadas.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function toggleStatus($id = null)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $product = $this->Products->get($id);
        $product->status = !$product->status;
        if ($this->Products->save($product)) {
            $this->Flash->success(__('Estado actualizado.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
