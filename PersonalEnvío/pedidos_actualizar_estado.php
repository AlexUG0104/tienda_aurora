<?php
// Archivo: pedidos_actualizar_estado.php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../utils/Mailer.php';

use Aurora\Mailer;

// Conexión a base de datos
$conn = new PDO("pgsql:host=localhost;dbname=aurora", "cesar", "1234");

// Obtener ID del pedido por GET
$id_pedido = $_GET['id'] ?? null;

if (!$id_pedido) {
    die('No se proporcionó un ID de pedido válido.');
}

// Si el formulario se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_estado = $_POST['estado'] ?? null;
    if ($nuevo_estado) {
        $sql = "UPDATE pedido SET estado_pedido = :estado WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':estado' => $nuevo_estado, ':id' => $id_pedido]);

        $sql_estado = "SELECT estado FROM pedido_estado WHERE id_estado = :id_estado";
        $stmt_estado = $conn->prepare($sql_estado);
        $stmt_estado->execute([':id_estado' => $nuevo_estado]);
        $estado_actual = $stmt_estado->fetchColumn();

        if (strtolower($estado_actual) === 'enviado') {
    $sql_info = "SELECT c.nombre AS nombre_cliente, cc.correo AS correo_cliente, p.fecha_compra
    FROM pedido p
    JOIN cliente c ON c.id = p.id_cliente
    JOIN cliente_correo cc ON cc.id_cliente = c.id
    WHERE p.id = :id
    LIMIT 1";
$stmt_info = $conn->prepare($sql_info);
$stmt_info->execute([':id' => $id_pedido]);
$pedido = $stmt_info->fetch(PDO::FETCH_ASSOC);

            if ($pedido && filter_var($pedido['correo_cliente'], FILTER_VALIDATE_EMAIL)) {
                $mailer = new Mailer();

                $asunto = "¡Tu pedido ha sido enviado!";
                $cuerpo = "
                    <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; background-color: #f9f9f9; color: #333; }
                            .container { max-width: 600px; margin: 20px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
                            h2 { color: #007BFF; }
                            p { font-size: 16px; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <h2>¡Hola " . htmlspecialchars($pedido['nombre_cliente']) . "!</h2>
                            <p>Nos alegra informarte que tu pedido realizado el <strong>" . date('d/m/Y', strtotime($pedido['fecha_compra'])) . "</strong> ha sido <strong>enviado</strong>.</p>
                            <p>Gracias por tu compra en <strong>Aurora Boutique</strong>. Te mantendremos al tanto hasta la entrega.</p>
                            <p>¡Esperamos que disfrutes tu compra!</p>
                            <p style='margin-top: 30px;'>Atentamente,<br><strong>Boutique Aurora CR</strong></p>
                        </div>
                    </body>
                    </html>
                ";

                if (!$mailer->enviarCorreo($pedido['correo_cliente'], $asunto, $cuerpo)) {
                    error_log("No se pudo enviar el correo al cliente con ID pedido: $id_pedido");
                }
            }
        }

        $_SESSION['estado_actualizado'] = true;
        header("Location: pedidos_por_enviar.php");
        exit;
    }
}

$sql_estados = "SELECT id_estado, estado FROM pedido_estado";
$estados = $conn->query($sql_estados)->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Estado del Pedido #<?= htmlspecialchars($id_pedido) ?></title>
    <link rel="icon" href="../imagenes/AB.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
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
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 50px;
            min-height: calc(100vh - 70px);
        }
        .form-box {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        h2 {
            margin-bottom: 25px;
            color: #333;
        }
        label {
            display: block;
            text-align: left;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
        }
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-left">
            <a href="pedidos_por_enviar.php">Lista de Pedidos</a>
        </div>
        <div class="nav-right">
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="container">
        <div class="form-box">
            <h2>Actualizar Estado del Pedido #<?= htmlspecialchars($id_pedido) ?></h2>
            <form method="post">
                <label for="estado">Nuevo estado:</label>
                <select name="estado" id="estado" required>
                    <?php foreach ($estados as $e): ?>
                        <option value="<?= $e['id_estado'] ?>" <?= ($e['id_estado'] == ($nuevo_estado ?? '') ? 'selected' : '') ?>>
                            <?= htmlspecialchars($e['estado']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Actualizar</button>
            </form>
        </div>
    </div>
</body>
</html>
