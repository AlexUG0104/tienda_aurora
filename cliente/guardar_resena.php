<?php
require_once '../config_sesion.php';
require_once '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    exit("Acceso no autorizado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPedido = isset($_POST['id_pedido']) ? (int) $_POST['id_pedido'] : 0;
    $comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';
    $calificacion = isset($_POST['calificacion']) ? (int) $_POST['calificacion'] : 0;

    if ($idPedido <= 0 || empty($comentario) || $calificacion < 1 || $calificacion > 5) {
        exit("<script>window.parent.postMessage('resena_error', '*');</script>");
    }

    try {
        // Verificar que el pedido pertenece al cliente autenticado
        $stmt = $pdo->prepare("
            SELECT p.id FROM pedido p
            JOIN cliente c ON p.id_cliente = c.id
            WHERE p.id = :id_pedido AND c.id_credencial = :credencial
        ");
        $stmt->execute([
            ':id_pedido' => $idPedido,
            ':credencial' => $_SESSION['user_id']
        ]);
        $esValido = $stmt->fetchColumn();

        if (!$esValido) {
            exit("<script>window.parent.postMessage('resena_error', '*');</script>");
        }

        // Ejecutar SP para insertar la reseña
        $stmtInsert = $pdo->prepare("CALL sp_insertar_resena(:id_pedido, :comentario, :calificacion)");
        $stmtInsert->execute([
            ':id_pedido' => $idPedido,
            ':comentario' => $comentario,
            ':calificacion' => $calificacion
        ]);

        // Avisar al padre que fue exitosa
        echo "<script>
            window.parent.postMessage('resena_enviada', '*');
        </script>";
    } catch (PDOException $e) {
        echo "<script>window.parent.postMessage('resena_error', '*');</script>";
    }
} else {
    echo "<script>window.parent.postMessage('resena_error', '*');</script>";
}
?>
