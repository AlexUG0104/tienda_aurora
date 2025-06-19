<?php
// administrador/classes/GestorPedidos.php

class GestorPedidos {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Inserta un nuevo pedido en la base de datos.
     * (Mantengo esta función como ya la teníamos, solo para referencia)
     *
     * @param string $codigoPedido Código único del pedido.
     * @param int $usuarioCreacion ID del usuario (administrador) que crea/registra el pedido.
     * @param int $idCliente ID del cliente asociado al pedido.
     * @param string $fechaCompra Fecha en que se realizó la compra (formato 'YYYY-MM-DD').
     * @param int $estadoPedido ID del estado inicial del pedido.
     * @param string|null $fechaEntrega Fecha estimada de entrega (opcional, formato 'YYYY-MM-DD').
     * @return int|false El ID del nuevo pedido insertado o false en caso de error.
     */
    public function insertarPedido($codigoPedido, $usuarioCreacion, $idCliente, $fechaCompra, $estadoPedido, $fechaEntrega = null) {
        try {
            $stmt = $this->pdo->prepare("SELECT insertar_pedido(:codigo_pedido, :usuario_creacion, :id_cliente, :fecha_compra, :estado_pedido, :fecha_entrega)");
            $stmt->bindParam(':codigo_pedido', $codigoPedido);
            $stmt->bindParam(':usuario_creacion', $usuarioCreacion, PDO::PARAM_INT);
            $stmt->bindParam(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_compra', $fechaCompra);
            $stmt->bindParam(':estado_pedido', $estadoPedido, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_entrega', $fechaEntrega);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error al insertar pedido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene una lista completa de pedidos para el panel de administración.
     * (Mantengo esta función como ya la teníamos, solo para referencia)
     *
     * @return array Un array de objetos stdClass con los datos de los pedidos o un array vacío.
     */
    public function obtenerPedidosAdministrador() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM obtener_pedidos_administrador()");
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener pedidos para administrador: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene los detalles de un pedido específico por su ID (solo la fila principal del pedido).
     * (Mantengo esta función como ya la teníamos, solo para referencia)
     *
     * @param int $id ID del pedido.
     * @return object|false El objeto pedido o false si no se encuentra o hay un error.
     */
    public function obtenerPedidoPorId($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM obtener_pedido_por_id(:id)");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener pedido por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza la información de un pedido existente.
     * (Mantengo esta función como ya la teníamos, solo para referencia)
     *
     * @param int $id ID del pedido a actualizar.
     * @param string $codigoPedido Nuevo código del pedido.
     * @param int $idCliente Nuevo ID del cliente.
     * @param string $fechaCompra Nueva fecha de compra (formato 'YYYY-MM-DD').
     * @param string $fechaEntrega Nueva fecha de entrega (formato 'YYYY-MM-DD').
     * @param int $estadoPedido Nuevo ID del estado del pedido.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizarPedido($id, $codigoPedido, $idCliente, $fechaCompra, $fechaEntrega, $estadoPedido) {
        try {
            $stmt = $this->pdo->prepare("SELECT actualizar_pedido(:id, :codigo_pedido, :id_cliente, :fecha_compra, :fecha_entrega, :estado_pedido)");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':codigo_pedido', $codigoPedido);
            $stmt->bindParam(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_compra', $fechaCompra);
            $stmt->bindParam(':fecha_entrega', $fechaEntrega);
            $stmt->bindParam(':estado_pedido', $estadoPedido, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error al actualizar pedido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza solo el estado de un pedido específico.
     * (Mantengo esta función como ya la teníamos, solo para referencia)
     *
     * @param int $idPedido ID del pedido a actualizar.
     * @param int $nuevoEstadoId Nuevo ID del estado del pedido.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizarEstadoPedido($idPedido, $nuevoEstadoId) {
        try {
            $stmt = $this->pdo->prepare("SELECT actualizar_estado_pedido(:id_pedido, :nuevo_estado_id)");
            $stmt->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
            $stmt->bindParam(':nuevo_estado_id', $nuevoEstadoId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error al actualizar estado del pedido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un pedido por su ID.
     * (Mantengo esta función como ya la teníamos, solo para referencia)
     *
     * @param int $id ID del pedido a eliminar.
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
    public function eliminarPedido($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT eliminar_pedido(:id)");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error al eliminar pedido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los detalles de los productos incluidos en un pedido específico.
     * Utiliza la función PostgreSQL obtener_detalles_pedido.
     *
     * @param int $idPedido ID del pedido para el cual obtener los detalles.
     * @return array Un array de objetos stdClass con los detalles de los productos del pedido o un array vacío.
     */
    public function obtenerDetallesPedido($idPedido) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM obtener_detalles_pedido(:id_pedido)");
            $stmt->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener detalles del pedido: " . $e->getMessage());
            return [];
        }
    }
}