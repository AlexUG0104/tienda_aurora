<?php
require '../db.php';

$identificacion = $_POST['identificacion'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$id_credencial = $_POST['id_credencial'];

$sql = "CALL insertar_cliente(:identificacion, :nombre, :apellido, :id_credencial)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':identificacion' => $identificacion,
    ':nombre' => $nombre,
    ':apellido' => $apellido,
    ':id_credencial' => $id_credencial
]);

echo "Cliente insertado con éxito. <a href='../index.php'>Volver al menú</a>";
?>
