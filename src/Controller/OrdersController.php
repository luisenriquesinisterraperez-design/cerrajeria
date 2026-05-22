<?php
declare(strict_types=1);

namespace App\Controller;

class OrdersController extends AppController
{
    public function index()
    {
        $identity = $this->request->getAttribute('identity');
        $user = $identity ? $identity->getOriginalData() : null;
        $isSuperAdmin = ($user && (!empty($user->is_superadmin) || $user->username === 'admin'));
        $isAdminEmpresa = ($user && $user->role === 'admin_empresa');
        $isAdmin = ($user && ($user->role === 'admin' || $isAdminEmpresa || $isSuperAdmin));
        $isRepartidor = ($user && $user->role === 'repartidor');

        $startDate = $this->request->getQuery('start_date');
        $endDate = $this->request->getQuery('end_date');

        $query = $this->Orders->find()
            ->contain(['Products', 'DeliveryDrivers', 'OrderLogs'])
            ->where(['Orders.payment_method !=' => 'Crédito'])
            ->andWhere(['Orders.status !=' => 'cancelado']) // Exclude cancelled orders from default view
            ->orderBy([
                'Orders.created' => 'DESC',
                'Orders.id' => 'DESC'
            ]);

        if ($startDate) $query->where(['Orders.created >=' => $startDate . ' 00:00:00']);
        if ($endDate) $query->where(['Orders.created <=' => $endDate . ' 23:59:59']);
            
        if ($isRepartidor) {
            $query->where(['Orders.delivery_driver_id' => $user->delivery_driver_id]);
            $this->paginate['limit'] = 5;

            $qEarned = $this->Orders->find();
            $driverEarnings = $qEarned->where(['Orders.delivery_driver_id' => $user->delivery_driver_id, 'Orders.status' => 'entregado'])
                ->select(['total' => $qEarned->func()->sum('shipping_cost')])
                ->disableHydration()
                ->first()['total'] ?? 0;
            
            $this->set('driverEarnings', (float)$driverEarnings);
        } elseif (!$isAdmin) {
            $this->paginate['limit'] = 10;
        } else {
            $this->paginate['limit'] = 50;
        }
        $orders = $this->paginate($query);
        $products = $this->Orders->Products->find('list', limit: 200)->all();
        
        $driversTable = $this->fetchTable('DeliveryDrivers');
        $deliveryDrivers = $driversTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'full_name',
            'limit' => 200
        ])->all();

        $clients = $this->fetchTable('Clients')->find()->all();

        $this->set(compact('orders', 'products', 'deliveryDrivers', 'clients', 'isAdmin', 'startDate', 'endDate'));
    }

    public function add()
    {
        $this->request->allowMethod(['post']);
        $data = $this->request->getData();
        
        $items = $data['items'] ?? [];
        if (empty($items)) {
            $this->Flash->error(__('Debe agregar al menos un producto al pedido.'));
            return $this->redirect(['action' => 'index']);
        }

        $orderGroupId = uniqid('ORD-');
        $successCount = 0;
        $firstOrder = null;
        $totalOrderAmount = 0;
        
        $commonData = [
            'order_group_id' => $orderGroupId,
            'type' => $data['type'],
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'customer_address' => $data['customer_address'] ?? '',
            'payment_method' => $data['payment_method'],
            'delivery_driver_id' => $data['delivery_driver_id'] ?? null,
            'status' => 'recibido'
        ];

        foreach ($items as $index => $item) {
            $order = $this->Orders->newEmptyEntity();
            $orderData = array_merge($commonData, [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'shipping_cost' => ($index === 0) ? (($data['shipping_cost'] ?? '') !== '' ? $data['shipping_cost'] : 0) : 0
            ]);

            $order = $this->Orders->patchEntity($order, $orderData);
            if ($this->Orders->save($order)) {
                $successCount++;
                if (!$firstOrder) $firstOrder = $order;
                $totalOrderAmount += $order->total;
            } else {
                \Cake\Log\Log::error("ADD ORDER ITEM FAILED. Errors: " . print_r($order->getErrors(), true));
            }
        }

        if ($successCount > 0) {
            if ($data['payment_method'] === 'Crédito') {
                $clientsTable = $this->fetchTable('Clients');
                $accountsReceivableTable = $this->fetchTable('AccountsReceivable');

                $client = $clientsTable->find()->where(['phone' => $data['customer_phone']])->first();
                if (!$client) {
                    $client = $clientsTable->newEntity([
                        'full_name' => $data['customer_name'],
                        'phone' => $data['customer_phone'],
                        'address' => $data['customer_address'] ?? ''
                    ]);
                    $clientsTable->save($client);
                }

                $account = $accountsReceivableTable->newEntity([
                    'client_id' => $client->id,
                    'order_id' => $firstOrder->id,
                    'amount' => $totalOrderAmount,
                    'description' => 'Pedido #' . $orderGroupId . ' (Consolidado)',
                    'status' => 'pendiente'
                ]);
                if ($accountsReceivableTable->save($account)) {
                    $this->Orders->updateAll(
                        ['accounts_receivable_id' => $account->id],
                        ['order_group_id' => $orderGroupId]
                    );
                }
                $this->Flash->success(__('Venta registrada ({0} productos) y cargada a Crédito.', $successCount));
            } else {
                $this->Flash->success(__('Venta registrada con éxito ({0} productos).', $successCount));
            }
        } else {
            $this->Flash->error(__('No se pudo registrar la venta.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null)
    {
        $order = $this->Orders->get($id, contain: ['Products', 'OrderLogs' => ['Users']]);
        $originalData = $order->toArray();
        $originalProductName = $order->product->name;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $order = $this->Orders->patchEntity($order, $data);
            
            // Detectar si el método de pago cambió a Crédito ANTES de guardar
            $isChangingToCredit = $order->isDirty('payment_method') && $order->payment_method === 'Crédito';

            $fieldsToTrack = [
                'product_id' => 'Producto',
                'quantity' => 'Cantidad',
                'type' => 'Tipo',
                'shipping_cost' => 'Envío',
                'customer_name' => 'Cliente',
                'payment_method' => 'Método Pago',
                'status' => 'Estado'
            ];

            $changes = [];
            foreach ($fieldsToTrack as $field => $label) {
                if ($order->isDirty($field)) {
                    $oldValue = $originalData[$field] ?? 'N/A';
                    $newValue = $order->get($field);
                    
                    if ($field === 'product_id') {
                        $oldValue = $originalProductName;
                        $newValue = $this->Orders->Products->get($newValue)->name;
                    }
                    
                    if ($oldValue != $newValue) {
                        $changes[] = "{$label}: de '{$oldValue}' a '{$newValue}'";
                    }
                }
            }

            if ($this->Orders->save($order)) {
                // Si el método de pago cambió a Crédito, crear AR
                if ($isChangingToCredit) {
                    $clientsTable = $this->fetchTable('Clients');
                    $accountsReceivableTable = $this->fetchTable('AccountsReceivable');

                    $client = $clientsTable->find()->where(['phone' => $order->customer_phone])->first();
                    if (!$client) {
                        $client = $clientsTable->newEntity([
                            'full_name' => $order->customer_name,
                            'phone' => $order->customer_phone,
                            'address' => $order->customer_address ?? ''
                        ]);
                        $clientsTable->save($client);
                    }

                    $account = $accountsReceivableTable->newEntity([
                        'client_id' => $client->id,
                        'order_id' => $order->id,
                        'amount' => $order->total,
                        'description' => 'Pedido #' . ($order->order_group_id ?: $order->id) . ' (Editado)',
                        'status' => 'pendiente'
                    ]);
                    if ($accountsReceivableTable->save($account)) {
                        $order->accounts_receivable_id = $account->id;
                        $this->Orders->save($order);
                    }
                }

                if (!empty($changes)) {
                    $logsTable = $this->fetchTable('OrderLogs');
                    $identity = $this->request->getAttribute('identity');
                    $user = $identity ? $identity->getOriginalData() : null;
                    
                    $log = $logsTable->newEntity([
                        'order_id' => $order->id,
                        'user_id' => $user ? $user->id : 1,
                        'modification_details' => "Modificado por " . ($user ? $user->username : 'Sistema') . ". Cambios: " . implode(", ", $changes)
                    ]);
                    $logsTable->save($log);
                }

                $this->Flash->success(__('El pedido ha sido actualizado.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo actualizar el pedido.'));
        }
        $products = $this->Orders->Products->find('list', limit: 200)->all();
        $deliveryDrivers = $this->Orders->DeliveryDrivers->find('list', limit: 200, keyField: 'id', valueField: 'full_name')->all();
        $this->set(compact('order', 'products', 'deliveryDrivers'));
    }

    public function updateStatusGroup($groupId = null, $newStatus = null)
    {
        $this->request->allowMethod(['post', 'put']);
        $orders = $this->Orders->find()->where(['order_group_id' => $groupId])->all();

        if ($orders->isEmpty()) {
            if (str_starts_with($groupId, 'SINGLE-')) {
                $id = str_replace('SINGLE-', '', $groupId);
                $orders = [$this->Orders->get($id)];
            }
        }

        $identity = $this->request->getAttribute('identity');
        $user = $identity ? $identity->getOriginalData() : null;

        $success = true;
        foreach ($orders as $order) {
            $order->status = $newStatus;
            if ($newStatus === 'entregado') {
                $order->delivered_at = new \Cake\I18n\DateTime();
            }
            if (!$this->Orders->save($order, ['user' => $user])) $success = false;
        }

        if ($success) {
            $this->Flash->success(__('Estado de la venta actualizado a {0}.', strtoupper($newStatus)));
        } else {
            $this->Flash->error(__('Hubo problemas al actualizar algunos items.'));
        }

        return $this->redirect($this->referer(['action' => 'index']));
    }

    public function cancel($id = null)
    {
        $this->request->allowMethod(['post', 'put']);
        
        $identity = $this->request->getAttribute('identity');
        $user = $identity ? $identity->getOriginalData() : null;
        $isAdmin = ($user && ($user->role === 'admin' || !empty($user->is_superadmin) || $user->role === 'admin_empresa'));
        $isStaff = ($user && $user->role === 'staff');

        if (!$isAdmin && !$isStaff) {
            $this->Flash->error(__('No tienes permiso para cancelar pedidos.'));
            return $this->redirect(['action' => 'index']);
        }

        $order = $this->Orders->get($id);

        if ($order->status === 'cancelado') {
            $this->Flash->warning(__('Este pedido ya ha sido cancelado.'));
            return $this->redirect(['action' => 'index']);
        }

        // Update status to cancelled
        $order->status = 'cancelado';
        
        // Create a log entry for cancellation
        $logsTable = $this->fetchTable('OrderLogs');
        $log = $logsTable->newEntity([
            'order_id' => $order->id,
            'user_id' => $user ? $user->id : null,
            'modification_details' => "Pedido cancelado por " . ($user ? $user->username : 'Sistema') . ".",
            'created' => new \Cake\I18n\DateTime()
        ]);

        if ($this->Orders->save($order, ['user' => $user]) && $logsTable->save($log)) {
            $this->Flash->success(__('El pedido ha sido cancelado y el inventario restaurado.'));
        } else {
            $this->Flash->error(__('No se pudo cancelar el pedido.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function printTicketGroup($groupId = null)
    {
        $query = $this->Orders->find()->contain(['Products'])->where(['order_group_id' => $groupId]);
        $orders = $query->toArray();

        if (empty($orders) && str_starts_with($groupId, 'SINGLE-')) {
            $id = str_replace('SINGLE-', '', $groupId);
            $order = $this->Orders->get($id, contain: ['Products']);
            $orders = [$order];
        }

        $this->viewBuilder()->setLayout('ajax');
        $this->set(compact('orders'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $order = $this->Orders->get($id, contain: ['Products']);
        
        $identity = $this->request->getAttribute('identity');
        $user = $identity ? $identity->getOriginalData() : null;
        $orderId = $order->id;
        $details = "ELIMINACIÓN DEFINITIVA: El usuario " . ($user ? $user->username : 'Sistema') . " borró el pedido #" . $orderId . " de " . $order->product->name . " (Cliente: " . $order->customer_name . ")";

        if ($this->Orders->delete($order)) {
            $this->logAudit($user ? $user->id : 1, $details, $orderId);
            $this->Flash->success(__('Pedido eliminado exitosamente y huella registrada.'));
        } else {
            $this->Flash->error(__('No se pudo eliminar el pedido. Intente de nuevo.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function printTicket($id = null)
    {
        $order = $this->Orders->get($id, contain: ['Products']);
        $this->viewBuilder()->setLayout('ajax');
        $this->set(compact('order'));
    }
}
