<?php
declare(strict_types=1);

namespace App\Controller;

class DashboardController extends AppController
{
    public function index()
    {
        $startDate = $this->request->getQuery('start_date');
        $endDate = $this->request->getQuery('end_date');
        if (!$startDate && !$endDate) {
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
        }

        $identity = $this->request->getAttribute('identity');
        $user = $identity ? $identity->getOriginalData() : null;
        $isSuperAdmin = ($user && (!empty($user->is_superadmin) || $user->username === 'admin'));
        $isAdminEmpresa = ($user && $user->role === 'admin_empresa');
        $isAdmin = ($user && ($user->role === 'admin' || $isAdminEmpresa || $isSuperAdmin));
        $isRepartidor = ($user && $user->role === 'repartidor');
        $isCliente = ($user && $user->role === 'cliente');

        $ordersTable = $this->fetchTable('Orders');

        if ($isRepartidor) {
            $driverId = $user->delivery_driver_id;

            // 1. Pedidos entregados en el período
            $qDelivered = $ordersTable->find()->where(['delivery_driver_id' => $driverId, 'status' => 'entregado']);
            if ($startDate) {
                $qDelivered->where(['Orders.created >=' => $startDate . ' 00:00:00']);
            }
            if ($endDate) {
                $qDelivered->where(['Orders.created <=' => $endDate . ' 23:59:59']);
            }
            $deliveredInPeriod = $qDelivered->count();

            // 2. Ganancia por envíos en el período
            $qEarned = $ordersTable->find()->where(['delivery_driver_id' => $driverId, 'status' => 'entregado']);
            if ($startDate) {
                $qEarned->where(['Orders.created >=' => $startDate . ' 00:00:00']);
            }
            if ($endDate) {
                $qEarned->where(['Orders.created <=' => $endDate . ' 23:59:59']);
            }

            $totalEarnedResult = $qEarned->select(['total' => $qEarned->func()->sum('shipping_cost')])
                ->disableHydration()->first();
            $totalEarned = (float)($totalEarnedResult['total'] ?? 0);

            // 3. Pendientes por entregar (tiempo real)
            $pendingDeliveries = $ordersTable->find()->where([
                'delivery_driver_id' => $driverId,
                'status NOT IN' => ['entregado', 'cancelado'], // Exclude delivered and cancelled orders
            ])->count();

            $this->set(compact('deliveredInPeriod', 'totalEarned', 'pendingDeliveries', 'isRepartidor', 'startDate', 'endDate'));

            return;
        }

        if ($isCliente) {
            $this->set(compact('isCliente', 'user'));

            return;
        }

        // --- LÓGICA PARA ADMIN / STAFF ---
        $expensesTable = $this->fetchTable('Expenses');
        $paymentsTable = $this->fetchTable('AccountPayments');

        // Consultas base
        $ordersQuery = $ordersTable->find()->contain(['Products.ProductIngredients.Ingredients']);
        if ($startDate) {
            $ordersQuery->where(['Orders.created >=' => $startDate . ' 00:00:00']);
        }
        if ($endDate) {
            $ordersQuery->where(['Orders.created <=' => $endDate . ' 23:59:59']);
        }
        $ordersQuery->andWhere(['Orders.status !=' => 'cancelado']); // Exclude cancelled orders

        // 1. Conteo total de pedidos (Agrupados por order_group_id si existe)
        $totalOrdersQuery = clone $ordersQuery;
        $totalOrders = $totalOrdersQuery->select([
                'count' => $totalOrdersQuery->func()->count('DISTINCT IFNULL(order_group_id, CAST(Orders.id AS CHAR))'),
            ])
            ->disableHydration()
            ->first()['count'] ?? 0;

        // 2. Ingresos Brutos (Ventas NO crédito + Abonos)
        $qVentasBase = $ordersTable->find()->where(['payment_method !=' => 'Crédito']);
        if ($startDate) {
            $qVentasBase->where(['Orders.created >=' => $startDate . ' 00:00:00']);
        }
        if ($endDate) {
            $qVentasBase->where(['Orders.created <=' => $endDate . ' 23:59:59']);
        }
        $qVentasBase->andWhere(['Orders.status !=' => 'cancelado']); // Exclude cancelled orders

        $orderIncomeResult = (clone $qVentasBase)->select(['total_sum' => $qVentasBase->func()->sum('total')])->disableHydration()->first();
        $orderIncome = (float)($orderIncomeResult['total_sum'] ?? 0);

        $qAbonosBase = $paymentsTable->find();
        if ($startDate) {
            $qAbonosBase->where(['AccountPayments.created >=' => $startDate . ' 00:00:00']);
        }
        if ($endDate) {
            $qAbonosBase->where(['AccountPayments.created <=' => $endDate . ' 23:59:59']);
        }
        $paymentIncomeResult = (clone $qAbonosBase)->select(['total_sum' => $qAbonosBase->func()->sum('amount')])->disableHydration()->first();
        $paymentIncome = (float)($paymentIncomeResult['total_sum'] ?? 0);

        $totalIncome = $orderIncome + $paymentIncome;

        // 3. Total de envíos
        $qShipping = $ordersTable->find();
        if ($startDate) {
            $qShipping->where(['Orders.created >=' => $startDate . ' 00:00:00']);
        }
        if ($endDate) {
            $qShipping->where(['Orders.created <=' => $endDate . ' 23:59:59']);
        }
        $qShipping->andWhere(['Orders.status !=' => 'cancelado']); // Exclude cancelled orders
        $totalShippingResult = $qShipping->select(['total_ship' => $qShipping->func()->sum('shipping_cost')])->disableHydration()->first();
        $totalShipping = (float)($totalShippingResult['total_ship'] ?? 0);

        // 4. Costo de Insumos
        $totalCostOfSales = 0;
        foreach ($ordersQuery->all() as $order) {
            if ($order->hasValue('product') && !empty($order->product->product_ingredients)) {
                foreach ($order->product->product_ingredients as $pi) {
                    if (!empty($pi->ingredient)) {
                        $totalCostOfSales += (float)$pi->quantity_required * (float)($pi->ingredient->cost ?? 0) * $order->quantity;
                    }
                }
            }
        }

        // 5. Gastos
        $qExp = $expensesTable->find();
        if ($startDate) {
            $qExp->where(['Expenses.date >=' => $startDate]);
        }
        if ($endDate) {
            $qExp->where(['Expenses.date <=' => $endDate]);
        }
        $totalExpensesResult = $qExp->select(['total_sum' => $qExp->func()->sum('amount')])->disableHydration()->first();
        $totalExpenses = (float)($totalExpensesResult['total_sum'] ?? 0);

        // Utilidad Neta Real
        $netProfit = $totalIncome - $totalShipping - $totalCostOfSales - $totalExpenses;

        // --- DESGLOSE POR MÉTODO DE PAGO ---
        $paymentTotalsMap = [
            'Efectivo' => 0,
            'Nequi' => 0,
            'Daviplata' => 0,
            'Cuenta' => 0,
        ];

        $resVentasQuery = clone $qVentasBase;
        $resVentas = $resVentasQuery->select([
                'payment_method',
                'total_sum' => $resVentasQuery->func()->sum('total'),
            ])
            ->groupBy(['payment_method'])
            ->disableHydration()
            ->all();

        foreach ($resVentas as $r) {
            $m = trim($r['payment_method']);
            if (isset($paymentTotalsMap[$m])) {
                $paymentTotalsMap[$m] += (float)($r['total_sum'] ?? 0);
            } else {
                foreach ($paymentTotalsMap as $key => $val) {
                    if (stripos($m, $key) !== false) {
                        $paymentTotalsMap[$key] += (float)($r['total_sum'] ?? 0);
                        break;
                    }
                }
            }
        }

        $resAbonosQuery = clone $qAbonosBase;
        $resAbonos = $resAbonosQuery->select([
                'payment_method',
                'total_sum' => $resAbonosQuery->func()->sum('amount'),
            ])
            ->groupBy(['payment_method'])
            ->disableHydration()
            ->all();

        foreach ($resAbonos as $r) {
            $m = trim($r['payment_method']);
            if (isset($paymentTotalsMap[$m])) {
                $paymentTotalsMap[$m] += (float)($r['total_sum'] ?? 0);
            } else {
                foreach ($paymentTotalsMap as $key => $val) {
                    if (stripos($m, $key) !== false) {
                        $paymentTotalsMap[$key] += (float)($r['total_sum'] ?? 0);
                        break;
                    }
                }
            }
        }

        $formattedPaymentTotals = [];
        foreach ($paymentTotalsMap as $method => $total) {
            $formattedPaymentTotals[] = (object)['method' => $method, 'total' => $total];
        }

        // Gráfico (Contar transacciones únicas por día)
        $salesByDayQ = $ordersTable->find();
        if ($startDate) {
            $salesByDayQ->where(['Orders.created >=' => $startDate . ' 00:00:00']);
        }
        if ($endDate) {
            $salesByDayQ->where(['Orders.created <=' => $endDate . ' 23:59:59']);
        }
        $salesByDayQ->andWhere(['Orders.status !=' => 'cancelado']); // Exclude cancelled orders

        $salesByDay = $salesByDayQ->select([
                'date' => $salesByDayQ->func()->date_format(['created' => 'identifier', "'%Y-%m-%d'" => 'literal']),
                'total' => $salesByDayQ->func()->sum('total'),
                'count' => $salesByDayQ->func()->count('DISTINCT IFNULL(order_group_id, CAST(id AS CHAR))'),
            ])
            ->groupBy(['date'])
            ->orderBy(['date' => 'ASC'])
            ->disableHydration()
            ->all()
            ->toArray();

        // Ranking Repartidores (Agrupado por transacción real)
        $qR = $this->fetchTable('DeliveryDrivers')->find();
        $driversData = $qR->select([
                'name' => 'DeliveryDrivers.name',
                'last_name' => 'DeliveryDrivers.last_name',
                'orders_count' => $qR->func()->count('DISTINCT IFNULL(Orders.order_group_id, CAST(Orders.id AS CHAR))'),
                'total_shipping' => $qR->func()->sum('Orders.shipping_cost'),
                'total_generated' => $qR->func()->sum('Orders.total'),
            ])
            ->leftJoinWith('Orders', function ($q) use ($startDate, $endDate) {
                if ($startDate) {
                    $q->where(['Orders.created >=' => $startDate . ' 00:00:00']);
                }
                if ($endDate) {
                    $q->where(['Orders.created <=' => $endDate . ' 23:59:59']);
                }
                $q->andWhere(['Orders.status !=' => 'cancelado']); // Exclude cancelled orders

                return $q;
            })->groupBy(['DeliveryDrivers.id'])->disableHydration()->all()->toArray();

        $qLocStats = $ordersTable->find()->where(['type' => 'local']);
        if ($startDate) {
            $qLocStats->where(['Orders.created >=' => $startDate . ' 00:00:00']);
        }
        if ($endDate) {
            $qLocStats->where(['Orders.created <=' => $endDate . ' 23:59:59']);
        }
        $localStats = $qLocStats->select([
                'orders_count' => $qLocStats->func()->count('DISTINCT IFNULL(order_group_id, CAST(id AS CHAR))'),
                'total_generated' => $qLocStats->func()->sum('total'),
            ])
            ->disableHydration()
            ->first();

        $ranking = [];
        foreach ($driversData as $d) {
            $ranking[] = (object)['name' => $d['name'] . ' ' . $d['last_name'], 'orders_count' => (int)$d['orders_count'], 'shipping_earned' => (float)$d['total_shipping'], 'total' => (float)$d['total_generated'], 'is_local' => false];
        }
        if ($localStats && $localStats['orders_count'] > 0) {
            $ranking[] = (object)['name' => '📦 Punto de Venta', 'orders_count' => (int)$localStats['orders_count'], 'shipping_earned' => 0, 'total' => (float)$localStats['total_generated'], 'is_local' => true];
        }
        usort($ranking, function ($a, $b) {

            return $b->orders_count <=> $a->orders_count;
        });

        // Top Productos
        $qTopP = $this->fetchTable('Products')->find();
        $topProducts = $qTopP->select(['name' => 'Products.name', 'image' => 'Products.image', 'sold_count' => $qTopP->func()->sum('Orders.quantity')])
            ->leftJoinWith('Orders', function ($q) use ($startDate, $endDate) {
                if ($startDate) {
                    $q->where(['Orders.created >=' => $startDate . ' 00:00:00']);
                }
                if ($endDate) {
                    $q->where(['Orders.created <=' => $endDate . ' 23:59:59']);
                }
                $q->andWhere(['Orders.status !=' => 'cancelado']); // Exclude cancelled orders

                return $q;
            })->groupBy(['Products.id'])->orderBy(['sold_count' => 'DESC'])->limit(5)->all()->toArray();

        $lowStock = $this->fetchTable('Ingredients')->find()->where(['stock <=' => 5])->orderBy(['stock' => 'ASC'])->limit(5)->all();

        // 6. Pendientes por entregar (Global para Admin/Staff)
        $pendingDeliveries = $ordersTable->find()->where([
            'status NOT IN' => ['entregado', 'cancelado'],
        ])->count();

        $this->set([
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'totalCostOfSales' => $totalCostOfSales,
            'totalShipping' => $totalShipping,
            'totalOrders' => $totalOrders,
            'netProfit' => $netProfit,
            'salesByDay' => $salesByDay,
            'driversRanking' => $ranking,
            'topProducts' => $topProducts,
            'lowStock' => $lowStock,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'paymentTotals' => $formattedPaymentTotals,
            'isRepartidor' => $isRepartidor,
            'pendingDeliveries' => $pendingDeliveries,
        ]);
    }

    public function syncInventory()
    {
        $this->request->allowMethod(['get', 'post']);

        $ingredientsTable = $this->fetchTable('Ingredients');
        $ordersTable = $this->fetchTable('Orders');

        // Obtener todos los ingredientes
        $ingredients = $ingredientsTable->find()->all();

        // Calcular uso total por ingrediente con SQL directo
        $connection = $ordersTable->getConnection();
        $usageQuery = $connection->execute('
            SELECT pi.ingredient_id, SUM(pi.quantity_required * o.quantity) as total_used
            FROM orders o
            INNER JOIN product_ingredients pi ON pi.product_id = o.product_id
            WHERE o.status NOT IN (\'cancelado\', \'pendiente\')
            GROUP BY pi.ingredient_id
        ');
        $usageRows = $usageQuery->fetchAll('assoc');
        $usageMap = [];
        foreach ($usageRows as $row) {
            $usageMap[(int)$row['ingredient_id']] = (float)$row['total_used'];
        }

        // Valores iniciales conocidos de la migración
        $initialStocks = [
            'Llave Virgen Sencilla' => 100,
            'Llave Virgen Seguridad' => 50,
            'Cilindro Estándar 60mm' => 10,
            'Chapa de Pomo Baño' => 5,
            'Lubricante Grafito' => 12,
        ];

        $report = [];

        foreach ($ingredients as $ingredient) {
            $totalUsed = $usageMap[(int)$ingredient->id] ?? 0;
            $initialStock = $initialStocks[$ingredient->name] ?? (float)$ingredient->stock;
            $correctStock = $initialStock - $totalUsed;
            $diff = (float)$ingredient->stock - $correctStock;

            $report[] = [
                'id' => $ingredient->id,
                'name' => $ingredient->name,
                'current_stock' => (float)$ingredient->stock,
                'total_used' => $totalUsed,
                'initial_stock' => $initialStock,
                'correct_stock' => $correctStock,
                'diff' => $diff,
                'unit' => $ingredient->unit,
            ];
        }

        // Si es POST, corregir el stock
        if ($this->request->is('post')) {
            $fixed = 0;
            foreach ($report as $r) {
                $ingredient = $ingredientsTable->get($r['id']);
                $ingredient->stock = $r['correct_stock'];
                if ($ingredientsTable->save($ingredient)) {
                    $fixed++;
                }
            }
            $this->Flash->success(__("Inventario corregido: {$fixed} insumos actualizados."));

            return $this->redirect(['action' => 'syncInventory']);
        }

        $this->set(compact('report'));
    }
}
