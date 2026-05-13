<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['login', 'logout']);
    }

    public function index()
    {
        $query = $this->Users->find();
        $users = $this->paginate($query);
        $this->set(compact('users'));
    }

    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            // Si el rol es admin (Global), lo marcamos como superadmin
            if (($data['role'] ?? '') === 'admin') {
                $data['is_superadmin'] = 1;
            } else {
                $data['is_superadmin'] = 0;
            }
            
            $user = $this->Users->patchEntity($user, $data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('El usuario ha sido creado.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo crear el usuario.'));
        }
        
        $deliveryDrivers = $this->fetchTable('DeliveryDrivers')->find('list', ['keyField' => 'id', 'valueField' => 'name'])->all();
        
        $this->set(compact('user', 'deliveryDrivers'));
    }

    public function edit($id = null)
    {
        $user = $this->Users->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            
            if (($data['role'] ?? '') === 'admin') {
                $data['is_superadmin'] = 1;
            } else {
                $identity = $this->request->getAttribute('identity');
                if ($identity && $identity->getOriginalData()->is_superadmin) {
                    $data['is_superadmin'] = 0;
                }
            }

            $user = $this->Users->patchEntity($user, $data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('El perfil ha sido actualizado.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo actualizar el perfil.'));
        }

        $deliveryDrivers = $this->fetchTable('DeliveryDrivers')->find('list', ['keyField' => 'id', 'valueField' => 'name'])->all();

        $this->set(compact('user', 'deliveryDrivers'));
    }

    public function login()
    {
        $this->request->allowMethod(['get', 'post']);

        if ($this->request->is('post')) {
            $username = $this->request->getData('username');
            $user = $this->Users->find()->where(['username' => $username])->first();

            // 1. Verificar si el usuario está bloqueado
            if ($user && $user->lockout_time && $user->lockout_time > new \Cake\I18n\DateTime()) {
                $timeLeft = $user->lockout_time->diff(new \Cake\I18n\DateTime())->format('%i');
                $this->Flash->error(__('Cuenta bloqueada temporalmente. Intente de nuevo en {0} minutos.', $timeLeft + 1));
                return;
            }

            $result = $this->Authentication->getResult();
            if ($result && $result->isValid()) {
                \Cake\Log\Log::debug('Login exitoso para: ' . $username);
                // 2. Login exitoso -> Resetear intentos fallidos
                if ($user) {
                    $user->failed_logins = 0;
                    $user->lockout_time = null;
                    $this->Users->save($user);
                }

                $target = $this->Authentication->getLoginRedirect() ?? ['controller' => 'Dashboard', 'action' => 'index'];
                return $this->redirect($target);
            }

            \Cake\Log\Log::debug('Login fallido para: ' . $username . '. Status: ' . ($result ? $result->getStatus() : 'null'));

            // 3. Login fallido -> Incrementar contador
            if ($user) {
                $user->failed_logins += 1;
                
                if ($user->failed_logins >= 5) {
                    $user->lockout_time = (new \Cake\I18n\DateTime())->addMinutes(15);
                    $this->Flash->error(__('Demasiados intentos fallidos. Su cuenta ha sido bloqueada por 15 minutos.'));
                } else {
                    $intentosRestantes = 5 - $user->failed_logins;
                    $this->Flash->error(__('Usuario o contraseña incorrectos. Intentos restantes: {0}', $intentosRestantes));
                }
                $this->Users->save($user);
            } else {
                $this->Flash->error(__('Usuario o contraseña incorrectos'));
            }
        }
    }

    public function logout()
    {
        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {
            $this->Authentication->logout();
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('Usuario eliminado.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
