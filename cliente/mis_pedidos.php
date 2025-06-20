<?php
require_once '../config_sesion.php';
require_once '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: login_cliente.php");
    exit;
}

$idCliente = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM obtener_pedidos_cliente(:id_cliente)");
    $stmt->execute(['id_cliente' => $idCliente]);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener pedidos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Pedidos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 30px;
            background: #f4f7f5;
        }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .btn-regresar { background-color: #abc1b2; color: white; padding: 8px 16px; border: none; border-radius: 6px; text-decoration: none; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: center; border: 1px solid #ddd; }
        th { background-color: #abc1b2; color: white; text-transform: uppercase; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .acciones { display: flex; flex-direction: column; gap: 6px; align-items: center; justify-content: center; }
        .btn-accion { background-color: #6b8f71; color: white; padding: 6px 10px; font-size: 13px; border-radius: 5px; text-decoration: none; border: none; cursor: pointer; }
        .btn-accion:hover { background-color: #547459; }
        .modal {
            display: none; position: fixed; z-index: 1000; left: 0; top: 0;
            width: 100%; height: 100%; overflow: auto;
            background-color: rgba(0,0,0,0.4);
            animation: fadeIn 0.3s ease-in-out;
        }
        .modal-content {
            background-color: #fff; margin: 5% auto; padding: 20px;
            border: 1px solid #888; width: 60%; border-radius: 8px;
            position: relative;
            animation: slideDown 0.4s ease;
        }
        .close {
            color: white; background: crimson;
            position: absolute; top: -14px; right: -14px;
            font-size: 24px; font-weight: bold;
            width: 36px; height: 36px;
            text-align: center; line-height: 36px;
            border-radius: 50%; cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
        .toast {
            display: none;
            position: fixed;
            bottom: 40px;
            right: 40px;
            background-color: #28a745;
            color: white;
            padding: 16px 24px;
            border-radius: 8px;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            z-index: 9999;
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeIn {
            from {opacity: 0;} to {opacity: 1;}
        }
        @keyframes slideDown {
            from {transform: translateY(-40px); opacity: 0;}
            to {transform: translateY(0); opacity: 1;}
        }
        @keyframes fadeInUp {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>

<div class="top-bar">
    <h2>📦 Mis Pedidos</h2>
    <a class="btn-regresar" href="../VentaGeneral/ventageneral.php"><i class="fas fa-arrow-left"></i> Regresar</a>
</div>

<div id="toast" class="toast">🎉 ¡Reseña enviada con éxito!</div>

<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Fecha de Compra</th>
            <th>Fecha de Entrega</th>
            <th>Estado</th>
            <th>Total</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pedidos as $pedido): ?>
            <tr>
                <td><?= htmlspecialchars($pedido['codigo_pedido']) ?></td>
                <td><?= htmlspecialchars($pedido['fecha_compra']) ?></td>
                <td><?= $pedido['fecha_entrega'] ? htmlspecialchars($pedido['fecha_entrega']) : 'Pendiente' ?></td>
                <td><?= htmlspecialchars($pedido['estado']) ?></td>
                <td>₡<?= number_format($pedido['total_factura'] ?? 0, 2) ?></td>
                <td>
                    <div class="acciones">
                        <?php if (!is_null($pedido['total_factura'])): ?>
                            <button class="btn-accion" onclick="abrirModalFactura(<?= $pedido['id_pedido'] ?>)"><i class="fa fa-file-invoice"></i> Ver Factura</button>

                            <?php if (strtolower($pedido['estado']) === 'entregado'): ?>
                                <?php
                                $stmtResena = $pdo->prepare("SELECT COUNT(*) FROM resena WHERE id_pedido = :id_pedido");
                                $stmtResena->execute(['id_pedido' => $pedido['id_pedido']]);
                                $yaTieneResena = $stmtResena->fetchColumn() > 0;
                                ?>

                                <?php if (!$yaTieneResena): ?>
                                    <button class="btn-accion" onclick="abrirModalResena(<?= $pedido['id_pedido'] ?>)"><i class="fa fa-star"></i> Dejar Reseña</button>
                                <?php else: ?>
                                    <em>Reseña enviada</em>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <em>Factura no disponible</em>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Modal Genérico -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal()">&times;</span>
        <iframe id="modalIframe" src="" width="100%" height="500" style="border: none;"></iframe>
    </div>
</div>

<script>
    function abrirModalFactura(idPedido) {
        document.getElementById('modalIframe').src = 'ver_factura.php?id_pedido=' + idPedido;
        document.getElementById('modal').style.display = 'block';
    }

    function abrirModalResena(idPedido) {
        document.getElementById('modalIframe').src = 'dejar_resena.php?id_pedido=' + idPedido;
        document.getElementById('modal').style.display = 'block';
    }

    function cerrarModal() {
        document.getElementById('modal').style.display = 'none';
        document.getElementById('modalIframe').src = '';
    }

    window.addEventListener("message", function (e) {
        if (e.data === "resena_enviada") {
            cerrarModal();
            const toast = document.getElementById("toast");
            toast.style.display = "block";
            setTimeout(() => {
                toast.style.display = "none";
                location.reload();
            }, 5000);
        }
    });

    window.onclick = function(event) {
        const modal = document.getElementById('modal');
        if (event.target == modal) {
            cerrarModal();
        }
    }
</script>

</body>
</html>
