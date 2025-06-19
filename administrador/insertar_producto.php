<?php
// admin/insertar_producto.php
require_once '../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_producto = $_POST['codigo_producto'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $descripcion = $_POST['descripcion'] ?? null;
    $precio_unitario = $_POST['precio_unitario'] ?? null;
    $stock = $_POST['stock'] ?? null;
    $talla = $_POST['talla'] ?? null;
    $id_categoria = $_POST['id_categoria'] ?? null;
    $color = $_POST['color'] ?? null;
    $usuario_creacion = 1; // **IMPORTANTE**: Reemplazar con el ID del usuario logeado

    // Validación básica de campos requeridos
    if (empty($codigo_producto) || empty($nombre) || empty($precio_unitario) || !isset($stock)) {
        echo json_encode(['success' => false, 'message' => 'Campos obligatorios faltantes (código, nombre, precio, stock).']);
        exit();
    }

    try {
        $sql = "SELECT insertar_producto(
                    :codigo_producto, :nombre, :descripcion, :precio_unitario, :stock,
                    :talla, :id_categoria, :color, :usuario_creacion
                ) AS new_product_id";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':codigo_producto' => $codigo_producto,
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':precio_unitario' => (float)$precio_unitario,
            ':stock' => (int)$stock,
            ':talla' => $talla,
            ':id_categoria' => $id_categoria ? (int)$id_categoria : null, // Aceptar null si no se proporciona
            ':color' => $color,
            ':usuario_creacion' => $usuario_creacion
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $newProductId = $result['new_product_id'];

        echo json_encode(['success' => true, 'message' => 'Producto agregado con éxito.', 'newProductId' => $newProductId]);

    } catch (PDOException $e) {
        error_log("Error al insertar producto: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error de base de datos al agregar producto: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
}
?>