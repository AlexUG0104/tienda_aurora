<?php
require_once '../config_sesion.php';
require_once '../db.php';

// Solo admins
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 1) {
    header("Location: ../login_admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cliente_id'])) {
    $clienteId = intval($_POST['cliente_id']);

    $stmt = $pdo->prepare("UPDATE cliente SET Cliente_Con_Descuento_Proxima_Facturacion = 1 WHERE id = ?");
    $stmt->execute([$clienteId]);

    header("Location: ver_ventas.php?mensaje=descuento_aplicado");
    exit();
} else {
    header("Location: ver_ventas.php?error=datos_invalidos");
    exit();
}
