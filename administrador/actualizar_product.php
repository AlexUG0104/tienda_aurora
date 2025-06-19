<?php
// admin/administrador/actualizar_producto.php
require_once '../db.php'; // Ajusta la ruta si es necesario

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
    exit;
}

$id = $_POST['id'] ?? null; // ID del producto a actualizar
$codigo_producto = $_POST['codigo_producto'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$descripcion = $_POST['descripcion'] ?? null;
$precio_unitario = $_POST['precio_unitario'] ?? null;
$stock = $_POST['stock'] ?? null;
$talla = $_POST['talla'] ?? null;
$id_categoria = $_POST['id_categoria'] ?? null;
$color = $_POST['color'] ?? null;
$url_imagen = $_POST['url_imagen'] ?? null;

if (empty($id) || empty($codigo_producto) || empty($nombre) || empty($precio_unitario) || empty($stock)) {
    echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios para la actualización.']);
    exit;
}

try {
    $sql = "SELECT actualizar_producto(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $id, // El ID es el primer parámetro en la función SQL
        $codigo_producto,
        $nombre,
        $descripcion,
        $precio_unitario,
        $stock,
        $talla,
        $id_categoria,
        $color,
        $url_imagen
    ]);

    echo json_encode(['success' => true, 'message' => 'Producto actualizado exitosamente.']);


} catch (PDOException $e) {
    error_log("Error al actualizar producto: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al actualizar producto. Intente de nuevo más tarde.', 'error_detail' => $e->getMessage()]);
}
?>