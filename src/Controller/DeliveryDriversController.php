<?php
declare(strict_types=1);

namespace App\Controller;

class DeliveryDriversController extends AppController
{
    public function index()
    {
        $deliveryDrivers = $this->paginate($this->DeliveryDrivers);
        $this->set(compact('deliveryDrivers'));
    }

    public function add()
    {
        $deliveryDriver = $this->DeliveryDrivers->newEmptyEntity();
        if ($this->request->is('post')) {
            $deliveryDriver = $this->DeliveryDrivers->patchEntity($deliveryDriver, $this->request->getData());
            if ($this->DeliveryDrivers->save($deliveryDriver)) {
                $this->Flash->success(__('Repartidor registrado.'));
            } else {
                $this->Flash->error(__('No se pudo registrar al repartidor.'));
            }
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null)
    {
        $deliveryDriver = $this->DeliveryDrivers->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $deliveryDriver = $this->DeliveryDrivers->patchEntity($deliveryDriver, $this->request->getData());
            if ($this->DeliveryDrivers->save($deliveryDriver)) {
                $this->Flash->success(__('Datos actualizados.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo actualizar los datos.'));
        }
        $this->set(compact('deliveryDriver'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $deliveryDriver = $this->DeliveryDrivers->get($id);
        $driverName = $deliveryDriver->name . ' ' . ($deliveryDriver->last_name ?? '');
        if ($this->DeliveryDrivers->delete($deliveryDriver)) {
            $identity = $this->request->getAttribute('identity');
            $user = $identity ? $identity->getOriginalData() : null;
            $this->logAudit(
                $user ? $user->id : 1,
                "ELIMINACIÓN: El usuario " . ($user ? $user->username : 'Sistema') . " eliminó al repartidor \"{$driverName}\""
            );
            $this->Flash->success(__('Repartidor eliminado.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
