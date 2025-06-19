<?php
require_once '../config_sesion.php';
require_once '../db.php';

// Verificar que sea cliente logueado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: login_cliente.php?type=client");
    exit();
}

// Paso 1: Validar y decodificar carrito JSON
if (!isset($_POST['carrito'])) {
    die("Carrito no recibido.");
}

$carrito = $_POST['carrito'];

if (is_string($carrito)) {
    $carrito = json_decode($carrito, true);
}

if (!is_array($carrito) || count($carrito) === 0) {
    die("Carrito inválido o vacío.");
}

try {
    $pdo->beginTransaction();

    // Obtener el id_cliente usando id_credencial directamente
        $stmt = $pdo->prepare("
            SELECT id 
            FROM cliente 
            WHERE id_credencial = :credencial_id
            LIMIT 1
        ");
        $stmt->execute([':credencial_id' => $_SESSION['user_id']]);
        $idCliente = $stmt->fetchColumn();

        if (!$idCliente) {
            die("Error: El usuario no tiene un cliente asociado en el sistema.");
        }


    // Paso 3: Generar código de pedido único
    $stmt = $pdo->query("SELECT COUNT(*) FROM pedido");
    $numPedidos = $stmt->fetchColumn();
    $codigoPedido = 'PED-' . str_pad($numPedidos + 1, 5, '0', STR_PAD_LEFT);

    // Paso 4: Buscar estado "Pendiente"
    $stmt = $pdo->prepare("SELECT id_estado FROM pedido_estado WHERE estado = 'Pendiente' LIMIT 1");
    $stmt->execute();
    $idEstadoPendiente = $stmt->fetchColumn();

    if (!$idEstadoPendiente) {
        throw new Exception("Estado 'Pendiente' no encontrado.");
    }

    // Paso 5: Insertar pedido
    $stmt = $pdo->prepare("SELECT insertar_pedido(:codigo, :usuario, :cliente, :fecha_compra, :estado, :fecha_entrega)");
    $stmt->execute([
        ':codigo' => $codigoPedido,
        ':usuario' => $_SESSION['user_id'],
        ':cliente' => $idCliente,
        ':fecha_compra' => date('Y-m-d'),
        ':estado' => $idEstadoPendiente,
        ':fecha_entrega' => null
    ]);
    $idPedido = $stmt->fetchColumn();

    
    // Paso 6: Insertar detalles de pedido
    
    foreach ($carrito as $item) {
        if (!isset($item['id'], $item['cantidad'], $item['precio'])) {
            continue;
        }

        $idProducto = (int)$item['id'];
        $cantidad = (int)$item['cantidad'];
        $precio = (float)$item['precio'];
        $nombre = htmlspecialchars($item['nombre'] ?? 'Producto desconocido');

        if ($idProducto <= 0 || $cantidad <= 0 || $precio <= 0) {
            continue;
        }

        try {
            $stmt = $pdo->prepare("CALL sp_agregar_detalle_pedido(:id_pedido, :id_producto, :cantidad, :precio_unitario, :usuario)");
            $stmt->execute([
                ':id_pedido' => $idPedido,
                ':id_producto' => $idProducto,
                ':cantidad' => $cantidad,
                ':precio_unitario' => $precio,
                ':usuario' => $_SESSION['user_id']
            ]);
        } catch (PDOException $detalleEx) {
            $pdo->rollBack();

            if (strpos($detalleEx->getMessage(), 'Stock insuficiente') !== false) {
                die("
                    <h2 style='color:red;'>¡Error de stock!</h2>
                    <p>No hay suficiente inventario disponible para el producto: <strong>{$nombre}</strong>.</p>
                    <a href='resumen_pedido.php'>Volver al resumen del pedido</a>
                ");
            }

            // Otros errores
            die("Error al agregar producto '{$nombre}': " . htmlspecialchars($detalleEx->getMessage()));
        }
    }

    // Paso 7: Confirmar transacción
    $pdo->commit();

    // Redirigir a éxito
    header("Location: pedido_exito.php?codigo=" . urlencode($codigoPedido));
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    die("Error al procesar el pedido: " . htmlspecialchars($e->getMessage()));
}
