<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * AccountsReceivable Controller
 *
 * @property \App\Model\Table\AccountsReceivableTable $AccountsReceivable
 */
class AccountsReceivableController extends AppController
{
    public function index()
    {
        $identity = $this->request->getAttribute('identity');
        $user = $identity ? $identity->getOriginalData() : null;
        $isCliente = ($user && $user->role === 'cliente');

        $query = $this->AccountsReceivable->find()
            ->contain(['Clients', 'AccountPayments'])
            ->orderBy(['AccountsReceivable.status' => 'ASC', 'AccountsReceivable.created' => 'DESC']);

        $where = [];
        if ($isCliente) {
            if ($user->client_id) {
                $where['AccountsReceivable.client_id'] = $user->client_id;
            } else {
                $where['AccountsReceivable.id'] = 0;
            }
        }
        $query->where($where);

        // Calcular resumen para el dashboard
        $summaryQuery = $this->AccountsReceivable->find()
            ->contain(['AccountPayments'])
            ->where($where);
        
        $totalOutstanding = 0;
        $pendingCount = 0;
        foreach ($summaryQuery as $account) {
            if ($account->status !== 'pagado') {
                $totalOutstanding += $account->balance;
                $pendingCount++;
            }
        }

        $accountsReceivable = $this->paginate($query);

        $this->set(compact('accountsReceivable', 'totalOutstanding', 'pendingCount'));
    }

    public function view($id = null)
    {
        $identity = $this->request->getAttribute('identity');
        $user = $identity ? $identity->getOriginalData() : null;
        $isCliente = ($user && $user->role === 'cliente');

        $account = $this->AccountsReceivable->get($id, [
            'contain' => ['Clients', 'Orders.Products', 'AccountPayments'],
        ]);

        if ($isCliente && $account->client_id !== $user->client_id) {
            $this->Flash->error(__('No tienes permiso para ver esta cuenta.'));

            return $this->redirect(['action' => 'index']);
        }

        if ($this->request->is('post') && !$isCliente) {
            $data = $this->request->getData();
            $ordersTable = $this->fetchTable('Orders');
            $newOrder = $ordersTable->newEmptyEntity();

            $orderData = [
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'],
                'accounts_receivable_id' => $account->id,
                'payment_method' => 'Crédito',
                'customer_name' => $account->client->full_name,
                'customer_phone' => $account->client->phone,
                'customer_address' => $account->client->address,
                'type' => 'local',
                'status' => 'entregado',
            ];

            $newOrder = $ordersTable->patchEntity($newOrder, $orderData);
            if ($ordersTable->save($newOrder)) {
                $account->amount += $newOrder->total;
                if ($account->status === 'pagado') {
                    $account->status = 'pendiente';
                }
                $this->AccountsReceivable->save($account);

                $this->Flash->success(__('Producto agregado a la deuda exitosamente.'));

                return $this->redirect(['action' => 'view', $account->id]);
            }
            $this->Flash->error(__('No se pudo agregar el producto. Verifique los datos e intente de nuevo.'));
        }

        $products = $this->fetchTable('Products')->find('list')->all();
        $this->set(compact('account', 'products'));
    }

    public function add()
    {
        $identity = $this->request->getAttribute('identity');
        $user = $identity ? $identity->getOriginalData() : null;
        if ($user && $user->role === 'cliente') {
            $this->Flash->error(__('Acción no permitida.'));

            return $this->redirect(['action' => 'index']);
        }
        $accountsReceivable = $this->AccountsReceivable->newEmptyEntity();
        if ($this->request->is('post')) {
            $accountsReceivable = $this->AccountsReceivable->patchEntity($accountsReceivable, $this->request->getData());
            if ($this->AccountsReceivable->save($accountsReceivable)) {
                $this->Flash->success(__('La cuenta por cobrar ha sido guardada.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar la cuenta por cobrar. Por favor, intente de nuevo.'));
        }
        $clients = $this->AccountsReceivable->Clients->find('list', ['limit' => 200, 'keyField' => 'id', 'valueField' => 'full_name'])->all();
        $this->set(compact('accountsReceivable', 'clients'));
    }

    public function payment($id = null)
    {
        $identity = $this->request->getAttribute('identity');
        $user = $identity ? $identity->getOriginalData() : null;
        if ($user && $user->role === 'cliente') {
            $this->Flash->error(__('Acción no permitida.'));

            return $this->redirect(['action' => 'index']);
        }
        $account = $this->AccountsReceivable->get($id, [
            'contain' => ['Clients', 'AccountPayments'],
        ]);

        $accountPaymentsTable = $this->fetchTable('AccountPayments');
        $payment = $accountPaymentsTable->newEmptyEntity();

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();
            $data['accounts_receivable_id'] = $account->id;

            $payment = $accountPaymentsTable->patchEntity($payment, $data);

            if ($accountPaymentsTable->save($payment)) {
                // Calcular total pagado sumando todos los abonos de esta cuenta
                $query = $accountPaymentsTable->find();
                $totalPaidResult = $query
                    ->where(['accounts_receivable_id' => $account->id])
                    ->select(['total' => $query->func()->sum('amount')])
                    ->disableHydration()
                    ->first();

                $totalPaid = round((float)($totalPaidResult['total'] ?? 0), 2);
                $totalDebt = round((float)$account->amount, 2);

                if ($totalPaid >= $totalDebt) {
                    $account->status = 'pagado';
                } else {
                    $account->status = 'pendiente';
                }

                $this->AccountsReceivable->save($account);

                $this->logAudit(
                    $user ? $user->id : 1,
                    'ABONO: El usuario ' . ($user ? $user->username : 'Sistema') . " registró un abono de $" . number_format((float)$data['amount'], 0) . " para la cuenta de " . $account->client->full_name . ". Saldo restante: $" . number_format($totalDebt - $totalPaid, 0)
                );

                $this->Flash->success(__('Abono registrado con éxito. Saldo actual: $' . number_format($totalDebt - $totalPaid, 0)));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo registrar el abono. Por favor, verifique los datos.'));
        }

        $this->set(compact('account', 'payment'));
    }

    public function markAsPaid($id = null)
    {
        $this->request->allowMethod(['post', 'put']);
        $identity = $this->request->getAttribute('identity');
        $user = $identity ? $identity->getOriginalData() : null;
        if ($user && $user->role === 'cliente') {
            $this->Flash->error(__('Acción no permitida.'));

            return $this->redirect(['action' => 'index']);
        }
        $account = $this->AccountsReceivable->get($id, contain: ['Clients']);
        $account->status = 'pagado';
        if ($this->AccountsReceivable->save($account)) {
            $this->logAudit(
                $user ? $user->id : 1,
                'PAGO TOTAL: El usuario ' . ($user ? $user->username : 'Sistema') . " marcó como pagada la deuda de " . $account->client->full_name
            );
            $this->Flash->success(__('La deuda ha sido marcada como pagada.'));
        } else {
            $this->Flash->error(__('No se pudo actualizar la deuda.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $identity = $this->request->getAttribute('identity');
        $user = $identity ? $identity->getOriginalData() : null;
        if ($user && $user->role === 'cliente') {
            $this->Flash->error(__('Acción no permitida.'));

            return $this->redirect(['action' => 'index']);
        }
        $accountsReceivable = $this->AccountsReceivable->get($id);
        $arDescription = $accountsReceivable->description ?? "ID #{$id}";

        // Cancelar las órdenes asociadas para restaurar inventario
        $ordersTable = $this->fetchTable('Orders');
        $associatedOrders = $ordersTable->find()
            ->where(['accounts_receivable_id' => $accountsReceivable->id])
            ->all();

        $cancelledCount = 0;
        foreach ($associatedOrders as $order) {
            // Solo cancelar órdenes que ya descontaron inventario (no pendientes)
            if ($order->status !== 'pendiente') {
                $order->status = 'cancelado';
                if ($ordersTable->save($order, ['user' => $user])) {
                    $cancelledCount++;
                }
            }
        }

        if ($this->AccountsReceivable->delete($accountsReceivable)) {
            $this->logAudit(
                $user ? $user->id : 1,
                'ELIMINACIÓN: El usuario ' . ($user ? $user->username : 'Sistema') . " eliminó la cuenta por cobrar \"{$arDescription}\"" . ($cancelledCount ? " y canceló {$cancelledCount} pedido(s) asociado(s)" : ''),
            );

            if ($cancelledCount) {
                $this->Flash->success(__("Cuenta por cobrar eliminada y {$cancelledCount} pedido(s) cancelado(s). Inventario restaurado."));
            } else {
                $this->Flash->success(__('La cuenta por cobrar ha sido eliminada.'));
            }
        } else {
            $this->Flash->error(__('No se pudo eliminar la cuenta por cobrar. Por favor, intente de nuevo.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
