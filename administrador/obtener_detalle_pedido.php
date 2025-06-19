<?php
// admin/obtener_detalle_pedido.php
require_once '../db.php'; 

header('Content-Type: application/json');

if (isset($_GET['id_pedido'])) {
    $id_pedido = (int)$_GET['id_pedido'];

    try {
        $sql = "SELECT * FROM obtener_detalles_pedido(:id_pedido)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_pedido' => $id_pedido]);
        $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($detalles);

    } catch (PDOException $e) {
        error_log("Error al obtener detalles del pedido ID: " . $id_pedido . " - " . $e->getMessage());
        echo json_encode(['error' => 'Error al cargar los detalles del pedido.']);
    }
} else {
    echo json_encode(['error' => 'ID de pedido no proporcionado.']);
}
?>