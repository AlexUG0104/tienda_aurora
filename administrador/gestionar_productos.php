<?php
// administrador/gestionar_productos.php
require_once '../config_sesion.php'; // Un nivel arriba para config_sesion.php
require_once 'classes/GestorProductos.php';

// Redirección si no es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 1) {
    header("Location: ../login_admin.php"); // Ajusta la ruta si es necesario
    exit();
}

require_once '../db.php'; // Un nivel arriba para db.php
require_once 'classes/GestorProductos.php'; // Entra a classes desde administrador

$gestorProductos = new GestorProductos($pdo);
$productos = $gestorProductos->obtenerProductos(); // Para cargar la tabla inicial
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Admin</title>
    <link rel="icon" href="imagenes/AB.ico" type="image/x-icon">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        /* CSS general para el panel de administración (igual que en las otras páginas) */
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
        input[type="number"],
        input[type="file"],
        textarea,
        select {
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
        .product-image-preview {
            max-width: 80px;
            max-height: 80px;
            border-radius: 5px;
            vertical-align: middle;
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
                    <li><a href="gestionar_pedidos.php" class="nav-link"><i class="fas fa-clipboard-list"></i> Gestión de Pedidos</a></li>
                    <li><a href="gestionar_productos.php" class="nav-link active"><i class="fas fa-box-open"></i> Gestión de Productos</a></li>
                    <li><a href="ver_ventas.php" class="nav-link"><i class="fas fa-chart-line"></i> Ver Ventas</a></li>
                </ul>
            </nav>
            <a href="../logout_admin.php" class="logout-button"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>

        <div class="main-content">
            <h2>Gestión de Productos e Inventario</h2>
            <div class="form-container">
                <h3>Agregar/Editar Producto</h3>
                <form id="form-producto" enctype="multipart/form-data">
                    <input type="hidden" id="producto_id" name="id">

                    <label for="nombre_producto">Nombre del Producto:</label>
                    <input type="text" id="nombre_producto" name="nombre" required placeholder="Nombre del producto">

                    <label for="descripcion_producto">Descripción:</label>
                    <textarea id="descripcion_producto" name="descripcion" rows="4" placeholder="Descripción del producto"></textarea>

                    <label for="precio_producto">Precio:</label>
                    <input type="number" id="precio_producto" name="precio_unitario" step="0.01" required placeholder="0.00">


                    <label for="stock_producto">Stock:</label>
                    <input type="number" id="stock_producto" name="stock" required placeholder="0">

                    <label for="imagen_producto">Imagen del Producto:</label>
                    <input type="file" id="imagen_producto" name="imagen" accept="image/*">
                    <img id="imagen_preview" src="" alt="Previsualización de imagen" class="product-image-preview" style="display: none; margin-top: 10px;">
                    <p style="font-size: 0.8em; color: #666;">Selecciona una nueva imagen si quieres cambiarla. Deja vacío para mantener la actual.</p>

                    <input type="submit" value="Guardar Producto">
                </form>
            </div>

            <div class="product-list">
                <h3>Productos Existentes</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Imagen</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="productos-table-body">
                        <?php
                        // Renderizar productos obtenidos por PHP
                        if (is_array($productos) && !isset($productos['error'])) {
                            foreach ($productos as $producto) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($producto['id']) . '</td>';
                                echo '<td>' . htmlspecialchars($producto['nombre']) . '</td>';
                                echo '<td>₡' . htmlspecialchars(number_format($producto['precio'], 2)) . '</td>';
                                echo '<td>' . htmlspecialchars($producto['stock']) . '</td>';
                                echo '<td>';
                                if (!empty($producto['imagen_url'])) {
                                    echo '<img src="' . htmlspecialchars($producto['imagen_url']) . '" alt="Producto" class="product-image-preview">';
                                } else {
                                    echo 'N/A';
                                }
                                echo '</td>';
                                echo '<td>';
                                echo '<button class="action-button edit-button" data-id="' . htmlspecialchars($producto['id']) . '" data-action="edit-product">Editar</button>';
                                echo '<button class="action-button delete-button" data-id="' . htmlspecialchars($producto['id']) . '" data-action="delete-product">Eliminar</button>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } elseif (isset($productos['error'])) {
                            echo '<tr><td colspan="6" style="color:red;">' . htmlspecialchars($productos['error']) . '</td></tr>';
                        } else {
                            echo '<tr><td colspan="6">No hay productos disponibles.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Función para cargar/recargar productos
        async function loadProducts() {
            try {
                // Ruta ajustada para la API
                const response = await fetch('api/products/get_products.php');
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
                }
                const result = await response.json();
                const tableBody = document.getElementById('productos-table-body');
                tableBody.innerHTML = ''; // Limpiar la tabla

                if (result.error) {
                    tableBody.innerHTML = `<tr><td colspan="6" style="color:red;">${result.error}</td></tr>`;
                    return;
                }

                if (result.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="6">No hay productos disponibles.</td></tr>`;
                    return;
                }

                result.forEach(item => {
                    const row = document.createElement('tr');
                    const imageUrl = item.imagen_url ? `../${item.imagen_url}` : ''; // Ajustar la ruta de la imagen si está fuera de 'administrador/'
                    const imageHtml = imageUrl ? `<img src="${imageUrl}" alt="Producto" class="product-image-preview">` : 'N/A';

                    row.innerHTML = `
                        <td>${item.id}</td>
                        <td>${item.nombre}</td>
                        <td>₡${parseFloat(item.precio).toFixed(2)}</td>
                        <td>${item.stock}</td>
                        <td>${imageHtml}</td>
                        <td>
                            <button class="action-button edit-button" data-id="${item.id}" data-action="edit-product">Editar</button>
                            <button class="action-button delete-button" data-id="${item.id}" data-action="delete-product">Eliminar</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
                attachProductEventListeners(); // Volver a adjuntar eventos a los nuevos botones
            } catch (error) {
                console.error("Error al cargar productos:", error);
                document.getElementById('productos-table-body').innerHTML = `<tr><td colspan="6" style="color:red;">Error al cargar los productos: ${error.message}</td></tr>`;
            }
        }

        // Adjuntar eventos a los botones de la tabla de productos
        function attachProductEventListeners() {
            document.querySelectorAll('#productos-table-body .action-button[data-action="edit-product"]').forEach(button => {
                button.onclick = async function() {
                    const productId = this.dataset.id;
                    await editProduct(productId);
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
        }

        // Función para editar producto (cargar datos en el formulario)
        async function editProduct(productId) {
            try {
                // Ruta ajustada para la API
                const response = await fetch(`api/products/get_products.php?id=${productId}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const product = await response.json();

                if (product && !product.error) {
                    document.getElementById('producto_id').value = product.id;
                    document.getElementById('nombre_producto').value = product.nombre;
                    document.getElementById('descripcion_producto').value = product.descripcion;
                    document.getElementById('precio_producto').value = parseFloat(product.precio_unitario).toFixed(2);
                    document.getElementById('stock_producto').value = product.stock;

                    const imagePreview = document.getElementById('imagen_preview');
                    if (product.imagen_url) {
                        imagePreview.src = `../${product.imagen_url}`; // Ajustar la ruta para la previsualización
                        imagePreview.style.display = 'block';
                    } else {
                        imagePreview.style.display = 'none';
                        imagePreview.src = '';
                    }

                    document.querySelector('#form-producto input[type="submit"]').value = 'Actualizar Producto';
                } else {
                    alert('Producto no encontrado o error: ' + (product.error || ''));
                }
            } catch (error) {
                console.error('Error al cargar datos del producto para edición:', error);
                alert('Error al cargar datos del producto.');
            }
        }

        // Función para eliminar producto
        async function deleteProduct(productId) {
            try {
                const formData = new FormData();
                formData.append('id', productId);
                // Ruta ajustada para la API
                const response = await fetch('api/products/delete_product.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    alert('Producto eliminado con éxito.');
                    loadProducts(); // Recargar la tabla
                } else {
                    alert('Error al eliminar el producto: ' + result.message);
                }
            } catch (error) {
                console.error('Error al eliminar producto:', error);
                alert('Error de comunicación al eliminar el producto.');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Manejo del formulario de producto (agregar/editar)
            document.getElementById('form-producto').addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const productId = document.getElementById('producto_id').value;

                let url;
                let method = 'POST'; // Siempre POST para FormData

                if (productId) {
                    // Editar producto existente
                    url = 'api/products/update_product.php'; // Ruta ajustada
                } else {
                    // Nuevo producto
                    url = 'api/products/insert_product.php'; // Ruta ajustada
                }

                try {
                    const response = await fetch(url, {
                        method: method,
                        body: formData // FormData automáticamente establece el Content-Type adecuado
                    });
                    const result = await response.json();
                    if (result.success) {
                        alert(productId ? 'Producto actualizado con éxito.' : 'Producto agregado con éxito.');
                        this.reset(); // Limpiar formulario
                        document.getElementById('producto_id').value = ''; // Limpiar ID oculto
                        document.querySelector('#form-producto input[type="submit"]').value = 'Guardar Producto';
                        document.getElementById('imagen_preview').style.display = 'none'; // Ocultar previsualización
                        document.getElementById('imagen_preview').src = '';
                        loadProducts(); // Recargar la tabla
                    } else {
                        alert('Error al guardar el producto: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error al enviar formulario de producto:', error);
                    alert('Error de comunicación al guardar el producto.');
                }
            });

            // Previsualización de la imagen al seleccionarla
            document.getElementById('imagen_producto').addEventListener('change', function() {
                const preview = document.getElementById('imagen_preview');
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.src = '';
                    preview.style.display = 'none';
                }
            });

            // Al cargar la página, se adjuntan los eventos a los productos ya cargados por PHP
            attachProductEventListeners();
        });
    </script>
</body>
</html>