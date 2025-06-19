<?php
require_once '../db.php';
require_once '../../config_sesion.php'; 

header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 1) {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_producto = $_POST['codigo_producto'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $descripcion = $_POST['descripcion'] ?? null;
    $precio_unitario = $_POST['precio_unitario'] ?? null;
    $stock = $_POST['stock'] ?? null;
    $talla = $_POST['talla'] ?? null;
    $id_categoria = $_POST['id_categoria'] ?? null;
    $color = $_POST['color'] ?? null;
    
    // Aquí es donde debemos asegurarnos de que estos dos parámetros también se envíen
    // Aunque no tengas un campo en el formulario para ellos, deben ser pasados a la función SQL.
    // Puedes obtener el usuario de la sesión o usar un valor por defecto.
    $usuario_creacion = $_SESSION['usuario_nombre'] ?? 'admin_desconocido'; // Obtén el nombre del usuario loggeado
    $url_imagen = $_POST['url_imagen'] ?? null; // Si no tienes un campo en el formulario, será null
    
    if (empty($codigo_producto) || empty($nombre) || !isset($precio_unitario) || !isset($stock)) {
        echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios (código, nombre, precio, stock).']);
        exit;
    }

    try {
        $precio_unitario = (float)$precio_unitario;
        $stock = (int)$stock;
        // Si id_categoria es opcional en tu DB, puedes dejarlo como null si está vacío
        $id_categoria = !empty($id_categoria) ? (int)$id_categoria : null; 

        // ¡Ajustar el número de placeholders ($1, $2, etc.) a 10!
        $query = "SELECT insertar_producto($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)";
        $stmt = $pdo->prepare($query);

        // Bindear los 10 parámetros. El orden debe coincidir con la definición de tu función PG.
        $stmt->bindParam(1, $codigo_producto, PDO::PARAM_STR);
        $stmt->bindParam(2, $nombre, PDO::PARAM_STR);
        $stmt->bindParam(3, $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(4, $precio_unitario); // Para NUMERIC/DECIMAL, PDO::PARAM_STR o dejar que PDO lo infiera si es FLOAT/DOUBLE
        $stmt->bindParam(5, $stock, PDO::PARAM_INT);
        $stmt->bindParam(6, $talla, PDO::PARAM_STR);
        $stmt->bindParam(7, $id_categoria, PDO::PARAM_INT);
        $stmt->bindParam(8, $color, PDO::PARAM_STR);
        $stmt->bindParam(9, $usuario_creacion, PDO::PARAM_STR); // ¡Añadido!
        $stmt->bindParam(10, $url_imagen, PDO::PARAM_STR);    // ¡Añadido!

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && isset($result['insertar_producto'])) {
            echo json_encode(['success' => true, 'message' => 'Producto agregado con éxito.', 'newProductId' => $result['insertar_producto']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: No se pudo obtener el ID del nuevo producto o la función no retornó un valor.']);
        }

    } catch (PDOException $e) {
        error_log("Error al insertar producto: " . $e->getMessage() . " | SQL: " . $query);
        echo json_encode(['success' => false, 'message' => 'Error de base de datos al agregar producto.', 'detail' => $e->getMessage()]);
    } catch (Exception $e) {
        error_log("Error inesperado al insertar producto: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error inesperado al agregar producto.', 'detail' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
}
?>