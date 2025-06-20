<?php
// administrador/ver_ventas.php
require_once '../config_sesion.php';

// Habilitar reporte de errores para depuración (¡QUITAR EN PRODUCCIÓN!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 1) {
    header("Location: ../login_admin.php");
    exit();
}

require_once '../db.php';
require_once 'classes/GestorPedidos.php';

$gestorPedidos = new GestorPedidos($pdo);

// Filtros
$filtro_nombre = isset($_GET['nombre_cliente']) ? trim($_GET['nombre_cliente']) : '';
$filtro_id_cliente = isset($_GET['id_cliente']) ? trim($_GET['id_cliente']) : '';

$ventas = $gestorPedidos->obtenerPedidosAdministrador($filtro_nombre, $filtro_id_cliente);

// Clientes con grandes ventas (últimos 6 meses)
function obtenerClientesGrandesVentas($pdo) {
    $sql = "SELECT c.id AS cliente_id, c.nombre AS cliente_nombre, SUM(f.total) AS total_ventas
            FROM factura f
            JOIN pedido p ON f.id_pedido = p.id
            JOIN cliente c ON p.id_cliente = c.id
            WHERE f.fecha_emision >= CURRENT_DATE - INTERVAL '6 months'
            GROUP BY c.id, c.nombre
            HAVING SUM(f.total) > 200000 
            ORDER BY total_ventas DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

$clientesGrandesVentas = obtenerClientesGrandesVentas($pdo);

// Procesar solicitud para aplicar descuento a cliente (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cliente_id_descuento'])) {
    $clienteId = intval($_POST['cliente_id_descuento']);
    $stmt = $pdo->prepare("UPDATE cliente SET Cliente_Con_Descuento_Proxima_Facturacion = true WHERE id = ?");
    $stmt->execute([$clienteId]);
    $mensaje = "Descuento aplicado al cliente ID $clienteId";
    header("Location: ver_ventas.php?mensaje=" . urlencode($mensaje));
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Reporte de Ventas - Admin</title>
    <link rel="icon" href="imagenes/AB.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: "Montserrat", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
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
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar .brand {
            font-size: 1.8em;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }

        .sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            width: 250px;
            height: calc(100vh - 60px);
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #ecf0f1;
            font-size: 1.8em;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 15px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1;
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
        margin-top: auto;
        transform: translateY(-40px);
        display: block;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .logout-button:hover {
        background-color: #d32f2f;
    }

        .container-wrapper {
            margin-left: 250px;
            padding-top: 80px;
            min-height: 100vh;
        }

        .main-content {
            padding: 30px 60px 30px 60px;
            background-color: #ffffff;
            border-radius: 8px;
            margin: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .filter-form {
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-form input[type="text"] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            flex: 1 1 200px;
        }

        .filter-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .filter-form button:hover {
            background-color: #0056b3;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
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

        .btn-apply-discount {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-weight: 600;
        }

        .btn-apply-discount:hover {
            background-color: #218838;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 700px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }

        /* Mensaje éxito */
        .mensaje-exito {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
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
                <li><a href="gestionar_usuarios.php"><i class="fas fa-users"></i> Gestión de Usuarios</a></li>
                <li><a href="gestionar_pedidos.php"><i class="fas fa-clipboard-list"></i> Gestión de Pedidos</a></li>
                <li><a href="gestionar_productos.php"><i class="fas fa-box-open"></i> Gestión de Productos</a></li>
                <li><a href="ver_ventas.php" class="active"><i class="fas fa-chart-line"></i> Ver Ventas</a></li>
            </ul>
        </nav>
        <a href="../logout_admin.php" class="logout-button"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    </div>

    <div class="main-content">
        <h2>Reporte de Ventas</h2>

        <?php if (isset($_GET['mensaje'])): ?>
            <div style="background-color:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px; font-weight:bold;">
                <?= htmlspecialchars($_GET['mensaje']) ?>
            </div>
        <?php endif; ?>

        <form method="get" class="filter-form">
            <input type="text" name="nombre_cliente" placeholder="Nombre del cliente" value="<?= htmlspecialchars($filtro_nombre) ?>">
            <input type="text" name="id_cliente" placeholder="ID del cliente" value="<?= htmlspecialchars($filtro_id_cliente) ?>">
            <button type="submit">Filtrar</button>
        </form>

        <button id="btnMostrarGrandesVentas" style="background:#007bff; color:#fff; border:none; padding:10px 20px; border-radius:5px; cursor:pointer; font-weight:600; margin-bottom:20px;">
    Mostrar Grandes Ventas
</button>

        <table class="data-table">
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Código Pedido</th>
                    <th>Cliente (ID - Nombre)</th>
                    <th>Fecha Compra</th>
                    <th>Estado</th>
                    <th>Total Pedido</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($ventas)): ?>
                <?php foreach ($ventas as $venta): ?>
                    <tr>
                        <td><?= htmlspecialchars($venta->id ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($venta->codigo_pedido ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($venta->cliente_id ?? 'N/A') ?> - <?= htmlspecialchars($venta->cliente_nombre ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($venta->fecha_compra ? (new DateTime($venta->fecha_compra))->format('d/m/Y') : 'N/A') ?></td>
                        <td><?= htmlspecialchars($venta->estado_pedido_nombre ?? 'Desconocido') ?></td>
                        <td>₡<?= number_format($venta->total_pedido ?? 0, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No hay ventas disponibles o no coinciden con los filtros aplicados.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Grandes Ventas -->
<div id="modalGrandesVentas" class="modal">
  <div class="modal-content">
    <span class="close" id="cerrarModal">&times;</span>
    <h3>Clientes con Ventas > ₡200,000</h3>
    <?php if (!empty($clientesGrandesVentas)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID Cliente</th>
                    <th>Nombre</th>
                    <th>Total Ventas</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientesGrandesVentas as $cliente): ?>
                    <tr>
                        <td><?= htmlspecialchars($cliente->cliente_id) ?></td>
                        <td><?= htmlspecialchars($cliente->cliente_nombre) ?></td>
                        <td>₡<?= number_format($cliente->total_ventas, 2) ?></td>
                        <td>
                            <form method="post" style="margin:0;">
                                <input type="hidden" name="cliente_id_descuento" value="<?= htmlspecialchars($cliente->cliente_id) ?>">
                                <button type="submit" class="btn-apply-discount" onclick="return confirm('¿Aplicar descuento a este cliente?')">Aplicar Descuento</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay clientes con ventas mayores a ₡200,000.</p>
    <?php endif; ?>
  </div>
</div>

<script>
    const btnMostrar = document.getElementById('btnMostrarGrandesVentas');
    const modal = document.getElementById('modalGrandesVentas');
    const cerrar = document.getElementById('cerrarModal');

    btnMostrar.onclick = () => modal.style.display = 'block';
    cerrar.onclick = () => modal.style.display = 'none';
    window.onclick = e => { if (e.target == modal) modal.style.display = 'none'; };
</script>

</body>
</html>
