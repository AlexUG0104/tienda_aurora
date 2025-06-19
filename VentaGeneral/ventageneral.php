<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config_sesion.php';
require_once '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: ../cliente/login_cliente.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo - Aurora Boutique</title>
    <link rel="icon" href="imagenes/AB.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background-color: #f5f5f5;
            padding-top: 90px;
        }
        nav {
            background: linear-gradient(135deg, #abc1b2 0%, #9bb4a3 100%);
            height: 70px;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 0 0 15px 15px;
            font-weight: 700;
            font-size: 1.2rem;
            color: #fff;
        }
        .nav-left a {
            color: #f1f1f1;
            text-decoration: none;
        }
        .nav-right {
            color: #f0f0f0;
            position: relative;
            left: -60px;
        }
        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 30px;
            margin: 40px auto;
            padding: 0 20px;
            max-width: 1200px;
        }
        .producto-card {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
            text-align: center;
            display: flex;
            flex-direction: column;
            padding: 0;
            height: 100%;
        }
        .producto-card img {
            width: 100%;
            height: 280px;
            object-fit: cover;
        }
        .producto-card h3,
        .producto-card p,
        .producto-card .precio {
            padding: 10px 20px 0;
        }
        .producto-card h3 {
            margin-top: 10px;
            font-size: 1.2rem;
            color: #444;
        }
        .producto-card p {
            color: #666;
            font-size: 0.95rem;
            margin: 10px 0 0;
            flex-grow: 1;
        }
        .producto-card .precio {
            color: #222;
            font-weight: bold;
            font-size: 1.1rem;
            margin-top: auto;
            padding-bottom: 10px;
        }
        .btn-comprar {
            background-color: #abc1b2;
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin: 10px auto 20px;
            width: 80%;
        }
        .btn-comprar:hover {
            background-color: #8ba996;
        }
        #carrito {
            position: fixed;
            top: 80px;
            right: 20px;
            background-color: #fff;
            border: 2px solid #abc1b2;
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            width: 250px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1001;
        }
        #carrito h4 {
            margin-top: 0;
            color: #333;
        }
        .carrito-item {
            font-size: 0.9rem;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .btn-realizar-compra {
            background-color: #6b8f71;
            color: white;
            border: none;
            padding: 8px 16px;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-realizar-compra:hover {
            background-color: #547459;
        }
        footer {
            background-color: #abc1b2;
            color: white;
            padding: 30px;
            text-align: center;
            margin-top: 60px;
        }
        .btn-eliminar {
            margin-left: 10px;
            color: #e74c3c;
            border: none;
            background: none;
            cursor: pointer;
            font-weight: bold;
            font-size: 1.1rem;
            line-height: 1;
            vertical-align: middle;
        }
        .btn-eliminar:hover {
            color: #c0392b;
        }
    </style>
</head>
<body>

<nav>
    <div class="nav-left">
        <form action="../cliente/logout_cliente.php" method="post" style="margin: 0;">
            <button type="submit" style="background: none; border: none; color: #f1f1f1; font-weight: 700; font-size: 1rem; cursor: pointer; padding: 0; font-family: 'Montserrat', sans-serif;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
    <div class="nav-right">Aurora Boutique</div>
</nav>

<div class="productos-grid">
    <?php
    try {
        $stmt = $pdo->prepare("SELECT * FROM obtener_productos()");
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<p>Error al obtener productos: " . htmlspecialchars($e->getMessage()) . "</p>";
        $productos = [];
    }
    ?>
</div>
<div class="productos-grid">
    <?php
    foreach ($productos as $p) {
        $nombre = htmlspecialchars($p['nombre']);
        $descripcion = htmlspecialchars($p['descripcion']);
        $precio = number_format($p['precio_unitario'], 2);
        $talla = htmlspecialchars($p['talla']);
        $url = htmlspecialchars($p['url_imagen']);
        $imagen = (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) ? $url : "../imagenes/general/" . $url;

        echo "
        <div class='producto-card'>
            <img src='$imagen' alt='$nombre'>
            <h3>$nombre</h3>
            <p>$descripcion</p>
            <div class='precio'>\$$precio</div>
            <div><small>Talla: $talla</small></div>
            <button class='btn-comprar' onclick=\"agregarAlCarrito('$nombre', $precio, '$talla')\">Comprar</button>
        </div>";
    }
    ?>
</div>

<!-- CARRITO FLOTANTE -->
<div id="carrito">
    <h4>🛒 Carrito</h4>
    <div id="carrito-items"></div>
    <button onclick="realizarCompra()" class="btn-realizar-compra">
        <i class="fas fa-shopping-cart"></i> Comprar ahora
    </button>
</div>

<!-- FORMULARIO OCULTO -->
<form id="formPedido" method="post" action="../cliente/realizar_pedido.php" style="display: none;">
    <input type="hidden" name="carrito" id="carritoInput">
</form>

<footer>
    <p>Aurora Boutique &copy; 2025. Todos los derechos reservados.</p>
</footer>

<script>
    const carrito = [];

    function agregarAlCarrito(nombre, precio, talla) {
        carrito.push({ nombre, precio, talla });
        renderizarCarrito();
    }

    function renderizarCarrito() {
        const carritoItems = document.getElementById("carrito-items");
        carritoItems.innerHTML = "";
        carrito.forEach((item, index) => {
            carritoItems.innerHTML += `
                <div class="carrito-item">
                    ${item.nombre} (Talla: ${item.talla}) - $${item.precio.toFixed(2)}
                    <button class="btn-eliminar" onclick="eliminarDelCarrito(${index})">✖</button>
                </div>`;
        });
    }

    function eliminarDelCarrito(index) {
        carrito.splice(index, 1);
        renderizarCarrito();
    }

    function realizarCompra() {
        if (carrito.length === 0) {
            alert("Tu carrito está vacío.");
            return;
        }

        const carritoInput = document.getElementById("carritoInput");
        carritoInput.value = JSON.stringify(carrito);
        document.getElementById("formPedido").submit();
    }
</script>

</body>
</html>
