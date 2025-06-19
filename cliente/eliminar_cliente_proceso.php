<?php
require '../db.php';

$id_cliente = $_POST['id_cliente'];

$sql = "CALL eliminar_cliente(:id_cliente)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id_cliente' => $id_cliente
]);

echo "Cliente eliminado con éxito. <a href='../index.php'>Volver al menú</a>";
?>
