<?php
require_once '../config_sesion.php';
require_once '../db.php';

// Verificar que sea cliente logueado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: login_cliente.php?type=client");
    exit();
}

// Obtener productos disponibles (stock > 0)
try {
    $stmt = $pdo->query("SELECT id, codigo_producto, nombre, descripcion, precio_unitario, stock, color, talla FROM producto WHERE stock > 0 ORDER BY nombre");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener productos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Realizar Pedido - Aurora Boutique</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        nav {
            background-color: #abc1b2;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        nav .nav-left a, nav .nav-right a {
            color: #333;
            text-decoration: none;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            margin-left: 20px;
        }

        nav .nav-right {
            display: flex;
            align-items: center;
        }

        .main-content-wrapper {
            padding-top: 90px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            width: 90%;
            max-width: 1200px;
            margin-bottom: 40px;
        }

        .product-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
        }

        .product-card h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .product-card p {
            color: #555;
            margin-bottom: 10px;
        }

        .product-card input[type="number"] {
            width: 60px;
            padding: 6px;
            margin-bottom: 10px;
        }

        .product-card button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .product-card button:hover {
            background-color: #0056b3;
        }

        .cart {
            width: 90%;
            max-width: 800px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 40px;
        }

        .cart h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .cart table {
            width: 100%;
            border-collapse: collapse;
        }

        .cart table th, .cart table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        .cart table th {
            background-color: #f9f9f9;
        }

        .cart .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-top: 15px;
        }

        .cart button[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .cart button[type="submit"]:hover {
            background-color: #218838;
        }

        .remove-btn {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 5px;
            transition: background-color 0.3s ease;
        }

        .remove-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<nav>
    <div class="nav-left">
        <a href="../index.php"><i class="fas fa-store"></i> Aurora Boutique</a>
    </div>
    <div class="nav-right">
        <a href="mis_pedidos.php"><i class="fas fa-list"></i> Mis pedidos</a>
    </div>
</nav>

<div class="main-content-wrapper">

    <h1>Realizar Pedido</h1>

    <div class="products-grid">
        <?php foreach ($productos as $producto): ?>
            <div class="product-card">
                <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                <p><strong>Descripción:</strong> <?php echo htmlspecialchars($producto['descripcion']); ?></p>
                <p><strong>Precio:</strong> ₡<?php echo number_format($producto['precio_unitario'], 2); ?></p>
                <p><strong>Stock:</strong> <?php echo $producto['stock']; ?></p>
                <p><strong>Color:</strong> <?php echo htmlspecialchars($producto['color']); ?> | <strong>Talla:</strong> <?php echo htmlspecialchars($producto['talla']); ?></p>
                <input type="number" min="1" max="<?php echo $producto['stock']; ?>" value="1" id="qty-<?php echo $producto['id']; ?>">
                <button onclick="agregarAlCarrito(<?php echo $producto['id']; ?>, '<?php echo addslashes($producto['nombre']); ?>', <?php echo $producto['precio_unitario']; ?>)">Agregar</button>
            </div>
        <?php endforeach; ?>
    </div>

    <form class="cart" id="cart-form" action="procesar_pedido.php" method="post" onsubmit="return validarCarrito();">
        <h2>Resumen del Pedido</h2>
        <table id="cart-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Aquí se agregarán los productos con JS -->
            </tbody>
        </table>
        <div class="total">Total: ₡<span id="total">0.00</span></div>
        <button type="submit">Confirmar Pedido</button>
    </form>

</div>

<script>
    let carrito = [];

    function agregarAlCarrito(id, nombre, precio) {
        const qty = parseInt(document.getElementById('qty-' + id).value);

        if (qty <= 0) return;

        const existente = carrito.find(item => item.id === id);
        if (existente) {
            existente.cantidad += qty;
        } else {
            carrito.push({ id, nombre, precio, cantidad: qty });
        }

        actualizarCarrito();
    }

    function quitarDelCarrito(id) {
        carrito = carrito.filter(item => item.id !== id);
        actualizarCarrito();
    }

    function actualizarCarrito() {
        const tbody = document.querySelector("#cart-table tbody");
        tbody.innerHTML = "";
        let total = 0;

        carrito.forEach(item => {
            const subtotal = item.precio * item.cantidad;
            total += subtotal;

            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${item.nombre}</td>
                <td>${item.cantidad}</td>
                <td>₡${item.precio.toFixed(2)}</td>
                <td>₡${subtotal.toFixed(2)}</td>
                <td><button type="button" class="remove-btn" onclick="quitarDelCarrito(${item.id})">Quitar</button></td>
            `;

            tbody.appendChild(row);
        });

        document.getElementById('total').innerText = total.toFixed(2);

        // Actualizar campos hidden para el submit
        const form = document.getElementById('cart-form');
        form.querySelectorAll('input[type=hidden]').forEach(e => e.remove());
        carrito.forEach(item => {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "productos[]";
            input.value = `${item.id}|${item.cantidad}|${item.precio}`;
            form.appendChild(input);
        });
    }

    function validarCarrito() {
        if (carrito.length === 0) {
            alert("El carrito está vacío. Agrega productos antes de confirmar el pedido.");
            return false;
        }
        return true;
    }
</script>

</body>
</html>
