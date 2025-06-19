<?php
require '../db.php';

$id_cliente = $_POST['id_cliente'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$id_credencial = $_POST['id_credencial'];

$sql = "CALL actualizar_cliente(:id_cliente, :nombre, :apellido, :id_credencial)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id_cliente' => $id_cliente,
    ':nombre' => $nombre,
    ':apellido' => $apellido,
    ':id_credencial' => $id_credencial
]);


echo "Cliente actualizado con éxito. <a href='../index.php'>Volver al menú</a>";
?>
