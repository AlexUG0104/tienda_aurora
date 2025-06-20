<?php
require_once '../config_sesion.php';
require_once '../db.php';

// Verificar que sea cliente logueado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: login_cliente.php?type=client");
    exit();
}

// Validar y decodificar carrito
if (!isset($_POST['carrito'])) {
    die("Carrito no recibido.");
}

$carrito = is_string($_POST['carrito']) ? json_decode($_POST['carrito'], true) : $_POST['carrito'];

if (!is_array($carrito) || count($carrito) === 0) {
    die("Carrito inválido o vacío.");
}

try {
    $pdo->beginTransaction();

    // Obtener ID del cliente desde id_credencial
    $stmt = $pdo->prepare("SELECT id FROM cliente WHERE id_credencial = :credencial_id LIMIT 1");
    $stmt->execute([':credencial_id' => $_SESSION['user_id']]);
    $idCliente = $stmt->fetchColumn();
    if (!$idCliente) {
        throw new Exception("No se encontró cliente asociado a esta sesión.");
    }

    // Generar código de pedido único
    $stmt = $pdo->query("SELECT COUNT(*) FROM pedido");
    $codigoPedido = 'PED-' . str_pad($stmt->fetchColumn() + 1, 5, '0', STR_PAD_LEFT);

    // Obtener ID de estado 'Pendiente'
    $stmt = $pdo->prepare("SELECT id_estado FROM pedido_estado WHERE estado = 'Pendiente' LIMIT 1");
    $stmt->execute();
    $idEstadoPendiente = $stmt->fetchColumn();
    if (!$idEstadoPendiente) {
        throw new Exception("Estado 'Pendiente' no encontrado.");
    }

    // Insertar pedido
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

    // Insertar detalles del pedido
    $totalSinIVA = 0;
    foreach ($carrito as $item) {
        if (!isset($item['id'], $item['cantidad'], $item['precio'])) continue;

        $idProducto = (int)$item['id'];
        $cantidad = (int)$item['cantidad'];
        $precio = (float)$item['precio'];
        $nombre = htmlspecialchars($item['nombre'] ?? 'Producto desconocido');

        if ($idProducto <= 0 || $cantidad <= 0 || $precio <= 0) continue;

        $totalSinIVA += $cantidad * $precio;

        try {
            $stmt = $pdo->prepare("CALL sp_agregar_detalle_pedido(:id_pedido, :id_producto, :cantidad, :precio_unitario, :usuario)");
            $stmt->execute([
                ':id_pedido' => $idPedido,
                ':id_producto' => $idProducto,
                ':cantidad' => $cantidad,
                ':precio_unitario' => $precio,
                ':usuario' => $_SESSION['user_id']
            ]);
        } catch (PDOException $ex) {
            $pdo->rollBack();
        
            $mensajeError = $ex->getMessage();
        
            if (strpos($mensajeError, 'Stock insuficiente') !== false) {
                $productoSeguro = htmlspecialchars($nombre);
                echo "<!DOCTYPE html>
                <html lang='es'>
                <head>
                    <meta charset='UTF-8'>
                    <title>Error de Stock</title>
                    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'>
                    <style>
                        body {
                            font-family: 'Montserrat', sans-serif;
                            background-color:rgb(255, 255, 255);
                            color:rgb(0, 0, 0);
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            height: 100vh;
                            padding: 20px;
                            text-align: center;
                        }
                        .error-box {
                            background:rgb(171, 193, 178);
                            border: 1px solidrgb(152, 255, 126);
                            border-radius: 10px;
                            padding: 30px;
                            max-width: 500px;
                            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                        }
                        .btn-back {
                            margin-top: 20px;
                        }
                    </style>
                </head>
                <body>
                    <div class='error-box'>
                        <h2 class='text-danger'>¡Lo sentimos!</h2>
                        <p>En este momento no hay stock suficiente para <strong>{$productoSeguro}</strong>.</p>
                        <a href='../VentaGeneral/ventageneral.php' class='btn btn-outline-danger btn-back'>
                            Volver a los productos
                        </a>
                    </div>
                </body>
                </html>";
                exit;
            }
        
            // Otro error no relacionado al stock
            $mensajeSeguro = htmlspecialchars($mensajeError);
            echo "<h2>Error al procesar la venta</h2>
                  <p>Producto: <strong>" . htmlspecialchars($nombre) . "</strong></p>
                  <p>Mensaje: $mensajeSeguro</p>";
            exit;
        }
        
    }

    // === Paso de transacción ===

    $nombreMetodoForm = $_POST['metodo_pago'] ?? '';
    $mapa = [
        'tarjeta' => 'Tarjeta',
        'efectivo' => 'Pago en Efectivo',
        'sinpe' => 'Sinpe Móvil',
        'transferencia' => 'Transferencia'
    ];

    $nombreMetodoBD = $mapa[strtolower($nombreMetodoForm)] ?? null;
    if (!$nombreMetodoBD) {
        throw new Exception("Método de pago inválido.");
    }

    // Validación adicional de comprobante si es sinpe o transferencia
    if (in_array(strtolower($nombreMetodoForm), ['sinpe', 'transferencia']) && empty(trim($_POST['comprobante_envio'] ?? ''))) {
        throw new Exception("Debe ingresar el número de comprobante para SINPE o Transferencia.");
    }

    // Buscar ID del método de pago
    $stmt = $pdo->prepare("SELECT id_metodo_pago FROM metodo_pago WHERE nombre_metodo = :nombre LIMIT 1");
    $stmt->execute([':nombre' => $nombreMetodoBD]);
    $idMetodoPago = $stmt->fetchColumn();
    if (!$idMetodoPago) {
        throw new Exception("Método de pago no encontrado.");
    }

    // Monto total con IVA
    $montoTotal = round($totalSinIVA * 1.13, 2);
    $referenciaPago = $_POST['comprobante_envio'] ?? 'Pago sin referencia';

    // Insertar transacción
    $stmt = $pdo->prepare("CALL registrar_transaccion(:id_metodo, :id_pedido, :monto, :referencia, :usuario)");
    $stmt->execute([
        ':id_metodo' => $idMetodoPago,
        ':id_pedido' => $idPedido,
        ':monto' => $montoTotal,
        ':referencia' => $referenciaPago,
        ':usuario' => $_SESSION['user_id']
    ]);

    // 🔁 NUEVO: Generar factura del pedido
    $stmt = $pdo->prepare("CALL generar_factura(:id_pedido)");
    $stmt->execute([':id_pedido' => $idPedido]);

    // Confirmar todo
    $pdo->commit();

    // Redirigir
    header("Location: pedido_exito.php?codigo=" . urlencode($codigoPedido));
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    die("Error al procesar el pedido: " . htmlspecialchars($e->getMessage()));
}
