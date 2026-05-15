<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;

class AppController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $identity = $this->request->getAttribute('identity');
        $user = $identity ? $identity->getOriginalData() : null;
        
        // Detección de roles simplificada
        $isSuperAdmin = ($user && (!empty($user->is_superadmin) || $user->username === 'admin' || $user->role === 'admin'));
        $isAdmin = $isSuperAdmin; // En modo simple, Admin es lo mismo que SuperAdmin
        $isRepartidor = ($user && !empty($user->role) && $user->role === 'repartidor');
        $isStaff = ($user && !empty($user->role) && $user->role === 'staff');
        $isCliente = ($user && !empty($user->role) && $user->role === 'cliente');
        
        if ($user) {
            $controller = $this->request->getParam('controller');
            $action = $this->request->getParam('action');

            // 1. RESTRICCIONES DE ESTRUCTURA (Solo Admin)
            $adminControllers = ['Users', 'OrderLogs'];
            if (in_array($controller, $adminControllers) && !$isAdmin) {
                if ($controller === 'Users' && in_array($action, ['login', 'logout', 'profile'])) {
                    // Permitido
                } else {
                    $this->Flash->error(__('Acceso Denegado: Solo el Administrador puede gestionar estos módulos.'));
                    return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
                }
            }

            // 2. RESTRICCIONES DE ELIMINACIÓN (Permitir a Staff para que sea auditado)
            if ($action === 'delete' && (!$isAdmin && !$isStaff)) {
                $this->Flash->error(__('Acceso Denegado: No tienes permiso para eliminar registros.'));
                return $this->redirect($this->referer(['controller' => 'Dashboard', 'action' => 'index']));
            }

            // 3. RESTRICCIONES DE MÓDULOS PARA STAFF / REPARTIDOR / CLIENTE
            if (!$isAdmin) {
                if ($isStaff || $isRepartidor || $isCliente) {
                    $allowed = [];
                    if ($isRepartidor) {
                        $allowed = ['Dashboard', 'Orders'];
                    } elseif ($isStaff) {
                        $allowed = ['Dashboard', 'Orders', 'Products', 'Ingredients', 'Clients', 'DeliveryDrivers', 'DailyClosures', 'AccountsReceivable', 'ProductIngredients', 'InventoryAdjustments'];
                    } elseif ($isCliente) {
                        $allowed = ['Dashboard', 'AccountsReceivable'];
                    }
                    
                    if (!in_array($controller, $allowed)) {
                        if (!($controller === 'Users' && in_array($action, ['login', 'logout', 'profile']))) {
                            $this->Flash->error(__('Módulo restringido para su perfil.'));
                            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
                        }
                    }
                }
            }
        }

        $this->set(compact('user', 'isAdmin', 'isSuperAdmin', 'isRepartidor', 'isStaff', 'isCliente'));
        $this->set('isAdminEmpresa', $isAdmin); // Compatibilidad con vistas antiguas
        $this->set('authUser', $user); // Compatibilidad
    }
}
