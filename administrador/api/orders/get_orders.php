<?php
// administrador/api/orders/get_orders.php
require_once '../../../config_sesion.php'; // Subir tres niveles
require_once '../../../db.php';           // Subir tres niveles
require_once '../../classes/GestorPedidos.php'; // Subir dos niveles, luego entrar a classes

header('Content-Type: application/json');

// Asegúrate de que solo los administradores puedan acceder a esta API
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 1) { // Asumiendo tipo_usuario 1 es admin
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit();
}

$gestorPedidos = new GestorPedidos($pdo); // Instancia de la clase GestorPedidos

// Llamar al método correcto que obtiene los pedidos para el administrador
$response = $gestorPedidos->obtenerPedidosAdministrador(); // <-- ¡CORREGIDO AQUÍ!

echo json_encode($response);
?>