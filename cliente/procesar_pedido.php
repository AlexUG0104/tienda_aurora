<?php
require_once '../config_sesion.php';
require_once '../db.php';

// Verificar que sea cliente logueado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: login_cliente.php?type=client");
    exit();
}

// Verificar que se enviaron productos
if (!isset($_POST['productos']) || empty($_POST['productos'])) {
    die("No se seleccionaron productos.");
}

try {
    $pdo->beginTransaction();

    // Generar código de pedido (simple autoincremento con formato)
    $stmt = $pdo->query("SELECT COUNT(*) FROM pedido");
    $numPedidos = $stmt->fetchColumn();
    $codigoPedido = 'PED-' . str_pad($numPedidos + 1, 5, '0', STR_PAD_LEFT);

    // Obtener id_estado 'Pendiente'
    $stmt = $pdo->prepare("SELECT id_estado FROM pedido_estado WHERE estado = 'Pendiente' LIMIT 1");
    $stmt->execute();
    $idEstadoPendiente = $stmt->fetchColumn();

    if (!$idEstadoPendiente) {
        throw new Exception("Estado 'Pendiente' no encontrado en pedido_estado.");
    }

    // Insertar en pedido
    $stmt = $pdo->prepare("
        INSERT INTO pedido (codigo_pedido, fecha_creacion, usuario_creacion, id_cliente, fecha_compra, estado_pedido)
        VALUES (:codigo, NOW(), :usuario_creacion, :id_cliente, CURRENT_DATE, :estado_pedido)
        RETURNING id
    ");

    $stmt->execute([
        ':codigo' => $codigoPedido,
        ':usuario_creacion' => $_SESSION['user_id'],
        ':id_cliente' => $_SESSION['user_id'], // el cliente mismo
        ':estado_pedido' => $idEstadoPendiente
    ]);

    $idPedido = $stmt->fetchColumn();

    // Insertar detalles de pedido
    foreach ($_POST['productos'] as $productoData) {
        list($idProducto, $cantidad, $precioUnitario) = explode('|', $productoData);

        // Validar stock disponible
        $stmtStock = $pdo->prepare("SELECT stock FROM producto WHERE id = :id_producto FOR UPDATE");
        $stmtStock->execute([':id_producto' => $idProducto]);
        $stockActual = $stmtStock->fetchColumn();

        if ($stockActual === false) {
            throw new Exception("Producto ID {$idProducto} no encontrado.");
        }

        if ($cantidad > $stockActual) {
            throw new Exception("Stock insuficiente para producto ID {$idProducto}.");
        }

        // Insertar en detalle_pedido
        $stmtDetalle = $pdo->prepare("
            INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario, fecha_creacion, usuario_creacion)
            VALUES (:id_pedido, :id_producto, :cantidad, :precio_unitario, NOW(), :usuario_creacion)
        ");

        $stmtDetalle->execute([
            ':id_pedido' => $idPedido,
            ':id_producto' => $idProducto,
            ':cantidad' => $cantidad,
            ':precio_unitario' => $precioUnitario,
            ':usuario_creacion' => $_SESSION['user_id']
        ]);

        // Actualizar stock del producto
        $stmtUpdateStock = $pdo->prepare("
            UPDATE producto SET stock = stock - :cantidad WHERE id = :id_producto
        ");
        $stmtUpdateStock->execute([
            ':cantidad' => $cantidad,
            ':id_producto' => $idProducto
        ]);
    }

    $pdo->commit();

    // Redirigir a página de éxito
    header("Location: pedido_exito.php?codigo=" . urlencode($codigoPedido));
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    die("Error al procesar pedido: " . $e->getMessage());
}
