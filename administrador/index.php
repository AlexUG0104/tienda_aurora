<?php
//administrador/index.php
ini_set('session.gc_maxlifetime', 1440);
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once '../db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Mi Tienda</title>
    <link rel="icon" href="imagenes/AB.ico" type="image/x-icon">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        .content-section {
            display: none;
        }
        .content-section.active {
            display: block;
        }
        .form-container {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
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
        input:required:valid, select:required:valid, textarea:required:valid {
            border-color: green;
        }
        input:required:invalid, select:required:invalid, textarea:required:invalid {
            border-color: red;
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
        .data-table .edit-button {
            background-color: #ffc107;
        }
        .data-table .edit-button:hover {
            background-color: #e0a800;
        }
        .data-table .delete-button {
            background-color: #dc3545;
        }
        .data-table .delete-button:hover {
            background-color: #c82333;
        }
        .data-table .add-button {
            background-color: #28a745;
            margin-bottom: 15px;
            padding: 10px 15px;
            display: inline-block;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            font-weight: bold;
        }
        .data-table .add-button:hover {
            background-color: #218838;
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
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0,0,0,0.4); 
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
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
        <a href="../logout_admin.php" class="brand">Aurora Boutique</a>
        </nav>
    <div class="container-wrapper">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <nav>
                <ul>
                    <li><a href="#" class="nav-link active" data-target="usuarios">Gestión de Usuarios</a></li>
                    <li><a href="#" class="nav-link" data-target="pedidos">Gestión de Pedidos</a></li>
                    <li><a href="#" class="nav-link" data-target="inventario">Control de Inventario</a></li>
                </ul>
            </nav>
            <a href="../logout_admin.php" class="logout-button">Cerrar Sesión</a>
        </div>

        <div class="main-content">
            <div id="usuarios" class="content-section active">
                <h2>Gestión de Usuarios</h2>
                <div class="form-container">
                    <h3>Registrar Nuevo Usuario</h3>
                    <form id="form-registro-usuario" action="procesar_usuario.php" method="post">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" required placeholder="Ingrese el nombre completo">

                        <label for="clave">Clave:</label>
                        <input type="password" id="clave" name="clave" required placeholder="Ingrese la clave">

                        <label for="tipo_usuario_select">Tipo de Usuario:</label>
                        <select id="tipo_usuario_select" name="tipo_usuario_select" required>
                            <option value="">-- Seleccione un tipo --</option>
                            <option value="1">Administrador</option>
                            <option value="2">Cliente</option>
                            <option value="3">Personal de Envíos</option>
                        </select>

                        <input type="submit" value="Registrar Usuario">
                    </form>
                </div>
                <div class="user-list">
                    <h3>Usuarios Existentes</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="usuarios-table-body">
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="pedidos" class="content-section">
                <h2>Gestión de Pedidos</h2>
                <p>Aquí se mostrará una lista de pedidos. Al hacer clic en un pedido, se abrirán sus detalles.</p>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID Pedido</th>
                            <th>Código Pedido</th>
                            <th>Cliente (ID)</th>
                            <th>Fecha Compra</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="pedidos-table-body">
                    </tbody>
                </table>

                <div id="detalle-pedido-modal" class="form-container" style="display:none; margin-top: 30px;">
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
                    <button type="button" class="action-button" onclick="document.getElementById('detalle-pedido-modal').style.display='none';">Cerrar Detalles</button>
                </div>
            </div>
            <div id="inventario" class="content-section">
                <h2>Control de Inventario</h2>
                <p>Aquí se mostrarán los productos y sus detalles. Se podrán editar y agregar nuevos.</p>
                <a href="#" class="add-button" id="add-product-button">Agregar Nuevo Producto</a>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Stock</th>
                            <th>Precio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="productos-table-body">
                    </tbody>
                </table>
                <div id="product-form-modal" class="form-container" style="display:none; margin-top: 30px;">
                    <h3><span id="product-form-title">Agregar Nuevo Producto</span></h3>
                    <form id="form-producto">
                        <input type="hidden" id="product_id" name="id">
                        <label for="product_codigo">Código:</label>
                        <input type="text" id="product_codigo" name="codigo_producto" required>
                        <label for="product_nombre">Nombre:</label>
                        <input type="text" id="product_nombre" name="nombre" required>
                        <label for="product_descripcion">Descripción:</label>
                        <textarea id="product_descripcion" name="descripcion" style="width: calc(100% - 22px); padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-size: 16px;"></textarea>
                        <label for="product_precio">Precio Unitario:</label>
                        <input type="number" id="product_precio" name="precio_unitario" step="0.01" required>
                        <label for="product_stock">Stock:</label>
                        <input type="number" id="product_stock" name="stock" required>
                        <label for="product_talla">Talla:</label>
                        <input type="text" id="product_talla" name="talla">
                        <label for="product_categoria">ID Categoría:</label>
                        <input type="number" id="product_categoria" name="id_categoria">
                        <label for="product_color">Color:</label>
                        <input type="text" id="product_color" name="color">
                        <input type="submit" value="Guardar Producto">
                        <button type="button" class="action-button delete-button" onclick="document.getElementById('product-form-modal').style.display='none';">Cancelar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        async function loadContent(targetSection) {
            let url = '';
            let targetBodyId = ''; 

            if (targetSection === 'usuarios') {
                url = 'obtener_usuarios.php';
                targetBodyId = 'usuarios-table-body';
            } else if (targetSection === 'pedidos') {
                url = 'obtener_pedidos.php';
                targetBodyId = 'pedidos-table-body';
            } else if (targetSection === 'inventario') {
                url = 'obtener_productos.php';
                targetBodyId = 'productos-table-body';
            } else {
                console.error("Sección objetivo no reconocida:", targetSection);
                return;
            } 
            try {
                const response = await fetch(url);
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
                }
                const result = await response.json();
                let actualData = [];
                if (result.error) {
                    console.error(`Error del servidor para ${targetSection}:`, result.error);
                    actualData = result.data || [];
                    const tableBody = document.getElementById(targetBodyId);
                    let colspan = 5; 
                    if (targetSection === 'pedidos') colspan = 6; 
                    if (targetSection === 'inventario') colspan = 6; 
                    tableBody.innerHTML = `<tr><td colspan="${colspan}" style="color:red;">${result.error}</td></tr>`;
                    return;
                } else {
                    actualData = result; 
                }
                const tableBody = document.getElementById(targetBodyId);
                tableBody.innerHTML = ''; 
                if (actualData.length === 0) {
                    let colspan = 5; 
                    if (targetSection === 'pedidos') colspan = 6; 
                    if (targetSection === 'inventario') colspan = 6; 
                    tableBody.innerHTML = `<tr><td colspan="${colspan}">No hay datos disponibles.</td></tr>`;
                    return;
                }
                actualData.forEach(item => {
                    let row = document.createElement('tr');
                    if (targetSection === 'usuarios') {
                        row.innerHTML = `
                            <td>${item.id}</td>
                            <td>${item.nombre}</td>
                            <td>${item.nombre_tipo_usuario}</td>
                            <td>
                                <button class="action-button edit-button" data-id="${item.id}" data-action="edit-user">Editar</button>
                                <button class="action-button delete-button" data-id="${item.id}" data-action="delete-user">Eliminar</button>
                            </td>
                        `;
                    } else if (targetSection === 'pedidos') {
                        row.innerHTML = `
                            <td>${item.id}</td>
                            <td>${item.codigo_pedido}</td>
                            <td>${item.id_cliente} - ${item.nombre_cliente || 'N/A'}</td> <td>${item.fecha_compra ? new Date(item.fecha_compra).toLocaleDateString() : 'N/A'}</td>
                            <td>${item.estado_pedido_texto}</td> <td>
                                <button class="action-button" data-id="${item.id}" data-action="view-order">Ver Detalles</button>
                                <button class="action-button edit-button" data-id="${item.id}" data-action="change-order-status">Cambiar Estado</button>
                            </td>
                        `;
                    } else if (targetSection === 'inventario') {
                        row.innerHTML = `
                            <td>${item.id}</td>
                            <td>${item.codigo_producto}</td>
                            <td>${item.nombre}</td>
                            <td>${item.stock}</td>
                            <td>$${parseFloat(item.precio_unitario).toFixed(2)}</td>
                            <td>
                                <button class="action-button edit-button" data-id="${item.id}" data-action="edit-product">Editar</button>
                                <button class="action-button delete-button" data-id="${item.id}" data-action="delete-product">Eliminar</button>
                            </td>
                        `;
                    }
                    tableBody.appendChild(row);
                });
                attachEventListeners(targetSection);
            } catch (error) {
                console.error(`Error al cargar datos para ${targetSection}:`, error);
                const tableBody = document.getElementById(targetBodyId);
                let colspan = 5; 
                if (targetSection === 'pedidos') colspan = 6;
                if (targetSection === 'inventario') colspan = 6;
                tableBody.innerHTML = `<tr><td colspan="${colspan}" style="color:red;">Error al cargar los datos: ${error.message}</td></tr>`;
            }
        }
        function attachEventListeners(section) {
            if (section === 'pedidos') {
                document.querySelectorAll('#pedidos-table-body .action-button[data-action="view-order"]').forEach(button => {
                    button.onclick = async function() {
                        const orderId = this.dataset.id;
                        const orderCode = this.closest('tr').children[1].textContent;
                        await loadOrderDetails(orderId, orderCode);
                    };
                });
                document.querySelectorAll('#pedidos-table-body .action-button[data-action="change-order-status"]').forEach(button => {
                    button.onclick = function() {
                        const orderId = this.dataset.id;
                        alert('Funcionalidad de cambiar estado del pedido ID: ' + orderId + ' (pendiente de implementar)');
                    };
                });
            } else if (section === 'inventario') {
                document.querySelectorAll('#productos-table-body .action-button[data-action="edit-product"]').forEach(button => {
                    button.onclick = async function() {
                        const productId = this.dataset.id;
                        await getProductDetailsForEdit(productId);
                    };
                });
                document.querySelectorAll('#productos-table-body .action-button[data-action="delete-product"]').forEach(button => {
                    button.onclick = async function() {
                        const productId = this.dataset.id;
                        if (confirm('¿Está seguro de eliminar este producto? Esta acción es irreversible.')) {
                            await deleteProduct(productId);
                        }
                    };
                });
            } else if (section === 'usuarios') {
                document.querySelectorAll('#usuarios-table-body .action-button[data-action="edit-user"]').forEach(button => {
                    button.onclick = function() {
                        const userId = this.dataset.id;
                        alert('Funcionalidad de editar usuario ID: ' + userId + ' (pendiente de implementar)');
                    };
                });
                document.querySelectorAll('#usuarios-table-body .action-button[data-action="delete-user"]').forEach(button => {
                    button.onclick = function() {
                        const userId = this.dataset.id;
                        if (confirm('¿Está seguro de eliminar este usuario?')) {
                            alert('Funcionalidad de eliminar usuario ID: ' + userId + ' (pendiente de implementar)');
                        }
                    };
                });
            }
        }
        async function loadOrderDetails(orderId, orderCode) {
            const url = `obtener_detalle_pedido.php?id_pedido=${orderId}`;
            try {
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                const detallesBody = document.getElementById('detalles-productos-body');
                detallesBody.innerHTML = '';
                document.getElementById('codigo_pedido_detalle').textContent = orderCode;
                if (data.length === 0) {
                    detallesBody.innerHTML = '<tr><td colspan="4">No hay detalles para este pedido.</td></tr>';
                } else {
                    data.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${item.nombre_producto}</td>
                            <td>${item.cantidad}</td>
                            <td>$${parseFloat(item.precio_unitario).toFixed(2)}</td>
                            <td>$${parseFloat(item.total_linea).toFixed(2)}</td>
                        `;
                        detallesBody.appendChild(row);
                    });
                }
                document.getElementById('detalle-pedido-modal').style.display = 'block';
            } catch (error) {
                console.error(`Error al cargar detalles del pedido ${orderId}:`, error);
                document.getElementById('detalles-productos-body').innerHTML = `<tr><td colspan="4" style="color:red;">Error al cargar detalles: ${error.message}</td></tr>`;
            }
        }
        async function getProductDetailsForEdit(productId) {
            const url = 'obtener_productos.php';
            try {
                const response = await fetch(url);
                if (!response.ok) throw new Error('Error al obtener productos');
                const products = await response.json();
                const product = products.find(p => p.id == productId);
                if (product) {
                    document.getElementById('product-form-title').textContent = 'Editar Producto';
                    document.getElementById('product_id').value = product.id;
                    document.getElementById('product_codigo').value = product.codigo_producto;
                    document.getElementById('product_nombre').value = product.nombre;
                    document.getElementById('product_descripcion').value = product.descripcion || '';
                    document.getElementById('product_precio').value = parseFloat(product.precio_unitario).toFixed(2);
                    document.getElementById('product_stock').value = product.stock;
                    document.getElementById('product_talla').value = product.talla || '';
                    document.getElementById('product_categoria').value = product.id_categoria || '';
                    document.getElementById('product_color').value = product.color || '';

                    document.getElementById('product-form-modal').style.display = 'block';
                } else {
                    alert('Producto no encontrado.');
                }
            } catch (error) {
                console.error('Error al obtener detalles del producto para editar:', error);
                alert('No se pudieron cargar los detalles del producto para editar.');
            }
        }
        async function deleteProduct(productId) {
            if (confirm('¿Está seguro de eliminar este producto? Esta acción es irreversible.')) {
                try {
                    const formData = new FormData();
                    formData.append('id', productId);
                    const response = await fetch('eliminar_producto.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    if (result.success) {
                        alert('Producto eliminado con éxito.');
                        loadContent('inventario'); 
                    } else {
                        alert('Error al eliminar el producto: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error al eliminar producto:', error);
                    alert('Error de comunicación al eliminar el producto.');
                }
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link');
            const contentSections = document.querySelectorAll('.content-section');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault(); 
                    navLinks.forEach(nav => nav.classList.remove('active'));
                    contentSections.forEach(section => section.classList.remove('active'));
                    this.classList.add('active');
                    const targetId = this.getAttribute('data-target');
                    document.getElementById(targetId).classList.add('active');
                    loadContent(targetId);
                });
            });
            loadContent('usuarios');
            document.getElementById('form-registro-usuario').addEventListener('submit', async function(e) {
                e.preventDefault(); 
                const formData = new FormData(this);
                try {
                    const response = await fetch('procesar_usuario.php', {
                        method: 'POST',
                        body: formData 
                    });
                    const result = await response.json(); 
                    if (result.success) {
                        alert('Usuario registrado con éxito. ID: ' + result.newUserId);
                        this.reset(); 
                        loadContent('usuarios'); 
                    } else {
                        alert('Error al registrar usuario: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error al enviar formulario de usuario:', error);
                    alert('Error de comunicación al registrar usuario.');
                }
            });
            document.getElementById('add-product-button').addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('product-form-title').textContent = 'Agregar Nuevo Producto';
                document.getElementById('form-producto').reset(); 
                document.getElementById('product_id').value = ''; 
                document.getElementById('product-form-modal').style.display = 'block';
            });
            document.getElementById('form-producto').addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const productId = formData.get('id'); 
                let url = '';
                let successMessage = '';
                let errorMessage = '';
                if (productId) { url = 'actualizar_producto.php';
                    successMessage = 'Producto actualizado con éxito.';
                    errorMessage = 'Error al actualizar el producto: ';} else {  url = 'insertar_producto.php';
                    successMessage = 'Producto agregado con éxito. ID: ';
                    errorMessage = 'Error al agregar el producto: ';}try {
                    const response = await fetch(url, {
                        method: 'POST',
                        body: formData});
                    const result = await response.json();if (result.success) {
                        alert(successMessage + (result.newProductId ? result.newProductId : ''));
                        document.getElementById('product-form-modal').style.display = 'none'; 
                        loadContent('inventario'); } else {alert(errorMessage + result.message);}} catch (error) {
                    console.error('Error al enviar formulario de producto:', error);
                    alert('Error de comunicación al guardar el producto.');} }); });</script></body></html>