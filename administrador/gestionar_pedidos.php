<?php
// administrador/gestionar_pedidos.php
require_once '../config_sesion.php'; // Un nivel arriba para config_sesion.php

// Redirección si no es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 1) {
    header("Location: ../login_admin.php"); // Ajusta la ruta si es necesario
    exit();
}

require_once '../db.php'; // Un nivel arriba para db.php
require_once 'classes/GestorPedidos.php'; // Entra a classes desde administrador

$gestorPedidos = new GestorPedidos($pdo);

// CORRECCIÓN: Llamar al método correcto para obtener pedidos para el administrador
$pedidos = $gestorPedidos->obtenerPedidosAdministrador();

// Manejo de errores para la carga inicial de PHP si el método de la clase devuelve un array vacío en caso de error
if (empty($pedidos) && !is_array($pedidos)) { // Si $pedidos es false o no es un array esperado (aunque el método devuelve [])
    $initial_load_error = "No se pudieron cargar los pedidos. Verifique los logs del servidor.";
} else {
    $initial_load_error = null;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos - Admin</title>
    <link rel="icon" href="imagenes/AB.ico" type="image/x-icon">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        /* CSS general para el panel de administración (igual que en gestionar_usuarios.php) */
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar {
            background-color: #34495e;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar .brand {
            font-size: 1.8em;
            font-weight: bold;
            color: white;
            text-decoration: none;
            margin-right: auto;
        }
        .container-wrapper {
            display: flex;
            flex-grow: 1;
            width: 100%;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #ecf0f1;
            font-size: 1.8em;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 15px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
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
        .main-content {
            flex-grow: 1;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            margin: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        .data-table th, .data-table td {
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
        .data-table .edit-button { /* Para "Cambiar Estado" */
            background-color: #ffc107;
        }
        .data-table .edit-button:hover {
            background-color: #e0a800;
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
            display: block;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .logout-button:hover {
            background-color: #d32f2f;
        }
        /* Estilos para el modal de detalles de pedido */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto; /* 10% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
        }
        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
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
            <h2>Gestión de Pedidos</h2>
            <p>Aquí se mostrará una lista de pedidos. Al hacer clic en un pedido, se abrirán sus detalles.</p>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Código Pedido</th>
                        <th>Cliente</th>
                        <th>Fecha Compra</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="pedidos-table-body">
                    <?php
                    // Renderizar pedidos obtenidos por PHP
                    if ($initial_load_error) {
                        echo '<tr><td colspan="6" style="color:red;">' . htmlspecialchars($initial_load_error) . '</td></tr>';
                    } elseif (is_array($pedidos) && !empty($pedidos)) {
                        foreach ($pedidos as $pedido) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($pedido->id) . '</td>'; // Acceso como objeto
                            echo '<td>' . htmlspecialchars($pedido->codigo_pedido) . '</td>'; // Acceso como objeto
                            echo '<td>' . htmlspecialchars($pedido->id_cliente) . ' - ' . htmlspecialchars($pedido->nombre_cliente ?? 'N/A') . '</td>'; // Acceso como objeto
                            echo '<td>' . htmlspecialchars($pedido->fecha_compra ? (new DateTime($pedido->fecha_compra))->format('d/m/Y') : 'N/A') . '</td>'; // Acceso como objeto
                            echo '<td>' . htmlspecialchars($pedido->estado_pedido_texto) . '</td>'; // Acceso como objeto
                            echo '<td>';
                            echo '<button class="action-button" data-id="' . htmlspecialchars($pedido->id) . '" data-action="view-order">Ver Detalles</button>';
                            echo '<button class="action-button edit-button" data-id="' . htmlspecialchars($pedido->id) . '" data-action="change-order-status">Cambiar Estado</button>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6">No hay pedidos disponibles.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>

            <div id="detalle-pedido-modal" class="modal">
                <div class="modal-content">
                    <span class="close-button" id="close-order-details-modal">&times;</span>
                    <h3>Detalles del Pedido: <span id="codigo_pedido_detalle"></span></h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detalles-productos-body">
                            </tbody>
                    </table>
                    <button type="button" class="action-button" id="close-order-details-btn">Cerrar Detalles</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Obtener el modal y el botón de cierre
        const orderDetailsModal = document.getElementById('detalle-pedido-modal');
        const closeOrderDetailsBtn = document.getElementById('close-order-details-modal');
        const closeOrderDetailsFooterBtn = document.getElementById('close-order-details-btn');

        // Función para cargar/recargar pedidos (usada por AJAX)
        async function loadOrders() {
            try {
                // Ruta ajustada para la API
                const response = await fetch('api/orders/get_orders.php');
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
                }
                const result = await response.json();
                const tableBody = document.getElementById('pedidos-table-body');
                tableBody.innerHTML = ''; // Limpiar la tabla

                if (result.error) {
                    tableBody.innerHTML = `<tr><td colspan="6" style="color:red;">${result.error}</td></tr>`;
                    return;
                }

                if (result.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="6">No hay pedidos disponibles.</td></tr>`;
                    return;
                }

                result.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.id}</td>
                        <td>${item.codigo_pedido}</td>
                        <td>${item.id_cliente} - ${item.nombre_cliente || 'N/A'}</td>
                        <td>${item.fecha_compra ? new Date(item.fecha_compra).toLocaleDateString() : 'N/A'}</td>
                        <td>${item.estado_pedido_texto}</td>
                        <td>
                            <button class="action-button" data-id="${item.id}" data-action="view-order">Ver Detalles</button>
                            <button class="action-button edit-button" data-id="${item.id}" data-action="change-order-status">Cambiar Estado</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
                attachOrderEventListeners(); // Volver a adjuntar eventos a los nuevos botones
            } catch (error) {
                console.error("Error al cargar pedidos:", error);
                document.getElementById('pedidos-table-body').innerHTML = `<tr><td colspan="6" style="color:red;">Error al cargar los pedidos: ${error.message}</td></tr>`;
            }
        }

        // Adjuntar eventos a los botones de la tabla de pedidos
        function attachOrderEventListeners() {
            document.querySelectorAll('#pedidos-table-body .action-button[data-action="view-order"]').forEach(button => {
                button.onclick = async function() {
                    const orderId = this.dataset.id;
                    const orderCode = this.closest('tr').children[1].textContent; // Obtener el código de pedido de la celda
                    await loadOrderDetails(orderId, orderCode);
                };
            });
            document.querySelectorAll('#pedidos-table-body .action-button[data-action="change-order-status"]').forEach(button => {
                button.onclick = async function() {
                    const orderId = this.dataset.id;
                    // Aquí podrías tener un select con opciones de estado, por ahora un prompt
                    const newStatus = prompt('Ingrese el nuevo ID de estado para el pedido ' + orderId + ' (ej. 1: Pendiente, 2: Procesando, 3: Enviado, 4: Entregado, 5: Cancelado):');
                    if (newStatus && !isNaN(newStatus)) {
                        await changeOrderStatus(orderId, newStatus);
                    } else if (newStatus !== null) {
                        alert('Por favor, ingrese un número válido para el estado.');
                    }
                };
            });
        }

        // Función para cargar detalles del pedido en el modal
        async function loadOrderDetails(orderId, orderCode) {
            // Ruta ajustada para la API
            const url = `api/orders/get_order_details.php?id_pedido=${orderId}`;
            try {
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                const detallesBody = document.getElementById('detalles-productos-body');
                detallesBody.innerHTML = ''; // Limpiar detalles anteriores
                document.getElementById('codigo_pedido_detalle').textContent = orderCode; // Mostrar el código de pedido

                if (data.error) {
                    detallesBody.innerHTML = `<tr><td colspan="4" style="color:red;">${data.error}</td></tr>`;
                } else if (data.length === 0) {
                    detallesBody.innerHTML = `<tr><td colspan="4">No hay productos en este pedido.</td></tr>`;
                } else {
                    data.forEach(item => {
                        const row = document.createElement('tr');
                        // CORRECCIÓN: Usar las columnas correctas y calcular el subtotal
                        const subtotal = (parseFloat(item.cantidad_pedida) * parseFloat(item.precio_unitario_detalle)).toFixed(2);
                        row.innerHTML = `
                            <td>${item.nombre_producto}</td>
                            <td>${item.cantidad_pedida}</td>
                            <td>₡${parseFloat(item.precio_unitario_detalle).toFixed(2)}</td>
                            <td>₡${subtotal}</td>
                        `;
                        detallesBody.appendChild(row);
                    });
                }
                orderDetailsModal.style.display = 'block'; // Mostrar el modal
            } catch (error) {
                console.error('Error al cargar detalles del pedido:', error);
                alert('Error al cargar los detalles del pedido: ' + error.message);
            }
        }

        // Función para cambiar el estado del pedido
        async function changeOrderStatus(orderId, newStatus) {
            try {
                const formData = new FormData();
                formData.append('id_pedido', orderId);
                formData.append('nuevo_estado_id', newStatus);

                // Ruta ajustada para la API
                const response = await fetch('api/orders/update_order_status.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    alert('Estado del pedido actualizado con éxito.');
                    loadOrders(); // Recargar la tabla de pedidos
                } else {
                    alert('Error al cambiar el estado del pedido: ' + result.message);
                }
            } catch (error) {
                console.error('Error al cambiar estado del pedido:', error);
                alert('Error de comunicación al cambiar el estado del pedido.');
            }
        }

        // Eventos para cerrar el modal
        closeOrderDetailsBtn.onclick = function() {
            orderDetailsModal.style.display = 'none';
        }
        closeOrderDetailsFooterBtn.onclick = function() {
            orderDetailsModal.style.display = 'none';
        }
        window.onclick = function(event) {
            if (event.target == orderDetailsModal) {
                orderDetailsModal.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            
            attachOrderEventListeners();
        });
    </script>
</body>
</html>