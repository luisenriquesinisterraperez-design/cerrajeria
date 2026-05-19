<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * DailyClosures Controller
 *
 * @property \App\Model\Table\DailyClosuresTable $DailyClosures
 */
class DailyClosuresController extends AppController
{
    public function index()
    {
        $query = $this->DailyClosures->find()
            ->orderBy(['DailyClosures.created' => 'DESC']);
            
        $dailyClosures = $this->paginate($query);

        $this->set(compact('dailyClosures'));
    }

    public function add()
    {
        $dailyClosure = $this->DailyClosures->newEmptyEntity();
        
        $ordersTable = $this->fetchTable('Orders');
        $expensesTable = $this->fetchTable('Expenses');
        $paymentsTable = $this->fetchTable('AccountPayments');

        $selectedDate = $this->request->getData('date') ?? date('Y-m-d');

        // 1. Ventas que NO son crédito (Dinero que entró hoy)
        $sales = $ordersTable->find()
            ->where(['DATE(created)' => $selectedDate, 'payment_method !=' => 'Crédito'])
            ->select(['total' => 'SUM(total)'])
            ->first();
        
        // 2. Abonos que se hicieron hoy (Dinero que entró hoy por deudas viejas)
        $abonos = $paymentsTable->find()
            ->where(['DATE(created)' => $selectedDate])
            ->select(['total' => 'SUM(amount)'])
            ->first();

        // 3. Gastos del día (Dinero que salió hoy)
        $expenses = $expensesTable->find()
            ->where(['date' => $selectedDate])
            ->select(['total' => 'SUM(amount)'])
            ->first();

        $totalSales = (float)($sales->total ?? 0);
        $totalAbonos = (float)($abonos->total ?? 0);
        $totalExpenses = (float)($expenses->total ?? 0);

        // El ingreso REAL esperado es: (Ventas Directas + Abonos) - Gastos
        $netExpectedIncome = ($totalSales + $totalAbonos) - $totalExpenses;

        if ($this->request->is('post')) {
            $dailyClosure = $this->DailyClosures->patchEntity($dailyClosure, $this->request->getData());
            $dailyClosure->difference = $dailyClosure->actual_amount - $dailyClosure->expected_amount;

            if ($this->DailyClosures->save($dailyClosure)) {
                $this->Flash->success(__('El cierre de caja ha sido guardado.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar el cierre de caja.'));
        }

        $this->set(compact('dailyClosure', 'totalSales', 'totalAbonos', 'totalExpenses', 'selectedDate', 'netExpectedIncome'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $dailyClosure = $this->DailyClosures->get($id);
        $closureDate = $dailyClosure->date ?? $dailyClosure->created;
        if ($this->DailyClosures->delete($dailyClosure)) {
            $identity = $this->request->getAttribute('identity');
            $user = $identity ? $identity->getOriginalData() : null;
            $this->logAudit(
                $user ? $user->id : 1,
                "ELIMINACIÓN: El usuario " . ($user ? $user->username : 'Sistema') . " eliminó el cierre de caja del {$closureDate}"
            );
            $this->Flash->success(__('El registro de cierre ha sido eliminado.'));
        } else {
            $this->Flash->error(__('No se pudo eliminar el registro.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
