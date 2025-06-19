<?php
require_once '../config_sesion.php';
require_once '../db.php';

// Verificar que sea cliente logueado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: login_cliente.php?type=client");
    exit();
}

// Validar que venga id_pedido por GET
if (!isset($_GET['id_pedido']) || empty($_GET['id_pedido'])) {
    die("ID de pedido no válido.");
}

$idPedido = (int) $_GET['id_pedido'];

// Obtener información del pedido
try {
    // Verificar que el pedido pertenece a este cliente
    $stmt = $pdo->prepare("SELECT p.id, p.codigo_pedido, p.estado_pedido, 
                                  COALESCE(SUM(dp.cantidad * dp.precio_unitario), 0) AS total
                           FROM pedido p
                           LEFT JOIN detalle_pedido dp ON p.id = dp.id_pedido
                           WHERE p.id = :id_pedido AND p.id_cliente = :id_cliente
                           GROUP BY p.id");
    $stmt->execute([
        ':id_pedido' => $idPedido,
        ':id_cliente' => $_SESSION['user_id']
    ]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        die("Pedido no encontrado o no autorizado.");
    }

    // Obtener métodos de pago
    $stmt = $pdo->query("SELECT id_metodo_pago, nombre_metodo FROM metodo_pago ORDER BY nombre_metodo");
    $metodosPago = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al obtener datos: " . $e->getMessage());
}

// Procesar el formulario (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idMetodoPago = $_POST['metodo_pago'] ?? null;
    $referenciaPago = trim($_POST['referencia_pago'] ?? '');

    if (!$idMetodoPago || $pedido['total'] <= 0) {
        die("Datos incompletos.");
    }

    try {
        $pdo->beginTransaction();

        // Insertar la transacción
        $stmt = $pdo->prepare("
            INSERT INTO transaccion (id_metodo_pago, id_pedido, monto_total, referencia_pago, fecha_transaccion, usuario_creacion)
            VALUES (:id_metodo_pago, :id_pedido, :monto_total, :referencia_pago, NOW(), :usuario_creacion)
        ");
        $stmt->execute([
            ':id_metodo_pago' => $idMetodoPago,
            ':id_pedido' => $pedido['id'],
            ':monto_total' => $pedido['total'],
            ':referencia_pago' => $referenciaPago,
            ':usuario_creacion' => $_SESSION['user_id']
        ]);

        $pdo->commit();

        // Redirigir a "Mis pedidos" con mensaje
        header("Location: mis_pedidos.php?mensaje=transaccion_ok");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error al registrar transacción: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" href="imagenes/AB.ico" type="image/x-icon">

    <meta charset="UTF-8" />
    <title>Registrar Transacción - Aurora Boutique</title>
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
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }

        .form-container h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .form-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-container input[type="text"],
        .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container button {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .form-container button:hover {
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
    <form class="form-container" method="post">
        <h1>Registrar Transacción</h1>

        <p><strong>Pedido:</strong> <?php echo htmlspecialchars($pedido['codigo_pedido']); ?></p>
        <p><strong>Monto Total:</strong> ₡<?php echo number_format($pedido['total'], 2); ?></p>

        <label for="metodo_pago">Método de Pago:</label>
        <select name="metodo_pago" id="metodo_pago" required>
            <option value="">Seleccione un método</option>
            <?php foreach ($metodosPago as $metodo): ?>
                <option value="<?php echo $metodo['id_metodo_pago']; ?>"><?php echo htmlspecialchars($metodo['nombre_metodo']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="referencia_pago">Número de referencia:</label>
        <input type="text" name="referencia_pago" id="referencia_pago" maxlength="100" placeholder="Ej: número de referencia SINPE o banco" required>

        <button type="submit">Confirmar Transacción</button>
    </form>
</div>

</body>
</html>
