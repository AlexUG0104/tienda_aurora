<?php
// administrador/api/products/insert_product.php
require_once '../../../config_sesion.php';
require_once '../../../db.php';
require_once '../../classes/GestorProductos.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
    exit();
}

$data = $_POST;

// ✅ Validación básica de campos obligatorios
if (
    empty($data['codigo_producto']) ||
    empty($data['nombre']) ||
    empty($data['precio_unitario']) ||
    !isset($data['stock'])
) {
    echo json_encode(['success' => false, 'message' => 'Campos obligatorios faltantes (código, nombre, precio, stock).']);
    exit();
}

// ✅ Manejo de imagen (opcional)
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $directorioFisico = realpath(__DIR__ . '/../../../imagenes/portada/') . '/';
    $rutaRelativaWeb = 'imagenes/portada/';

    // Asegurar que el directorio exista
    if (!is_dir($directorioFisico)) {
        mkdir($directorioFisico, 0777, true);
    }

    // Nombre único
    $nombreArchivo = uniqid() . '_' . basename($_FILES['imagen']['name']);
    $rutaCompleta = $directorioFisico . $nombreArchivo;

    // Mover archivo
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaCompleta)) {
        $data['url_imagen'] = $rutaRelativaWeb . $nombreArchivo;
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar la imagen en el servidor.']);
        exit();
    }
} else {
    $data['url_imagen'] = null; // Imagen no obligatoria
}

// Insertar producto
try {
    $gestorProductos = new GestorProductos($pdo);
    $response = $gestorProductos->insertarProducto($data);
    echo json_encode($response);
} catch (Exception $e) {
    error_log("Error al insertar producto: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error inesperado al insertar el producto.']);
}
