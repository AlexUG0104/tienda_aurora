<?php
require_once '../config_sesion.php';
require_once '../db.php';

// Verificar que sea cliente logueado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: login_cliente.php?type=client");
    exit();
}

// Obtener pedidos del cliente con total y si tienen transacción registrada
try {
    $stmt = $pdo->prepare("
        SELECT p.id, p.codigo_pedido, p.fecha_compra, pe.estado AS estado_pedido,
               COALESCE(SUM(dp.cantidad * dp.precio_unitario), 0) AS total,
               (SELECT COUNT(*) FROM transaccion t WHERE t.id_pedido = p.id) AS tiene_transaccion
        FROM pedido p
        JOIN pedido_estado pe ON p.estado_pedido = pe.id_estado
        LEFT JOIN detalle_pedido dp ON p.id = dp.id_pedido
        WHERE p.id_cliente = :id_cliente
        GROUP BY p.id, pe.estado, p.fecha_compra
        ORDER BY p.fecha_compra DESC
    ");
    $stmt->execute([':id_cliente' => $_SESSION['user_id']]);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al obtener pedidos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" href="imagenes/AB.ico" type="image/x-icon">

    <meta charset="UTF-8" />
    <title>Mis Pedidos - Aurora Boutique</title>
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

        nav .nav-left a {
            color: #333;
            text-decoration: none;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
        }

        .main-content-wrapper {
            padding-top: 90px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .table-container {
            width: 90%;
            max-width: 1000px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }

        .table-container h1 {
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        table th {
            background-color: #f9f9f9;
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            transition: background-color 0.3s ease;
        }

        .btn-success {
            background-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

<nav>
    <div class="nav-left">
        <a href="../index.php"><i class="fas fa-store"></i> Aurora Boutique</a>
    </div>
</nav>

<div class="main-content-wrapper">
    <div class="table-container">
        <h1>Mis Pedidos</h1>

        <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'transaccion_ok'): ?>
            <p style="color: green; font-weight: bold;">¡Transacción registrada con éxito!</p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Código Pedido</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Monto Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($pedidos) > 0): ?>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pedido['codigo_pedido']); ?></td>
                            <td><?php echo htmlspecialchars($pedido['fecha_compra']); ?></td>
                            <td><?php echo htmlspecialchars($pedido['estado_pedido']); ?></td>
                            <td>₡<?php echo number_format($pedido['total'], 2); ?></td>
                            <td>
                                <?php if ($pedido['tiene_transaccion'] == 0 && $pedido['estado_pedido'] == 'Pendiente'): ?>
                                    <a href="registrar_transaccion.php?id_pedido=<?php echo $pedido['id']; ?>" class="btn btn-success">Registrar Transacción</a>
                                <?php elseif ($pedido['tiene_transaccion'] > 0): ?>
                                    <a href="#" class="btn btn-secondary" style="pointer-events: none;">Transacción Registrada</a>
                                <?php else: ?>
                                    <!-- No mostrar acción -->
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No tienes pedidos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
