<?php
require_once '../config_sesion.php';
require_once '../db.php';

if (!isset($_GET['id_pedido'])) {
    die("Pedido no especificado.");
}

$idPedido = (int) $_GET['id_pedido'];

// Validar que el pedido sea del cliente logueado
$stmt = $pdo->prepare("
    SELECT id FROM pedido 
    WHERE id = :id 
    AND id_cliente = (
        SELECT id FROM cliente WHERE id_credencial = :credencial
    )
");
$stmt->execute([
    ':id' => $idPedido,
    ':credencial' => $_SESSION['user_id']
]);
if (!$stmt->fetchColumn()) {
    die("Acceso denegado.");
}

// Consultar la factura
$stmt = $pdo->prepare("SELECT * FROM factura WHERE id_pedido = :id");
$stmt->execute([':id' => $idPedido]);
$factura = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$factura) {
    echo "<p>No se ha generado factura para este pedido aún.</p>";
    exit();
}

// Consultar el detalle del pedido
$stmt = $pdo->prepare("
    SELECT pr.nombre AS producto, dp.cantidad, dp.precio_unitario
    FROM detalle_pedido dp
    JOIN producto pr ON dp.id_producto = pr.id
    WHERE dp.id_pedido = :id
");
$stmt->execute([':id' => $idPedido]);
$detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura del Pedido #<?= htmlspecialchars($idPedido) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f5;
            margin: 30px;
            color: #333;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        h2 {
            margin: 0;
            color: #444;
        }

        .btn-imprimir {
            background-color: #abc1b2;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.2s ease-in-out;
            margin-left: 8px;
            cursor: pointer;
        }

        .btn-imprimir:hover {
            background-color: #94a89b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 0 8px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 10px 14px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #abc1b2;
            color: white;
            text-transform: uppercase;
            font-size: 14px;
        }

        h3 {
            margin-top: 30px;
        }

        .total-row {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .resumen-table td {
            text-align: right;
            font-size: 15px;
        }

        .resumen-table td:first-child {
            text-align: left;
            font-weight: bold;
            color: #444;
        }

        @media print {
            .btn-regresar {
                display: none;
            }

            .top-bar {
                justify-content: flex-start;
            }

            body {
                margin: 0;
                background: white;
                color: black;
            }
        }
    </style>
</head>
<body>

<div class="top-bar">
    <h2>🧾 Factura del Pedido #<?= htmlspecialchars($idPedido) ?></h2>
    <div>
        <button class="btn-imprimir" onclick="window.print()"><i class="fas fa-print"></i> Imprimir PDF</button>
    </div>
</div>

<p><strong>📅 Fecha de emisión:</strong> <?= htmlspecialchars($factura['fecha_emision']) ?></p>

<h3>🛒 Detalle de productos:</h3>
<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Total Línea</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($detalles as $item): 
            $totalLinea = $item['cantidad'] * $item['precio_unitario'];
        ?>
        <tr>
            <td><?= htmlspecialchars($item['producto']) ?></td>
            <td><?= $item['cantidad'] ?></td>
            <td>₡<?= number_format($item['precio_unitario'], 2) ?></td>
            <td>₡<?= number_format($totalLinea, 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h3>💰 Resumen de factura:</h3>
<table class="resumen-table">
    <tr>
        <td>Subtotal:</td>
        <td>₡<?= number_format($factura['subtotal'], 2) ?></td>
    </tr>
    <tr>
        <td>Descuento:</td>
        <td>₡<?= number_format($factura['descuento'], 2) ?></td>
    </tr>
    <tr>
        <td>Impuesto (13%):</td>
        <td>₡<?= number_format($factura['impuesto'], 2) ?></td>
    </tr>
    <tr class="total-row">
        <td>Total:</td>
        <td>₡<?= number_format($factura['total'], 2) ?></td>
    </tr>
</table>

</body>
</html>
