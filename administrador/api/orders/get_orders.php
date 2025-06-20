<?php
// administrador/api/orders/get_orders.php
require_once '../../../config_sesion.php'; 
require_once '../../../db.php';          
require_once '../../classes/GestorPedidos.php'; 

header('Content-Type: application/json');


if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 1) { 
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit();
}

$gestorPedidos = new GestorPedidos($pdo); // Instancia de la clase GestorPedidos

// Llamar al método correcto que obtiene los pedidos para el administrador
$response = $gestorPedidos->obtenerPedidosAdministrador(); 

echo json_encode($response);
?>