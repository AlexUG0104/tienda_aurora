<?php
// admin/actualizar_producto.php
require_once '../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $codigo_producto = $_POST['codigo_producto'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $descripcion = $_POST['descripcion'] ?? null;
    $precio_unitario = $_POST['precio_unitario'] ?? null;
    $stock = $_POST['stock'] ?? null;
    $talla = $_POST['talla'] ?? null;
    $id_categoria = $_POST['id_categoria'] ?? null;
    $color = $_POST['color'] ?? null;
    $usuario_modifica = 1; // **IMPORTANTE**: Reemplazar con el ID del usuario logeado

    // Validación básica
    if (empty($id) || empty($codigo_producto) || empty($nombre) || empty($precio_unitario) || !isset($stock)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos obligatorios deben estar llenos (ID, código, nombre, precio, stock).']);
        exit();
    }

    try {
        $sql = "SELECT actualizar_producto(
                    :id, :codigo_producto, :nombre, :descripcion, :precio_unitario, :stock,
                    :talla, :id_categoria, :color, :usuario_modifica
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => (int)$id,
            ':codigo_producto' => $codigo_producto,
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':precio_unitario' => (float)$precio_unitario,
            ':stock' => (int)$stock,
            ':talla' => $talla,
            ':id_categoria' => $id_categoria ? (int)$id_categoria : null,
            ':color' => $color,
            ':usuario_modifica' => $usuario_modifica
        ]);

        echo json_encode(['success' => true, 'message' => 'Producto actualizado con éxito.']);

    } catch (PDOException $e) {
        error_log("Error al actualizar producto ID: " . $id . " - " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error de base de datos al actualizar producto: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
}
?>