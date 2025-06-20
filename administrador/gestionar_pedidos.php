<?php
session_start();
require_once '../config_sesion.php'; 

if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 1) {
    header("Location: ../administrador/login.php");
    exit();
}

require_once '../db.php'; 
require_once 'classes/GestorPedidos.php'; 

$gestorPedidos = new GestorPedidos($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_pedido_aceptar'])) {
        $id_pedido = intval($_POST['id_pedido_aceptar']);

        $stmt = $pdo->prepare("SELECT aceptar_pedido(:id)");
        $stmt->bindParam(':id', $id_pedido, PDO::PARAM_INT);

        if ($stmt->execute() && $stmt->fetchColumn()) {
            $_SESSION['mensaje_exito'] = "Pedido #$id_pedido aceptado correctamente.";
        } else {
            $_SESSION['mensaje_error'] = "Error al aceptar pedido #$id_pedido.";
        }

        header("Location: gestionar_pedidos.php");
        exit;
    }

    if (isset($_POST['id_pedido_rechazar'])) {
        $id_pedido = intval($_POST['id_pedido_rechazar']);

        $stmt = $pdo->prepare("SELECT rechazar_pedido(:id)");
        $stmt->bindParam(':id', $id_pedido, PDO::PARAM_INT);

        if ($stmt->execute() && $stmt->fetchColumn()) {
            $_SESSION['mensaje_exito'] = "Pedido #$id_pedido rechazado correctamente.";
        } else {
            $_SESSION['mensaje_error'] = "Error al rechazar pedido #$id_pedido.";
        }

        header("Location: gestionar_pedidos.php");
        exit;
    }
}


$pedidos = $gestorPedidos->obtenerPedidosAdministradorPendientes();

$initial_load_error = (!is_array($pedidos) || empty($pedidos)) ? "No se pudieron cargar los pedidos. Verifique los logs del servidor." : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gestión de Pedidos - Admin</title>
    <link rel="icon" href="imagenes/AB.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <style>
    body {
        font-family: 'Montserrat', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f0f2f5;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        height: 100vh; 
    }

    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 60px;
        background-color: #34495e;
        color: white;
        padding: 0 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        z-index: 1000;
        flex-shrink: 0;
    }

    .navbar .brand {
        font-size: 1.8em;
        font-weight: 700;
        color: white;
        text-decoration: none;
        letter-spacing: 1.5px;
    }

    .container-wrapper {
        display: flex;
        flex-grow: 1;
        width: 100%;
        height: calc(100vh - 60px); /* altura total menos navbar */
        overflow: hidden; /* evitar scroll innecesario */
        margin-top: 60px; /* para no tapar navbar fijo */
    }

    .sidebar {
        width: 250px;
        background-color: #2c3e50;
        color: white;
        padding: 20px;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        overflow-y: auto; /* para scroll vertical */
        flex-shrink: 0;
    }

    .sidebar h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #ecf0f1;
        font-size: 1.8em;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        padding-bottom: 15px;
        flex-shrink: 0;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
        margin: 0;
        flex-grow: 1;
        overflow-y: auto;
    }

    .sidebar ul li {
        margin-bottom: 10px;
    }

    .sidebar ul li a {
        display: block;
        padding: 12px 15px;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .sidebar ul li a:hover {
        background-color: #34495e;
        transform: translateX(5px);
    }

    .sidebar ul li a.active {
        background-color: #007bff;
        font-weight: bold;
    }

    .logout-button {
        background-color: #f44336;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        text-align: center;
        margin-top: auto; /* pegar abajo */
        display: block;
        text-decoration: none;
        transition: background-color 0.3s ease;
        flex-shrink: 0;
    }

    .logout-button:hover {
        background-color: #d32f2f;
    }

    .main-content {
        flex-grow: 1;
        padding: 30px;
        background-color: #ffffff;
        border-radius: 8px;
        margin: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        overflow-y: auto;
    }

    /* Formulario */
    .form-container {
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        margin-bottom: 30px;
    }

    .form-container h3 {
        text-align: center;
        color: #333;
        margin-bottom: 25px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        color: #555;
        font-weight: bold;
    }

    input[type="text"],
    input[type="password"],
    textarea,
    select,
    input[type="number"] {
        width: calc(100% - 22px);
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 16px;
    }

    input[type="submit"] {
        background-color: #28a745;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 18px;
        width: 100%;
        transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
        background-color: #218838;
    }

    /* Tabla de datos */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border-radius: 8px;
        overflow: hidden;
    }

    .data-table th,
    .data-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
    }

    .data-table th {
        background-color: #f8f9fa;
        color: #333;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 0.9em;
    }

    .data-table tr:hover {
        background-color: #f2f2f2;
    }

    .data-table .action-button {
        background-color: #007bff;
        color: white;
        padding: 8px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.9em;
        transition: background-color 0.3s ease;
        margin-right: 5px;
    }

    .data-table .action-button:hover {
        background-color: #0056b3;
    }

    .data-table .edit-button {
        background-color: #28a745;
    }

    .data-table .edit-button:hover {
        background-color: #218838;
    }
</style>
</head>
<body>
    <nav class="navbar">
        <a href="../index.php" class="brand">Aurora Boutique</a>
    </nav>
    <div class="container-wrapper">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <nav>
                <ul>
                    <li><a href="gestionar_usuarios.php" class="nav-link"><i class="fas fa-users"></i> Gestión de Usuarios</a></li>
                    <li><a href="gestionar_pedidos.php" class="nav-link active"><i class="fas fa-clipboard-list"></i> Gestión de Pedidos</a></li>
                    <li><a href="gestionar_productos.php" class="nav-link"><i class="fas fa-box-open"></i> Gestión de Productos</a></li>
                    <li><a href="ver_ventas.php" class="nav-link"><i class="fas fa-chart-line"></i> Ver Ventas</a></li>
                </ul>
            </nav>
            <a href="../logout_admin.php" class="logout-button"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>

        <div class="main-content">
            <h2>Gestión de Pedidos Pendientes</h2>

            <?php if (!empty($_SESSION['mensaje_exito'])): ?>
                <div class="mensaje-exito"><?= htmlspecialchars($_SESSION['mensaje_exito']) ?></div>
                <?php unset($_SESSION['mensaje_exito']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['mensaje_error'])): ?>
                <div class="mensaje-error"><?= htmlspecialchars($_SESSION['mensaje_error']) ?></div>
                <?php unset($_SESSION['mensaje_error']); ?>
            <?php endif; ?>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Cliente</th>
                        <th>Fecha Compra</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($initial_load_error) {
                        echo '<tr><td colspan="6" style="color:red;">' . htmlspecialchars($initial_load_error) . '</td></tr>';
                    } elseif (!empty($pedidos)) {
                        foreach ($pedidos as $pedido) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($pedido->id) . '</td>';
                            echo '<td>' . htmlspecialchars($pedido->id_cliente) . ' - ' . htmlspecialchars($pedido->cliente ?? 'N/A') . '</td>';
                            echo '<td>' . htmlspecialchars($pedido->fecha_compra ? (new DateTime($pedido->fecha_compra))->format('d/m/Y') : 'N/A') . '</td>';
                            echo '<td>' . htmlspecialchars($pedido->estado ?? 'N/A') . '</td>';
                            echo '<td>
                                <form method="post" action="gestionar_pedidos.php" style="display:inline;">
                                    <input type="hidden" name="id_pedido_aceptar" value="' . htmlspecialchars($pedido->id) . '">
                                    <button type="submit" class="action-button edit-button" onclick="return confirm(\'¿Está seguro que desea ACEPTAR el pedido #' . htmlspecialchars($pedido->id) . '?\')">Aceptar</button>
                                </form>
                                <form method="post" action="gestionar_pedidos.php" style="display:inline; margin-left:5px;">
                                    <input type="hidden" name="id_pedido_rechazar" value="' . htmlspecialchars($pedido->id) . '">
                                    <button type="submit" class="action-button" style="background-color:#dc3545;" onclick="return confirm(\'¿Está seguro que desea RECHAZAR el pedido #' . htmlspecialchars($pedido->id) . '?\')">Rechazar</button>
                                </form>
                            </td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6">No hay pedidos pendientes.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
