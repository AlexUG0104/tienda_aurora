<?php
require_once '../config_sesion.php';
require_once '../db.php';

// Verificar que sea cliente logueado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: login_cliente.php?type=client");
    exit();
}

// Recibir carrito desde formulario anterior
$carrito = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['carrito'])) {
    $carrito = json_decode($_POST['carrito'], true);
    if (!is_array($carrito)) {
        die("Error: formato de carrito inválido.");
    }
} else {
    die("No se recibieron productos para el pedido.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Resumen del Pedido - Aurora Boutique S.A.</title>
    <link rel="icon" href="../imagenes/AB.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        /* --- Tu CSS existente --- */
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
        nav a {
            color: #333;
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: 700;
        }
        .content {
            padding-top: 100px;
            max-width: 800px;
            margin: 0 auto 50px auto;
            background: white;
            padding: 25px 30px 40px 30px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        h1, h3 {
            margin-top: 60px;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        .total, .iva, .total-con-iva {
            text-align: right;
            font-size: 18px;
            font-weight: 700;
            margin-top: 20px;
            color: #27ae60;
        }
        .iva, .total-con-iva {
            margin-top: 5px;
            font-size: 16px;
            color: #16a085;
        }
        .cantidad-input {
            width: 60px;
            padding: 5px;
            font-size: 1rem;
        }
        .btn-confirmar {
            background-color: #28a745;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            display: block;
            margin: 30px auto 0;
            transition: background-color 0.3s ease;
        }
        .btn-confirmar:hover {
            background-color: #218838;
        }
        /* Inputs para dirección y comprobante */
        .form-group {
            margin: 15px 0;
        }
        label {
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
            color: #34495e;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
            box-sizing: border-box;
        }
        /* Ocultar inicialmente */
        #direccion-envio-container,
        #comprobante-envio-container {
            display: none;
        }
        /* Info empresa y métodos de pago */
        .info-pago {
            background-color: #e8f5e9;
            border: 1px solid #c8e6c9;
            padding: 15px 20px;
            margin-top: 25px;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #2e7d32;
        }
        .info-pago h4 {
            margin-top: 0;
        }
        /* Radios estilizados */
        .metodo-pago label {
            display: inline-block;
            margin-right: 20px;
            cursor: pointer;
            font-weight: 600;
            color: #2c3e50;
        }
        .metodo-pago input[type="radio"] {
            margin-right: 6px;
            transform: scale(1.2);
            vertical-align: middle;
        }
        /* Botón eliminar producto */
        .btn-eliminar {
            margin-left: 10px;
            color: #e74c3c;
            border: none;
            background: none;
            cursor: pointer;
            font-weight: bold;
            font-size: 1.3rem;
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
    <a href="../index.php"><i class="fas fa-store"></i> Aurora Boutique S.A.</a>
</nav>

<div class="content">
    <h1>Resumen del Pedido</h1>
    <form action="procesar_pago.php" method="post" id="pedido-form">
        <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Talla</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody id="pedido-body">
            <?php
            $total = 0;
            foreach ($carrito as $index => $item) {
            $id = htmlspecialchars($item['id']);
            $nombre = htmlspecialchars($item['nombre']);
            $talla = htmlspecialchars($item['talla'] ?? '');
            $precio = floatval($item['precio']);
            $cantidad = isset($item['cantidad']) ? intval($item['cantidad']) : 1;
            $subtotal = $precio * $cantidad;
            $total += $subtotal;
            echo "<tr data-index='{$index}' data-id='{$id}' data-nombre='{$nombre}' data-talla='{$talla}' data-precio='{$precio}'>
                    <td>
                    {$nombre}
                    <button type='button' class='btn-eliminar' onclick='eliminarProducto(this)' title='Eliminar producto'>×</button>
                    </td>
                    <td>{$talla}</td>
                    <td class='precio' data-precio='{$precio}'>₡" . number_format($precio, 2) . "</td>
                    <td>
                    <input type='number' class='cantidad-input' value='{$cantidad}' min='0' onchange='actualizarTotales()'>
                    </td>
                    <td class='subtotal'>₡" . number_format($subtotal, 2) . "</td>
                </tr>";
        }

            ?>
        </tbody>
        </table>

        <div class="total">
            Total estimado: ₡<span id="total"><?php echo number_format($total, 2); ?></span>
        </div>
        <div class="iva">
            IVA (13%): ₡<span id="iva"><?php echo number_format($total * 0.13, 2); ?></span>
        </div>
        <div class="total-con-iva">
            Total con IVA: ₡<span id="total-con-iva"><?php echo number_format($total * 1.13, 2); ?></span>
        </div>

        <h3>Información de Pago</h3>
        <div class="metodo-pago">
            <label><input type="radio" name="metodo_pago" value="tarjeta" required onchange="toggleDireccion()"> Tarjeta en tienda</label>
            <label><input type="radio" name="metodo_pago" value="sinpe" onchange="toggleDireccion()"> SINPE Móvil</label>
            <label><input type="radio" name="metodo_pago" value="transferencia" onchange="toggleDireccion()"> Transferencia</label>
            <label><input type="radio" name="metodo_pago" value="efectivo" onchange="toggleDireccion()"> Pago contra entrega</label>
        </div>

        <div id="direccion-envio-container" class="form-group">
            <label for="direccion_envio">Dirección de Envío *</label>
            <textarea id="direccion_envio" name="direccion_envio" rows="3" placeholder="Ingrese su dirección completa para el envío"></textarea>
        </div>

        <div id="comprobante-envio-container" class="form-group">
            <label for="comprobante_envio">Comprobante de Pago *</label>
            <textarea id="comprobante_envio" name="comprobante_envio" rows="3" placeholder="Ingrese el comprobante para completar el pago"></textarea>
        </div>

        <div class="info-pago">
            <h4>Datos para Transferencias y SINPE</h4>
            <p><strong>Nombre:</strong> Aurora Boutique S.A.</p>
            <p><strong>IBAN:</strong> CR12 3456 7890 1234 5678 90</p>
            <p><strong>Banco:</strong> Banco Nacional de Costa Rica</p>
            <p><strong>SINPE:</strong> 8580-3868 / 6007-8154</p>
        </div>

        <!-- CARRITO JSON ACTUALIZADO -->
        <input type="hidden" name="carrito" id="carrito-hidden">
        <button class="btn-confirmar" type="submit">Confirmar y Pagar</button>
    </form>
</div>

<script>
function obtenerCarritoActualizado() {
    const filas = document.querySelectorAll("#pedido-body tr");
    const carrito = [];

    filas.forEach(fila => {
        if (fila.style.display === "none") return;

        const id = fila.dataset.id;
        const nombre = fila.dataset.nombre;
        const talla = fila.dataset.talla;
        const precio = parseFloat(fila.dataset.precio);
        const cantidad = parseInt(fila.querySelector("input.cantidad-input").value);

        if (cantidad > 0) {
            carrito.push({ id, nombre, talla, precio, cantidad });
        }
    });

    return carrito;
}

function actualizarTotales() {
    const filas = document.querySelectorAll("#pedido-body tr");
    let total = 0;

    filas.forEach(fila => {
        if (fila.style.display === "none") return;

        const precio = parseFloat(fila.querySelector(".precio").dataset.precio);
        const cantidad = parseInt(fila.querySelector("input[type='number']").value);
        const subtotal = precio * cantidad;

        fila.querySelector(".subtotal").innerText = "₡" + subtotal.toFixed(2);
        total += subtotal;
    });

    document.getElementById("total").innerText = total.toFixed(2);
    document.getElementById("iva").innerText = (total * 0.13).toFixed(2);
    document.getElementById("total-con-iva").innerText = (total * 1.13).toFixed(2);

    document.getElementById("carrito-hidden").value = JSON.stringify(obtenerCarritoActualizado());
}

function eliminarProducto(btn) {
    const fila = btn.closest('tr');
    fila.querySelector("input.cantidad-input").value = 0;
    fila.style.display = 'none';
    actualizarTotales();
}

function toggleDireccion() {
    const metodo = document.querySelector('input[name="metodo_pago"]:checked').value;
    const direccion = document.getElementById("direccion-envio-container");
    const comprobante = document.getElementById("comprobante-envio-container");

    direccion.style.display = metodo === "efectivo" ? "none" : "block";
    comprobante.style.display = (metodo === "sinpe" || metodo === "transferencia") ? "block" : "none";
}

// Inicializar comportamiento
window.addEventListener('DOMContentLoaded', () => {
    actualizarTotales(); // Para cargar el carrito desde el inicio
});
</script>

</body>
</html>