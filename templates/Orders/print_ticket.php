<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order $order
 */
$company = $order->company;
$branch = $order->branch;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ticket #<?= $order->id ?></title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm;
            margin: 0;
            padding: 0;
            font-size: 12px;
            color: #000;
            line-height: 1.2;
        }
        .ticket {
            padding: 4mm;
            width: 72mm;
        }
        .header {
            text-align: center;
            margin-bottom: 4mm;
        }
        .brand {
            font-weight: bold;
            font-size: 18px;
            text-transform: uppercase;
            margin-bottom: 1mm;
        }
        .biz-info {
            font-size: 10px;
            margin-bottom: 2mm;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 2mm 0;
        }
        .info {
            font-size: 11px;
            margin-bottom: 3mm;
        }
        .info p {
            margin: 0.5mm 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            text-align: left;
            font-size: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 1mm;
        }
        td {
            padding: 1mm 0;
            vertical-align: top;
        }
        .total-row {
            font-weight: bold;
            font-size: 14px;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            text-align: center;
            margin-top: 6mm;
            font-size: 10px;
        }
        @media print {
            .no-print { display: none; }
            body { width: 80mm; }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="ticket">
        <div class="header">
            <div class="brand"><?= h($company->name) ?></div>
            <div class="biz-info">
                <p><strong>NIT: <?= h($company->nit) ?></strong></p>
                <p><?= h($branch->name) ?></p>
                <p><?= h($branch->address ?: $company->address) ?></p>
                <p>Tel: <?= h($branch->phone ?: $company->phone) ?></p>
            </div>
            <div class="divider"></div>
            <p><strong>COMPROBANTE DE VENTA</strong></p>
            <p>ORDEN: #<?= str_pad((string)$order->id, 6, '0', STR_PAD_LEFT) ?></p>
            <p><?= $order->created->format('d/m/Y H:i') ?></p>
        </div>

        <div class="info">
            <p><strong>CLIENTE:</strong> <?= h($order->customer_name) ?></p>
            <p><strong>CEL:</strong> <?= h($order->customer_phone) ?></p>
            <p><strong>TIPO:</strong> <?= strtoupper($order->type) ?></p>
            <?php if ($order->type === 'domicilio'): ?>
                <p><strong>DIR:</strong> <?= h($order->customer_address) ?></p>
            <?php endif; ?>
        </div>

        <div class="divider"></div>

        <table>
            <thead>
                <tr>
                    <th width="15%">CANT</th>
                    <th>DESCRIPCIÓN</th>
                    <th width="30%" class="text-right">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $order->quantity ?></td>
                    <td><?= h($order->product->name) ?></td>
                    <td class="text-right">$<?= number_format($order->total - ($order->shipping_cost ?? 0), 0) ?></td>
                </tr>
                <?php if ($order->shipping_cost > 0): ?>
                <tr>
                    <td>1</td>
                    <td>Servicio Domicilio</td>
                    <td class="text-right">$<?= number_format($order->shipping_cost, 0) ?></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="divider"></div>

        <table>
            <tr class="total-row">
                <td>TOTAL:</td>
                <td class="text-right">$<?= number_format($order->total, 0) ?></td>
            </tr>
        </table>
        
        <p style="margin-top: 3mm; font-size: 10px;">
            <strong>FORMA DE PAGO:</strong> <?= h($order->payment_method) ?>
        </p>

        <div class="footer">
            <p>********************************</p>
            <p>¡MUCHAS GRACIAS POR SU COMPRA!</p>
            <p>Vuelva pronto</p>
            <p>********************************</p>
            <p style="margin-top: 2mm; font-size: 8px; color: #666;">
                Powered by CERRAJERÍA SARRIA PRO
            </p>
            <div class="no-print" style="margin-top: 5mm;">
                <button onclick="window.close();" style="padding: 8px 15px; cursor:pointer; font-family: sans-serif; border-radius: 5px; border: 1px solid #ccc;">Cerrar Ventana</button>
            </div>
        </div>
    </div>
</body>
</html>
