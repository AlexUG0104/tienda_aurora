<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$conn = new PDO("pgsql:host=localhost;dbname=aurora", "cesar", "1234");

$sql = "SELECT p.id, c.nombre AS cliente, p.fecha_compra, e.estado
        FROM pedido p
        JOIN cliente c ON c.id = p.id_cliente
        JOIN pedido_estado e ON e.id_estado = p.estado_pedido
        WHERE e.estado IN ('Pendiente', 'En proceso')";

$stmt = $conn->query($sql);
$pedidos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos por Enviar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background-color: #f0f2f5;
        }
        nav {
            background-color: #abc1b2;
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .nav-left a, .nav-right a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .main-content {
            padding-top: 20px;
            display: flex;
            justify-content: center;
        }
        .tabla-scroll {
            max-height: 400px;
            overflow-y: scroll;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 90%;
            max-width: 1000px;
            background-color: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #abc1b2;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        h2 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-left">
            <a href="#">Aurora Boutique</a>
        </div>
        <div class="nav-right">
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </nav>

    <h2>Pedidos por Enviar</h2>
    <div class="main-content">
        <div class="tabla-scroll">
            <table>
                <tr>
                    <th>ID</th><th>Cliente</th><th>Fecha</th><th>Estado</th><th>Acción</th>
                </tr>
                <?php foreach ($pedidos as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= $p['cliente'] ?></td>
                        <td><?= date("d/m/Y", strtotime($p['fecha_compra'])) ?></td>
                        <td><?= $p['estado'] ?></td>
                        <td><a href="pedidos_actualizar_estado.php?id=<?= $p['id'] ?>">Actualizar estado</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>