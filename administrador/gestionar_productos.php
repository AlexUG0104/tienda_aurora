<?php
require_once '../config_sesion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 1) {
    header("Location: ../administrador/login.php");
    exit();
}

require_once '../db.php'; 
require_once 'classes/GestorProductos.php'; 


$gestorProductos = new GestorProductos($pdo);
$productos = $gestorProductos->obtenerProductos(); 
$categorias = $gestorProductos->obtenerCategorias();

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
    background-color: #34495e;
    color: white;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    flex-shrink: 0; 
}

.container-wrapper {
    display: flex;
    flex-grow: 1;
    width: 100%;
    height: calc(100vh - 60px); 
    overflow: hidden; 
}

.sidebar {
    width: 250px;
    background-color: #2c3e50;
    color: white;
    padding: 20px;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    overflow-y: auto; 
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
    margin-top: auto; 
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

.navbar .brand {
    font-size: 1.8em;
    font-weight: 700;
    color: white; 
    text-decoration: none;
    margin-right: auto;
    letter-spacing: 1.5px; 
    font-family: 'Montserrat', sans-serif; 
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
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
            display: block;
            margin: auto;
        }
        .product-imagen-preview {
            width: 50px;
            height: auto;
            border-radius: 4px;
            object-fit: contain;
        }

      
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0,0,0,0.6); 
            justify-content: center; 
            align-items: center; 
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto; 
            padding: 30px;
            border: 1px solid #888;
            width: 80%; 
            max-width: 600px; 
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 32px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 20px;
            cursor: pointer;
        }

        .close-button:hover,
        .close-button:focus {
            color: #000;
            text-decoration: none;
        }

        .modal-content h3 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        .modal-content input[type="text"],
        .modal-content input[type="number"],
        .modal-content textarea,
        .modal-content select {
            width: calc(100% - 22px); 
        }

        .modal-content input[type="submit"] {
            background-color: #007bff; 
            width: 100%;
        }

        .modal-content input[type="submit"]:hover {
            background-color: #0056b3;
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
                <h3>Agregar Nuevo Producto</h3>
                <form id="form-add-product" enctype="multipart/form-data">
                    <label for="add_codigo_producto">Código del Producto:</label>
                    <input type="text" id="add_codigo_producto" name="codigo_producto" required placeholder="Código único del producto">

                    <label for="add_nombre_producto">Nombre del Producto:</label>
                    <input type="text" id="add_nombre_producto" name="nombre" required placeholder="Nombre del producto">

                    <label for="add_descripcion_producto">Descripción:</label>
                    <textarea id="add_descripcion_producto" name="descripcion" rows="4" placeholder="Descripción del producto"></textarea>

                    <label for="add_precio_producto">Precio:</label>
                    <input type="number" id="add_precio_producto" name="precio_unitario" step="0.01" required placeholder="0.00">

                    <label for="add_stock_producto">Stock:</label>
                    <input type="number" id="add_stock_producto" name="stock" required placeholder="0">

                    <label for="add_talla_producto">Talla:</label>
                    <select id="add_talla_producto" name="talla">
                        <option value="">Seleccione una talla</option>
                        <option value="XS">XS</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        <option value="XXL">XXL</option>
                        <option value="U">U (Única)</option>
                    </select>

                    <label for="add_color_producto">Color:</label>
                    <input type="text" id="add_color_producto" name="color" placeholder="Ej: Rojo, Azul, Negro">

                    <label for="add_categoria_producto">Categoría:</label>
                    <select id="add_categoria_producto" name="id_categoria" required>
                        <option value="">Seleccione una categoría</option>
                        <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= htmlspecialchars($categoria['id']) ?>">
                            <?= htmlspecialchars($categoria['nombre_categoria']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="add_url_imagen">URL de la Imagen del Producto:</label>
                    <input type="text" id="add_url_imagen" name="url_imagen" placeholder="https://ejemplo.com/imagen.jpg">
                    <img id="add_imagen_preview" src="" alt="Previsualización de imagen" class="product-image-preview" style="display: none; margin-top: 10px;">
                    
                    <input type="submit" value="Agregar Producto">
                </form>
            </div>

            <div class="product-list">
                <h3>Productos Existentes</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="productos-table-body">
                        <?php
                        if (is_array($productos) && !isset($productos['error'])) {
                            foreach ($productos as $producto) {
                                echo '<tr>';
                                echo '<td>';
                                if (!empty($producto['url_imagen'])) {
                                   echo '<img src="' . htmlspecialchars($producto['url_imagen']) . '" alt="Producto" class="product-image-preview">';
                                } else {
                                    echo 'N/A';
                                }
                                echo '</td>';
                                echo '<td>' . htmlspecialchars($producto['id']) . '</td>';
                                echo '<td>' . htmlspecialchars($producto['nombre']) . '</td>';
                                echo '<td>$' . htmlspecialchars(number_format($producto['precio_unitario'], 2)) . '</td>';
                                echo '<td>' . htmlspecialchars($producto['stock']) . '</td>';
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

    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <span class="close-button" id="closeEditProductModal">&times;</span>
            <h3>Editar Producto</h3>
            <form id="form-edit-product" enctype="multipart/form-data">
                <input type="hidden" id="edit_producto_id" name="id">

                <label for="edit_codigo_producto">Código del Producto:</label>
                <input type="text" id="edit_codigo_producto" name="codigo_producto" required>

                <label for="edit_nombre_producto">Nombre del Producto:</label>
                <input type="text" id="edit_nombre_producto" name="nombre" required>

                <label for="edit_descripcion_producto">Descripción:</label>
                <textarea id="edit_descripcion_producto" name="descripcion" rows="4"></textarea>

                <label for="edit_precio_producto">Precio:</label>
                <input type="number" id="edit_precio_producto" name="precio_unitario" step="0.01" required>

                <label for="edit_stock_producto">Stock:</label>
                <input type="number" id="edit_stock_producto" name="stock" required>

                <label for="edit_talla_producto">Talla:</label>
                <select id="edit_talla_producto" name="talla">
                    <option value="">Seleccione una talla</option>
                    <option value="XS">XS</option>
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                    <option value="XXL">XXL</option>
                    <option value="U">U (Única)</option>
                </select>

                <label for="edit_color_producto">Color:</label>
                <input type="text" id="edit_color_producto" name="color">

                <label for="edit_categoria_producto">Categoría:</label>
                <select id="edit_categoria_producto" name="id_categoria" required>
                    <option value="">Seleccione una categoría</option>
                    <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= htmlspecialchars($categoria['id']) ?>">
                        <?= htmlspecialchars($categoria['nombre_categoria']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <label for="edit_url_imagen">URL de la Imagen del Producto:</label>
                <input type="text" id="edit_url_imagen" name="url_imagen">
                <img id="edit_imagen_preview" src="" alt="Previsualización de imagen" class="product-image-preview" style="display: none; margin-top: 10px;">

                <input type="submit" value="Actualizar Producto">
            </form>
        </div>
    </div>

    <script>
        const editProductModal = document.getElementById('editProductModal');
        const closeEditProductModalButton = document.getElementById('closeEditProductModal');
        const formEditProduct = document.getElementById('form-edit-product');

        const editProductoId = document.getElementById('edit_producto_id');
        const editCodigoProducto = document.getElementById('edit_codigo_producto');
        const editNombreProducto = document.getElementById('edit_nombre_producto');
        const editDescripcionProducto = document.getElementById('edit_descripcion_producto');
        const editPrecioProducto = document.getElementById('edit_precio_producto');
        const editStockProducto = document.getElementById('edit_stock_producto');
        const editTallaProducto = document.getElementById('edit_talla_producto');
        const editColorProducto = document.getElementById('edit_color_producto');
        const editCategoriaProducto = document.getElementById('edit_categoria_producto');
        const editUrlImagen = document.getElementById('edit_url_imagen');
        const editImagenPreview = document.getElementById('edit_imagen_preview');


        function closeEditProductModal() {
            editProductModal.style.display = 'none';
            formEditProduct.reset(); 
            editProductoId.value = ''; 
            editImagenPreview.src = '';
            editImagenPreview.style.display = 'none';
        }

        closeEditProductModalButton.onclick = closeEditProductModal;
        window.onclick = function(event) {
            if (event.target == editProductModal) {
                closeEditProductModal();
            }
        }

        async function loadProducts() {
            try {
                const response = await fetch('api/products/get_products.php');
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
                }
                const result = await response.json();
                const tableBody = document.getElementById('productos-table-body');
                tableBody.innerHTML = '';

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
                    const imageUrl = item.url_imagen ? item.url_imagen : '';
                    const imageHtml = imageUrl? `<img src="${imageUrl}" alt="Producto" class="product-image-preview">` : 'N/A';

                    row.innerHTML = `
                        <td>${imageHtml}</td>
                        <td>${item.id}</td>
                        <td>${item.nombre}</td>
                        <td>$${parseFloat(item.precio_unitario).toFixed(2)}</td>
                        <td>${item.stock}</td>
                        <td>
                            <button class="action-button edit-button" data-id="${item.id}" data-action="edit-product">Editar</button>
                            <button class="action-button delete-button" data-id="${item.id}" data-action="delete-product">Eliminar</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
                attachProductEventListeners(); 
            } catch (error) {
                console.error("Error al cargar productos:", error);
                document.getElementById('productos-table-body').innerHTML = `<tr><td colspan="6" style="color:red;">Error al cargar los productos: ${error.message}</td></tr>`;
            }
        }

        function attachProductEventListeners() {
            document.querySelectorAll('#productos-table-body .action-button[data-action="edit-product"]').forEach(button => {
                button.onclick = async function() {
                    const productId = this.dataset.id;
                    await loadProductForEdit(productId); 
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

        async function loadProductForEdit(productId) {
            try {
                const response = await fetch(`api/products/get_product_by_id.php?id=${productId}`);
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
                }
                const productData = await response.json();

                if (productData.error) {
                    alert('Error al cargar datos del producto: ' + productData.error);
                    return;
                }

                editProductoId.value = productData.id;
                editCodigoProducto.value = productData.codigo_producto;
                editNombreProducto.value = productData.nombre;
                editDescripcionProducto.value = productData.descripcion || ''; 
                editPrecioProducto.value = parseFloat(productData.precio_unitario).toFixed(2);
                editStockProducto.value = productData.stock;
                editTallaProducto.value = productData.talla || '';
                editColorProducto.value = productData.color || '';
                editCategoriaProducto.value = productData.id_categoria; 
                if (productData.url_imagen) {
                    editUrlImagen.value = productData.url_imagen;
                    editImagenPreview.src = productData.url_imagen;
                    editImagenPreview.style.display = 'block';
                } else {
                    editUrlImagen.value = '';
                    editImagenPreview.src = '';
                    editImagenPreview.style.display = 'none';
                }
                
                editProductModal.style.display = 'flex'; 
            } catch (error) {
                console.error('Error al cargar datos del producto para edición:', error);
                alert('Error de comunicación al cargar datos del producto para edición: ' + error.message);
            }
        }

        async function deleteProduct(productId) {
            try {
                const formData = new FormData();
                formData.append('id', productId);
                const response = await fetch('api/products/delete_product.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    alert('Producto eliminado con éxito.');
                    loadProducts(); 
                } else {
                    alert('Error al eliminar el producto: ' + result.message);
                }
            } catch (error) {
                console.error('Error al eliminar producto:', error);
                alert('Error de comunicación al eliminar el producto.');
            }
        }

        document.getElementById('form-add-product').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const url = 'api/products/insert_product.php'; 

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    alert('Producto agregado con éxito.');
                    this.reset(); 
                    document.getElementById('add_imagen_preview').style.display = 'none'; 
                    document.getElementById('add_imagen_preview').src = '';
                    loadProducts(); 
                } else {
                    alert('Error al agregar el producto: ' + result.message);
                }
            } catch (error) {
                console.error('Error al enviar formulario de producto:', error);
                alert('Error de comunicación al agregar el producto.');
            }
        });

        formEditProduct.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const url = 'api/products/update_product.php';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    alert('Producto actualizado con éxito.');
                    closeEditProductModal(); 
                    loadProducts(); 
                } else {
                    alert('Error al actualizar producto: ' + result.message);
                }
            } catch (error) {
                console.error('Error al enviar formulario de edición de producto:', error);
                alert('Error de comunicación al actualizar producto.');
            }
        });

        document.getElementById('add_url_imagen').addEventListener('input', function () {
            const preview = document.getElementById('add_imagen_preview');
            const url = this.value.trim();
            if (url) {
                preview.src = url;
                preview.style.display = 'block';
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        });

        document.getElementById('edit_url_imagen').addEventListener('input', function () {
            const preview = document.getElementById('edit_imagen_preview');
            const url = this.value.trim();
            if (url) {
                preview.src = url;
                preview.style.display = 'block';
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        });


        document.addEventListener('DOMContentLoaded', function() {
            attachProductEventListeners();
            document.getElementById('form-add-product').reset();
        });
    </script>
</body>
</html>
