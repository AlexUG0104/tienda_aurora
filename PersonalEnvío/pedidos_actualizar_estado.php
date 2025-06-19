<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = new PDO("pgsql:host=localhost;dbname=aurora", "cesar", "1234");

$id_pedido = $_GET['id'] ?? null;

// Si se envía el formulario para actualizar el estado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_estado = $_POST['estado'];

    $sql = "UPDATE pedido SET estado_pedido = :estado WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':estado' => $nuevo_estado, ':id' => $id_pedido]);

    $_SESSION['estado_actualizado'] = true;
    header("Location: pedidos_por_enviar.php");
    exit;
}

// Obtener todos los estados posibles
$sql = "SELECT id_estado, estado FROM pedido_estado";
$estados = $conn->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Estado</title>
    <link rel="icon" href="imagenes/AB.ico" type="image/x-icon">
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
                        <option value="<?= $e['id_estado'] ?>"><?= htmlspecialchars($e['estado']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Actualizar</button>
            </form>
        </div>
    </div>
</body>
</html>
