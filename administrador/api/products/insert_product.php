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

$data = [
    'codigo_producto' => $_POST['codigo_producto'] ?? null,
    'nombre' => $_POST['nombre'] ?? null,
    'descripcion' => $_POST['descripcion'] ?? null,
    'precio_unitario' => $_POST['precio_unitario'] ?? null,
    'stock' => $_POST['stock'] ?? null,
    'talla' => $_POST['talla'] ?? null,
    'id_categoria' => $_POST['id_categoria'] ?? null,
    'color' => $_POST['color'] ?? null,
    'usuario_creacion' => $_SESSION['usuario']['id'] ?? 1, 
    'url_imagen' => $_POST['url_imagen'] ?? null
];



// Validación básica de campos obligatorios
if (
    empty($data['codigo_producto']) ||
    empty($data['nombre']) ||
    empty($data['precio_unitario']) ||
    !isset($data['stock'])
) {
    echo json_encode(['success' => false, 'message' => 'Campos obligatorios faltantes (código, nombre, precio, stock).']);
    exit();
}

// Manejo de imagen
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $directorioFisico = realpath(__DIR__ . '/../../../imagenes/portada/') . '/';
    $rutaRelativaWeb = 'imagenes/portada/';

   
    if (!is_dir($directorioFisico)) {
        mkdir($directorioFisico, 0777, true);
    }

 
    $nombreArchivo = uniqid() . '_' . basename($_FILES['imagen']['name']);
    $rutaCompleta = $directorioFisico . $nombreArchivo;

    // Mover archivo
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaCompleta)) {
        $data['url_imagen'] = $rutaRelativaWeb . $nombreArchivo;
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar la imagen en el servidor.']);
        exit();
    }
} elseif (!isset($_FILES['imagen']) && isset($_POST['url_imagen']) && trim($_POST['url_imagen']) !== '') {
    $data['url_imagen'] = trim($_POST['url_imagen']); 
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
