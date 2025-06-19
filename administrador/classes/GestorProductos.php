<?php
// administrador/classes/GestorProductos.php

class GestorProductos {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function obtenerProductos() {
        try {
            $sql = "SELECT * FROM obtener_productos()";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en GestorProductos::obtenerProductos: " . $e->getMessage());
            return ['error' => 'Error al obtener productos.', 'details' => $e->getMessage()];
        }
    }

    public function insertarProducto($data) {
    try {
        $sql = "SELECT insertar_producto(
                    :codigo_producto, :nombre, :descripcion, :precio_unitario, :stock,
                    :talla, :id_categoria, :color, :usuario_creacion, :url_imagen
                ) AS new_product_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':codigo_producto' => $data['codigo_producto'],
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? null,
            ':precio_unitario' => (float)$data['precio_unitario'],
            ':stock' => (int)$data['stock'],
            ':talla' => $data['talla'] ?? null,
            ':id_categoria' => isset($data['id_categoria']) ? (int)$data['id_categoria'] : null,
            ':color' => $data['color'] ?? null,
            ':usuario_creacion' => 1, // Por defecto
            ':url_imagen' => $data['url_imagen'] ?? null
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ['success' => true, 'newProductId' => $result['new_product_id']];
    } catch (PDOException $e) {
        error_log("Error en GestorProductos::insertarProducto: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error de base de datos al agregar producto: ' . $e->getMessage()];
    }
}

    public function actualizarProducto($data) {
        try {
            $sql = "SELECT actualizar_producto(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['id'],
                $data['codigo_producto'],
                $data['nombre'],
                $data['descripcion'] ?? null,
                (float)$data['precio_unitario'],
                (int)$data['stock'],
                $data['talla'] ?? null,
                isset($data['id_categoria']) ? (int)$data['id_categoria'] : null,
                $data['color'] ?? null,
                $data['url_imagen'] ?? null // Asegúrate de que tu función SQL actualizar_producto acepte url_imagen
            ]);
            return ['success' => true, 'message' => 'Producto actualizado exitosamente.'];
        } catch (PDOException $e) {
            error_log("Error en GestorProductos::actualizarProducto: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al actualizar producto: ' . $e->getMessage()];
        }
    }

    public function eliminarProducto($id) {
        try {
            $sql = "SELECT eliminar_producto(?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return ['success' => true, 'message' => 'Producto eliminado exitosamente.'];
        } catch (PDOException $e) {
            error_log("Error en GestorProductos::eliminarProducto: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al eliminar producto: ' . $e->getMessage()];
        }
    }
}
?>