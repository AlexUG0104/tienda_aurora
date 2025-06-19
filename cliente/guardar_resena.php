<?php
require_once '../config_sesion.php';
require_once '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: login_cliente.php?type=client");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPedido = $_POST['id_pedido'];
    $calificacion = $_POST['calificacion'];
    $comentario = trim($_POST['comentario']);

    // Validación básica
    if (empty($idPedido) || empty($calificacion) || empty($comentario)) {
        die("Todos los campos son obligatorios.");
    }

    // Verificar que no exista ya una reseña para este pedido
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM resena WHERE id_pedido = :id_pedido");
    $stmtCheck->execute([':id_pedido' => $idPedido]);
    if ($stmtCheck->fetchColumn() > 0) {
        die("Ya has enviado una reseña para este pedido.");
    }

    // Insertar reseña
    $stmtInsert = $pdo->prepare("
        INSERT INTO resena (id_pedido, comentario, calificacion, fecha_reseña)
        VALUES (:id_pedido, :comentario, :calificacion, CURRENT_TIMESTAMP)
    ");
    $stmtInsert->execute([
        ':id_pedido' => $idPedido,
        ':comentario' => $comentario,
        ':calificacion' => $calificacion
    ]);

    // Redirigir a éxito o página de pedidos
    header("Location: mis_pedidos.php?mensaje=reseña_guardada");
    exit();
} else {
    die("Método no permitido.");
}
