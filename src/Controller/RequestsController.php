<?php
declare(strict_types=1);

namespace App\Controller;

class RequestsController extends AppController
{
    public function index()
    {
        $query = $this->fetchTable('Orders')->find()
            ->contain(['Products'])
            ->where(['Orders.status' => 'pendiente'])
            ->orderBy(['Orders.created' => 'DESC']);

        $requests = $this->paginate($query);
        $this->set(compact('requests'));
    }

    public function approve($id = null)
    {
        $this->request->allowMethod(['post', 'put']);

        $order = $this->fetchTable('Orders')->get($id, contain: ['Products']);

        if ($order->status !== 'pendiente') {
            $this->Flash->warning(__('Esta solicitud ya fue procesada.'));
            return $this->redirect(['action' => 'index']);
        }

        $order->status = 'recibido';

        if ($this->fetchTable('Orders')->save($order)) {
            if ($order->payment_method === 'Crédito') {
                $clientsTable = $this->fetchTable('Clients');
                $client = $clientsTable->find()->where(['phone' => $order->customer_phone])->first();
                if (!$client) {
                    $client = $clientsTable->newEntity([
                        'full_name' => $order->customer_name,
                        'phone' => $order->customer_phone,
                    ]);
                    $clientsTable->save($client);
                }

                $accountsReceivableTable = $this->fetchTable('AccountsReceivable');

                // Buscar cuenta abierta del cliente para acumular
                $existingAccount = $accountsReceivableTable->find()
                    ->where(['client_id' => $client->id, 'status' => 'pendiente'])
                    ->first();

                if ($existingAccount) {
                    // Acumular al saldo existente
                    $existingAccount->amount = (float)$existingAccount->amount + (float)$order->total;
                    $existingAccount->description .= ' + ' . ($order->hasValue('product') ? $order->product->name : 'Producto #' . $order->product_id);
                    $accountsReceivableTable->save($existingAccount);

                    // Vincular la nueva orden a la cuenta existente
                    $order->accounts_receivable_id = $existingAccount->id;
                    $this->fetchTable('Orders')->save($order);
                } else {
                    // Crear nueva cuenta
                    $account = $accountsReceivableTable->newEntity([
                        'client_id' => $client->id,
                        'order_id' => $order->id,
                        'amount' => $order->total,
                        'description' => 'Solicitud de catálogo: ' . ($order->hasValue('product') ? $order->product->name : 'Producto #' . $order->product_id),
                        'status' => 'pendiente',
                    ]);
                    $accountsReceivableTable->save($account);

                    // Vincular la orden a la nueva cuenta
                    $order->accounts_receivable_id = $account->id;
                    $this->fetchTable('Orders')->save($order);
                }

                $this->Flash->success(__('Solicitud aprobada y acumulada a Cuentas por Cobrar.'));
            } else {
                $this->Flash->success(__('Solicitud aprobada. Ventas actualizadas.'));
            }
        } else {
            $this->Flash->error(__('No se pudo aprobar la solicitud.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function reject($id = null)
    {
        $this->request->allowMethod(['post', 'put']);

        $order = $this->fetchTable('Orders')->get($id);

        if ($order->status !== 'pendiente') {
            $this->Flash->warning(__('Esta solicitud ya fue procesada.'));
            return $this->redirect(['action' => 'index']);
        }

        $order->status = 'cancelado';

        if ($this->fetchTable('Orders')->save($order)) {
            $this->Flash->success(__('Solicitud rechazada.'));
        } else {
            $this->Flash->error(__('No se pudo rechazar la solicitud.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $order = $this->fetchTable('Orders')->get($id, contain: ['Products']);

        if ($order->status !== 'pendiente') {
            $this->Flash->warning(__('Solo se pueden eliminar solicitudes pendientes.'));
            return $this->redirect(['action' => 'index']);
        }

        if ($this->fetchTable('Orders')->delete($order)) {
            $this->Flash->success(__('Solicitud eliminada.'));
        } else {
            $this->Flash->error(__('No se pudo eliminar.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
