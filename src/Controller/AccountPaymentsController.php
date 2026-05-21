<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * AccountPayments Controller
 *
 * @property \App\Model\Table\AccountPaymentsTable $AccountPayments
 */
class AccountPaymentsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->AccountPayments->find();
        $accountPayments = $this->paginate($query);

        $this->set(compact('accountPayments'));
    }

    /**
     * View method
     *
     * @param string|null $id Account Payment id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $accountPayment = $this->AccountPayments->get($id, contain: []);
        $this->set(compact('accountPayment'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $accountPayment = $this->AccountPayments->newEmptyEntity();
        if ($this->request->is('post')) {
            $accountPayment = $this->AccountPayments->patchEntity($accountPayment, $this->request->getData());
            if ($this->AccountPayments->save($accountPayment)) {
                $this->Flash->success(__('The account payment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The account payment could not be saved. Please, try again.'));
        }
        $this->set(compact('accountPayment'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Account Payment id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $accountPayment = $this->AccountPayments->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $accountPayment = $this->AccountPayments->patchEntity($accountPayment, $this->request->getData());
            if ($this->AccountPayments->save($accountPayment)) {
                $this->Flash->success(__('The account payment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The account payment could not be saved. Please, try again.'));
        }
        $this->set(compact('accountPayment'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Account Payment id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $accountPayment = $this->AccountPayments->get($id);
        $arId = $accountPayment->accounts_receivable_id;

        if ($this->AccountPayments->delete($accountPayment)) {
            // Sincronizar estado de la cuenta por cobrar
            $arTable = $this->fetchTable('AccountsReceivable');
            $account = $arTable->get($arId, contain: ['AccountPayments']);
            
            // Si el saldo es mayor a 0 y estaba pagado, volver a pendiente
            if ($account->balance > 0 && $account->status === 'pagado') {
                $account->status = 'pendiente';
                $arTable->save($account);
            }

            $this->Flash->success(__('El abono ha sido eliminado y el saldo actualizado.'));
        } else {
            $this->Flash->error(__('No se pudo eliminar el abono. Por favor, intente de nuevo.'));
        }

        return $this->redirect($this->referer(['controller' => 'AccountsReceivable', 'action' => 'index']));
    }
}
