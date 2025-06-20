<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../utils/Mailer.php';

use Aurora\Mailer;

// Obtener ID del pedido por GET
$id_pedido = $_GET['id'] ?? null;
if (!$id_pedido) {
    die('No se proporcionó un ID de pedido válido.');
}

// Obtener estados disponibles (fuera del POST para mostrar el select)
$sql_estados = "SELECT * FROM obtener_todos_los_estados()";
$estados = $conn->query($sql_estados)->fetchAll();

$nuevo_estado = null;

// Si el formulario se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_estado = $_POST['estado'] ?? null;
    if ($nuevo_estado) {
        // Actualizar estado del pedido
        $sql = "CALL actualizar_estado_pedido(:id, :estado)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id_pedido, ':estado' => $nuevo_estado]);


        // Obtener nombre del estado actualizado usando función
        $sql_estado = "SELECT obtener_nombre_estado(:id_estado)";
        $stmt_estado = $conn->prepare($sql_estado);
        $stmt_estado->execute([':id_estado' => $nuevo_estado]);
        $estado_actual = $stmt_estado->fetchColumn();

        // Obtener datos del cliente y pedido mediante función
        $sql_info = "SELECT * FROM obtener_info_pedido_cliente(:id)";
        $stmt_info = $conn->prepare($sql_info);
        $stmt_info->execute([':id' => $id_pedido]);
        $pedido = $stmt_info->fetch(PDO::FETCH_ASSOC);

        // Mensajes personalizados por estado
        $mensajes = [
            'Pendiente'   => 'Tu pedido ha sido recibido y está pendiente de ser procesado.',
            'En proceso'  => 'Estamos preparando tu pedido con cuidado y dedicación.',
            'Enviado'     => 'Tu pedido ha sido enviado y está en camino. ¡Gracias por tu compra!',
            'Entregado'   => 'Tu pedido ha sido entregado exitosamente. ¡Esperamos que lo disfrutes!',
        ];
        $mensaje_estado = $mensajes[$estado_actual] ?? "El estado de tu pedido ha sido actualizado.";

        // Intentar enviar correo y capturar resultado
        $correo_enviado = false;
        if ($pedido && filter_var($pedido['correo_cliente'], FILTER_VALIDATE_EMAIL)) {
            $mailer = new Mailer();
            $asunto = "Actualización del estado de tu pedido";
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
                        <p>Te informamos que el estado de tu pedido realizado el <strong>" . date('d/m/Y', strtotime($pedido['fecha_compra'])) . "</strong> ha cambiado a <strong>" . htmlspecialchars($estado_actual) . "</strong>.</p>
                        <p>$mensaje_estado</p>
                        <p>Gracias por confiar en <strong>Aurora Boutique</strong>.</p>
                        <p style='margin-top: 30px;'>Atentamente,<br><strong>Boutique Aurora CR</strong></p>
                    </div>
                </body>
                </html>
            ";

            $correo_enviado = $mailer->enviarCorreo($pedido['correo_cliente'], $asunto, $cuerpo);
            if (!$correo_enviado) {
                error_log("No se pudo enviar el correo al cliente con ID pedido: $id_pedido");
            }
        } else {
            error_log("Correo inválido o no encontrado para pedido ID: $id_pedido");
        }

        // Guardar mensaje en sesión para mostrar confirmación en la página de pedidos
        $_SESSION['estado_actualizado'] = true;
        $_SESSION['correo_enviado'] = $correo_enviado;

        header("Location: pedidos_por_enviar.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Estado del Pedido #<?= htmlspecialchars($id_pedido) ?></title>
    <link rel="icon" href="../imagenes/AB.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Tu CSS tal cual */
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
