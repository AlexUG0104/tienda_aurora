<?php
require_once '../config_sesion.php';
require_once '../db.php';

// Verificar que sea cliente logueado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: login_cliente.php?type=client");
    exit();
}

// Obtener ID del estado 'Entregado'
$stmtEstado = $pdo->prepare("SELECT id_estado FROM pedido_estado WHERE estado = 'Entregado' LIMIT 1");
$stmtEstado->execute();
$idEstadoEntregado = $stmtEstado->fetchColumn();

if (!$idEstadoEntregado) {
    die("Estado 'Entregado' no encontrado en pedido_estado.");
}

// Obtener pedidos entregados del cliente
$stmtPedidos = $pdo->prepare("
    SELECT id, codigo_pedido, fecha_entrega
    FROM pedido
    WHERE id_cliente = :id_cliente AND estado_pedido = :estado_entregado
    ORDER BY fecha_entrega DESC
");
$stmtPedidos->execute([
    ':id_cliente' => $_SESSION['user_id'],
    ':estado_entregado' => $idEstadoEntregado
]);
$pedidos = $stmtPedidos->fetchAll(PDO::FETCH_ASSOC);

// Si selecciona un pedido, cargar sus productos
$productosPedido = [];
if (isset($_GET['id_pedido'])) {
    $idPedido = $_GET['id_pedido'];

    // Validar que el pedido pertenece al cliente y está entregado
    $stmtValidar = $pdo->prepare("
        SELECT COUNT(*) FROM pedido
        WHERE id = :id_pedido AND id_cliente = :id_cliente AND estado_pedido = :estado_entregado
    ");
    $stmtValidar->execute([
        ':id_pedido' => $idPedido,
        ':id_cliente' => $_SESSION['user_id'],
        ':estado_entregado' => $idEstadoEntregado
    ]);
    if ($stmtValidar->fetchColumn() > 0) {
        // Obtener productos del pedido
        $stmtProductos = $pdo->prepare("
            SELECT p.nombre, dp.cantidad, dp.precio_unitario
            FROM detalle_pedido dp
            INNER JOIN producto p ON dp.id_producto = p.id
            WHERE dp.id_pedido = :id_pedido
        ");
        $stmtProductos->execute([':id_pedido' => $idPedido]);
        $productosPedido = $stmtProductos->fetchAll(PDO::FETCH_ASSOC);
    } else {
        die("Pedido no válido.");
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" href="imagenes/AB.ico" type="image/x-icon">

    <meta charset="UTF-8" />
    <title>Dejar Reseña - Aurora Boutique</title>
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

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 90%;
            max-width: 600px;
            margin-bottom: 40px;
        }

        .form-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .form-container select, .form-container textarea, .form-container input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .form-container textarea {
            resize: vertical;
        }

        .form-container .stars {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .form-container .stars i {
            font-size: 30px;
            color: #ccc;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .form-container .stars i.selected {
            color: #f5a623;
        }

        .productos-lista {
            margin-bottom: 20px;
        }

        .productos-lista ul {
            list-style: none;
            padding: 0;
        }

        .productos-lista li {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 8px 12px;
            margin-bottom: 5px;
            border-radius: 5px;
        }

        .form-container input[type="submit"] {
            background-color: #28a745;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            border: none;
        }

        .form-container input[type="submit"]:hover {
            background-color: #218838;
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

    <div class="form-container">
        <h1>Dejar Reseña</h1>

        <form method="get" action="dejar_resena.php">
            <label for="id_pedido">Selecciona un pedido entregado:</label>
            <select name="id_pedido" id="id_pedido" required onchange="this.form.submit()">
                <option value="">-- Selecciona un pedido --</option>
                <?php foreach ($pedidos as $pedido): ?>
                    <option value="<?php echo $pedido['id']; ?>" <?php if (isset($idPedido) && $pedido['id'] == $idPedido) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($pedido['codigo_pedido']) . " - " . htmlspecialchars($pedido['fecha_entrega']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if (!empty($productosPedido)): ?>
            <div class="productos-lista">
                <h3>Productos en este pedido:</h3>
                <ul>
                    <?php foreach ($productosPedido as $producto): ?>
                        <li>
                            <?php echo htmlspecialchars($producto['nombre']); ?> - Cantidad: <?php echo $producto['cantidad']; ?> - ₡<?php echo number_format($producto['precio_unitario'], 2); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <form method="post" action="guardar_resena.php">
                <input type="hidden" name="id_pedido" value="<?php echo $idPedido; ?>">

                <label>Calificación:</label>
                <div class="stars" id="stars">
                    <i class="fa fa-star" data-value="1"></i>
                    <i class="fa fa-star" data-value="2"></i>
                    <i class="fa fa-star" data-value="3"></i>
                    <i class="fa fa-star" data-value="4"></i>
                    <i class="fa fa-star" data-value="5"></i>
                </div>
                <input type="hidden" name="calificacion" id="calificacion" required>

                <label for="comentario">Comentario:</label>
                <textarea name="comentario" id="comentario" rows="4" placeholder="Escribe tu comentario..." required></textarea>

                <input type="submit" value="Enviar Reseña">
            </form>
        <?php endif; ?>
    </div>

</div>

<script>
    const stars = document.querySelectorAll('#stars i');
    const calificacionInput = document.getElementById('calificacion');

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const value = star.getAttribute('data-value');
            calificacionInput.value = value;

            stars.forEach(s => s.classList.remove('selected'));
            for (let i = 0; i < value; i++) {
                stars[i].classList.add('selected');
            }
        });
    });
</script>

</body>
</html>
