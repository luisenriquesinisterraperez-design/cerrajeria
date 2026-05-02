<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * AccountsReceivable Controller
 *
 * 
 * @property \App\Model\Table\AccountsReceivableTable $AccountsReceivable
 */
class AccountsReceivableController extends AppController
{
    public function index()
    {
        $query = $this->AccountsReceivable->find()
            ->contain(['Clients', 'Orders', 'AccountPayments'])
            ->orderBy(['AccountsReceivable.status' => 'ASC', 'AccountsReceivable.created' => 'DESC']);
            
        $accountsReceivable = $this->paginate($query);

        $this->set(compact('accountsReceivable'));
    }

    public function add()
    {
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
        $account = $this->AccountsReceivable->get($id, [
            'contain' => ['Clients', 'AccountPayments']
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
        $account = $this->AccountsReceivable->get($id);
        $account->status = 'pagado';
        if ($this->AccountsReceivable->save($account)) {
            $this->Flash->success(__('La deuda ha sido marcada como pagada.'));
        } else {
            $this->Flash->error(__('No se pudo actualizar la deuda.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $accountsReceivable = $this->AccountsReceivable->get($id);
        if ($this->AccountsReceivable->delete($accountsReceivable)) {
            $this->Flash->success(__('La cuenta por cobrar ha sido eliminada.'));
        } else {
            $this->Flash->error(__('No se pudo eliminar la cuenta por cobrar. Por favor, intente de nuevo.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
