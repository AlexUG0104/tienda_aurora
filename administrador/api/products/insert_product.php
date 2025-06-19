<?php
// administrador/api/products/insert_product.php
require_once '../../../config_sesion.php';
require_once '../../../db.php';
require_once '../../classes/GestorProductos.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;

    if (empty($data['codigo_producto']) || empty($data['nombre']) || empty($data['precio_unitario']) || !isset($data['stock'])) {
        echo json_encode(['success' => false, 'message' => 'Campos obligatorios faltantes (código, nombre, precio, stock).']);
        exit();
    }

    // ✅ Procesar imagen si se envió
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $directorioFisico = '../../../imagenes/portada/';
        $rutaRelativaWeb = 'imagenes/portada/';

        // Crear directorio si no existe
        if (!file_exists($directorioFisico)) {
            mkdir($directorioFisico, 0777, true);
        }

        $nombreArchivo = uniqid() . '_' . basename($_FILES['imagen']['name']);
        $rutaCompleta = $directorioFisico . $nombreArchivo;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaCompleta)) {
            $data['url_imagen'] = $rutaRelativaWeb . $nombreArchivo;
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al mover la imagen al servidor.']);
            exit();
        }
    } else {
        $data['url_imagen'] = null; // Opcional: null si no se envía imagen
    }

    $gestorProductos = new GestorProductos($pdo);
    $response = $gestorProductos->insertarProducto($data);

    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
}
