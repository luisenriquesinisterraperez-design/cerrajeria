<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Utility\Text;
use Exception;

class ProductsController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        $identity = $this->request->getAttribute('identity');
        $user = $identity ? $identity->getOriginalData() : null;
        $isCliente = ($user && !empty($user->role) && $user->role === 'cliente');
        $action = $this->request->getParam('action');

        if ($isCliente && !in_array($action, ['catalog', 'index', 'request'])) {
            $this->Flash->error(__('Acceso Denegado.'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
    }

    public function catalog()
    {
        $products = $this->Products->find()
            ->where(['Products.status' => true])
            ->orderBy(['Products.name' => 'ASC'])
            ->all();

        $this->set('whatsappPhone', env('WHATSAPP_PHONE', '573170880796'));

        $this->set(compact('products'));
    }

    public function request()
    {
        $this->request->allowMethod(['post']);
        $data = $this->request->getData();

        $identity = $this->request->getAttribute('identity');
        $user = $identity ? $identity->getOriginalData() : null;

        $productId = $data['product_id'] ?? null;
        $type = $data['type'] ?? 'compra';

        if (!$productId) {
            $this->Flash->error(__('Seleccione un producto.'));
            return $this->redirect(['action' => 'catalog']);
        }

        $product = $this->Products->get($productId);

        $customerName = $user ? $user->username : 'Cliente';
        $customerPhone = '';

        if ($user && !empty($user->client_id)) {
            $client = $this->fetchTable('Clients')->get($user->client_id);
            $customerName = $client->full_name;
            $customerPhone = $client->phone ?? '';
        }

        $ordersTable = $this->fetchTable('Orders');
        $order = $ordersTable->newEntity([
            'product_id' => $productId,
            'quantity' => 1,
            'type' => 'local',
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'total' => $product->price,
            'payment_method' => $type === 'credito' ? 'Crédito' : 'Efectivo',
            'status' => 'pendiente',
            'order_group_id' => uniqid('SOL-'),
        ]);

        if ($ordersTable->save($order)) {
            $this->Flash->success(__('Solicitud enviada. Esperá la confirmación.'));
        } else {
            $this->Flash->error(__('No se pudo enviar la solicitud.'));
        }

        return $this->redirect(['action' => 'catalog']);
    }

    public function index()
    {
        $query = $this->Products->find()
            ->contain(['ProductIngredients'])
            ->orderBy(['Products.id' => 'DESC']);

        $products = $this->paginate($query);
        $this->set(compact('products'));
    }

    private function handleUpload(array $data): array
    {
        $image = $this->request->getData('image_file');
        if (!$image || $image->getError() !== UPLOAD_ERR_OK) {
            return $data;
        }

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo((string)$image->getClientFilename(), PATHINFO_EXTENSION));
        if (!in_array($extension, $allowed, true)) {
            $this->Flash->error(__('Formato de imagen no permitido. Usa JPG, PNG, GIF o WEBP.'));
            return $data;
        }

        $base = Text::slug((string)($data['name'] ?? 'product'));
        if ($base === '') {
            $base = 'product';
        }
        $filename = $base . '-' . time() . '.' . $extension;
        $targetDir = WWW_ROOT . 'img' . DS . 'products';
        $targetPath = $targetDir . DS . $filename;

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        try {
            $image->moveTo($targetPath);
            $data['image'] = $filename;
        } catch (\Exception $e) {
            $this->Flash->error(__('No se pudo guardar la imagen. Verifica los permisos de la carpeta.'));
        }

        return $data;
    }

    public function add()
    {
        $product = $this->Products->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->handleUpload($this->request->getData());

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
            $data = $this->handleUpload($this->request->getData());

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
