<?php
require_once '../config_sesion.php';
require_once '../db.php';

// Verificar que sea cliente logueado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: login_cliente.php?type=client");
    exit();
}

// Recibir carrito desde formulario anterior
$carrito = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['carrito'])) {
    $carrito = json_decode($_POST['carrito'], true);
    if (!is_array($carrito)) {
        die("Error: formato de carrito inválido.");
    }
} else {
    die("No se recibieron productos para el pedido.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Resumen del Pedido - Aurora Boutique</title>
    <link rel="icon" href="../imagenes/AB.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        nav {
            background-color: #abc1b2;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        nav a {
            color: #333;
            text-decoration: none;
            font-size: 1.2rem;
        }
        .content {
            padding-top: 100px;
            max-width: 800px;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }
        .btn-confirmar {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            margin: 30px auto 0;
        }
        .btn-confirmar:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<nav>
    <a href="../index.php"><i class="fas fa-store"></i> Aurora Boutique</a>
</nav>

<div class="content">
    <h1>Resumen del Pedido</h1>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            foreach ($carrito as $item) {
                $nombre = htmlspecialchars($item['nombre']);
                $precio = floatval($item['precio']);
                $cantidad = intval($item['cantidad']);
                $subtotal = $precio * $cantidad;
                $total += $subtotal;
                echo "<tr>
                        <td>{$nombre}</td>
                        <td>₡" . number_format($precio, 2) . "</td>
                        <td>{$cantidad}</td>
                        <td>₡" . number_format($subtotal, 2) . "</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
    <div class="total">
        Total a pagar: ₡<?php echo number_format($total, 2); ?>
    </div>

    <form action="procesar_pago.php" method="post">
        <input type="hidden" name="carrito" value='<?php echo json_encode($carrito); ?>'>
        <button class="btn-confirmar" type="submit">Confirmar y Pagar</button>
    </form>
</div>

</body>
</html>
