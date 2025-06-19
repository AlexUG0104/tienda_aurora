<?php
// administrador/api/products/get_products.php
require_once '../../../config_sesion.php'; // Subir tres niveles
require_once '../../../db.php';          // Subir tres niveles
require_once '../../classes/GestorProductos.php'; // Subir dos niveles, luego entrar a classes

header('Content-Type: application/json');

$gestorProductos = new GestorProductos($pdo); // Nombre de clase actualizado
$response = $gestorProductos->obtenerProductos(); // Nombre de método actualizado

echo json_encode($response);
?>