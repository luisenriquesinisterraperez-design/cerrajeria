<?php
declare(strict_types=1);

namespace App\Controller;

class ClientsController extends AppController
{
    public function index()
    {
        $query = $this->Clients->find()->orderBy(['created' => 'DESC']);
        $clients = $this->paginate($query);
        $this->set(compact('clients'));
    }

    public function add()
    {
        $client = $this->Clients->newEmptyEntity();
        if ($this->request->is('post')) {
            $client = $this->Clients->patchEntity($client, $this->request->getData());
            if ($this->Clients->save($client)) {
                $this->Flash->success(__('Cliente registrado correctamente.'));
            } else {
                $this->Flash->error(__('Error al registrar el cliente.'));
            }
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null)
    {
        $client = $this->Clients->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $client = $this->Clients->patchEntity($client, $this->request->getData());
            if ($this->Clients->save($client)) {
                $this->Flash->success(__('Datos del cliente actualizados.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Error al actualizar los datos.'));
        }
        $this->set(compact('client'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $client = $this->Clients->get($id);
        $clientName = $client->name;
        if ($this->Clients->delete($client)) {
            $identity = $this->request->getAttribute('identity');
            $user = $identity ? $identity->getOriginalData() : null;
            $this->logAudit(
                $user ? $user->id : 1,
                "ELIMINACIÓN: El usuario " . ($user ? $user->username : 'Sistema') . " eliminó el cliente \"{$clientName}\""
            );
            $this->Flash->success(__('Cliente eliminado.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
