<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (isset($_SESSION['estado_actualizado'])) {
    echo "<div style='background:#d4edda;color:#155724;padding:15px;border-radius:5px;margin:10px auto;width:90%;max-width:600px;text-align:center;'>";
    echo "El estado del pedido fue actualizado correctamente.";
    if (isset($_SESSION['correo_enviado']) && $_SESSION['correo_enviado']) {
        echo " El correo de notificación fue enviado exitosamente.";
    } else {
        echo " Pero hubo un problema al enviar el correo de notificación.";
    }
    echo "</div>";

    unset($_SESSION['estado_actualizado'], $_SESSION['correo_enviado']);
}



require_once __DIR__ . '/../db.php'; 
try {
    $sql = "SELECT * FROM obtener_pedidos_pendientes()";
    $stmt = $conn->query($sql);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    echo "Error en la conexión o consulta: " . $ex->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pedidos por Enviar</title>
    <link rel="icon" href="imagenes/AB.ico" type="image/x-icon" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
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
        h2 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }
        .main-content {
            padding-top: 20px;
            display: flex;
            justify-content: center;
        }
        .tabla-scroll {
            max-height: 400px;
            overflow-y: auto;
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
        a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
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
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($pedidos)): ?>
                    <?php foreach ($pedidos as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['id']) ?></td>
                            <td><?= htmlspecialchars($p['cliente']) ?></td>
                            <td><?= htmlspecialchars(date("d/m/Y", strtotime($p['fecha_compra']))) ?></td>
                            <td><?= htmlspecialchars($p['estado']) ?></td>
                            <td>
                                <a href="pedidos_actualizar_estado.php?id=<?= urlencode($p['id']) ?>">Actualizar estado</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No hay pedidos pendientes o en proceso.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
