<?php
require_once '../config_sesion.php';
require_once '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: ../cliente/login_cliente.php");
    exit;
}

$idCliente = $_SESSION['user_id'];
$idPedido = isset($_GET['id_pedido']) ? (int)$_GET['id_pedido'] : 0;

$pedidoValido = false;
if ($idPedido > 0) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM pedido p
        JOIN pedido_estado e ON p.estado_pedido = e.id_estado
        WHERE p.id = :id_pedido
        AND LOWER(e.estado) = 'entregado'
        AND p.id_cliente = (SELECT id FROM cliente WHERE id_credencial = :credencial)
    ");
    $stmt->execute([':id_pedido' => $idPedido, ':credencial' => $idCliente]);
    $pedidoValido = $stmt->fetchColumn() > 0;

    if ($pedidoValido) {
        $stmtCheck = $pdo->prepare("SELECT 1 FROM resena WHERE id_pedido = :id");
        $stmtCheck->execute([':id' => $idPedido]);
        if ($stmtCheck->fetch()) {
            $pedidoValido = false;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dejar Reseña</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f5; display: flex; justify-content: center; padding: 50px; }
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 600px; width: 100%; }
        h1 { text-align: center; color: #444; margin-bottom: 20px; }
        textarea, input[type="submit"] { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; margin-bottom: 15px; font-size: 14px; }
        .stars i { font-size: 26px; color: #ccc; cursor: pointer; }
        .stars i.selected { color: #f5a623; }
        input[type="submit"] { background: #6b8f71; color: white; font-size: 16px; cursor: pointer; transition: background 0.2s; }
        input[type="submit"]:hover { background: #547459; }
        .msg { color: #e74c3c; font-weight: bold; text-align: center; }
    </style>
</head>
<body>

<div class="card">
    <h1>📝 Dejar Reseña</h1>

    <?php if (!$pedidoValido): ?>
        <p class="msg">No puedes dejar una reseña para este pedido.</p>
    <?php else: ?>
        <form method="post" action="guardar_resena.php">
            <input type="hidden" name="id_pedido" value="<?= $idPedido ?>">

            <label>Calificación:</label>
            <div class="stars" id="stars">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fa fa-star" data-value="<?= $i ?>"></i>
                <?php endfor; ?>
            </div>
            <input type="hidden" name="calificacion" id="calificacion" required>

            <label>Comentario:</label>
            <textarea name="comentario" rows="4" placeholder="Escribe tu experiencia..." required></textarea>

            <input type="submit" value="Enviar Reseña">
        </form>
    <?php endif; ?>
</div>

<script>
    const estrellas = document.querySelectorAll('.stars i');
    const inputCalificacion = document.getElementById('calificacion');

    estrellas.forEach(estrella => {
        estrella.addEventListener('click', () => {
            const valor = estrella.getAttribute('data-value');
            inputCalificacion.value = valor;
            estrellas.forEach(e => e.classList.remove('selected'));
            for (let i = 0; i < valor; i++) {
                estrellas[i].classList.add('selected');
            }
        });
    });
</script>

</body>
</html>
