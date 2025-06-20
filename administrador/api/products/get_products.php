<?php
// administrador/api/products/get_products.php
require_once '../../../config_sesion.php'; 
require_once '../../../db.php';         
require_once '../../classes/GestorProductos.php'; 

header('Content-Type: application/json');

$gestorProductos = new GestorProductos($pdo); 
$response = $gestorProductos->obtenerProductos(); 
echo json_encode($response);
?>