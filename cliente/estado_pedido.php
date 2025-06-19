<?php
require_once '../config_sesion.php';
require_once '../db.php';

// Solo clientes tipo 2
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: login_cliente.php?type=client");
    exit();
}

// Consultar todos los estados (opcional si quieres filtrar)
$stmtEstados = $pdo->query("SELECT id_estado, estado FROM pedido_estado");
$estados = $stmtEstados->fetchAll(PDO::FETCH_ASSOC);

// Pedidos del cliente
$stmtPedidos = $pdo->prepare("
    SELECT p.id, p.codigo_pedido, p.fecha_compra, p.fecha_entrega, e.estado
    FROM pedido p
    JOIN pedido_estado e ON p.estado_pedido = e.id_estado
    WHERE p.id_cliente = :id_cliente
    ORDER BY p.fecha_compra DESC
");
$stmtPedidos->execute([':id_cliente' => $_SESSION['user_id']]);
$pedidos = $stmtPedidos->fetchAll(PDO::FETCH_ASSOC);

// Si se selecciona pedido, cargar detalles
$productosPedido = [];
if (isset($_GET['id_pedido'])) {
    $idPedido = $_GET['id_pedido'];

    // Validar pedido cliente y obtener detalles
    $stmtVal = $pdo->prepare("
        SELECT COUNT(*) FROM pedido WHERE id = :id AND id_cliente = :cliente
    ");
    $stmtVal->execute([':id' => $idPedido, ':cliente' => $_SESSION['user_id']]);
    if ($stmtVal->fetchColumn() > 0) {
        $stmtProductos = $pdo->prepare("
            SELECT p.nombre, dp.cantidad, dp.precio_unitario
            FROM detalle_pedido dp
            JOIN producto p ON dp.id_producto = p.id
            WHERE dp.id_pedido = :id_pedido
        ");
        $stmtProductos->execute([':id_pedido' => $idPedido]);
        $productosPedido = $stmtProductos->fetchAll(PDO::FETCH_ASSOC);
    } else {
        die("Pedido no válido.");
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8" />
<title>Estado de Mis Pedidos</title>
<link rel="icon" href="imagenes/AB.ico" type="image/x-icon">

<style>
    /* Aquí mantén tu estilo acorde a la paleta de Aurora Boutique */
    body { font-family: 'Montserrat', sans-serif; background: #f0f2f5; padding: 30px; }
    h1 { text-align:center; color: #2a7a2a; margin-bottom: 25px; }
    .pedido { background:#fff; padding:20px; margin-bottom:20px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
    .pedido-header { display:flex; justify-content:space-between; font-weight:600; font-size:1.1rem; color:#2a7a2a; cursor:pointer; }
    .estado { background:#2a7a2a; color:#fff; padding:5px 12px; border-radius:12px; text-transform:uppercase; font-size:0.8rem; }
    .detalle { display:none; margin-top:15px; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:10px; border-bottom:1px solid #ddd; text-align:left; }
    th { background:#e6f0e6; color:#2a7a2a; }
</style>
</head>
<body>

<h1>Estado de Mis Pedidos</h1>

<?php if (empty($pedidos)): ?>
    <p>No tienes pedidos registrados.</p>
<?php else: ?>
    <?php foreach ($pedidos as $pedido): ?>
        <div class="pedido" onclick="this.querySelector('.detalle').classList.toggle('show')">
            <div class="pedido-header">
                <div>Pedido: <strong><?=htmlspecialchars($pedido['codigo_pedido'])?></strong></div>
                <div class="estado"><?=htmlspecialchars($pedido['estado'])?></div>
                <div>Compra: <?=date('d/m/Y', strtotime($pedido['fecha_compra']))?></div>
                <div>Entrega: <?= $pedido['fecha_entrega'] ? date('d/m/Y', strtotime($pedido['fecha_entrega'])) : 'Pendiente' ?></div>
            </div>
            <div class="detalle">
                <?php if (isset($idPedido) && $idPedido == $pedido['id'] && !empty($productosPedido)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productosPedido as $producto): ?>
                                <tr>
                                    <td><?=htmlspecialchars($producto['nombre'])?></td>
                                    <td><?=intval($producto['cantidad'])?></td>
                                    <td>₡ <?=number_format($producto['precio_unitario'], 2, ',', '.')?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Haz clic para seleccionar y ver detalles.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
document.querySelectorAll('.pedido').forEach(pedido => {
    pedido.addEventListener('click', () => {
        const detalles = pedido.querySelector('.detalle');
        detalles.style.display = detalles.style.display === 'block' ? 'none' : 'block';
    });
});
</script>

</body>
</html>
