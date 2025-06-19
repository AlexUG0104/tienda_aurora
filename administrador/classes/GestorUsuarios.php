<?php

class GestorUsuarios {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene todos los usuarios de la base de datos junto con su tipo de usuario
     * utilizando el procedimiento almacenado 'obtener_usuarios_existentes()'.
     *
     * @return array Un array de usuarios o un array con un mensaje de error.
     */
    public function obtenerUsuarios(): array {
        try {
            // Llama al procedimiento almacenado que ahora devuelve 'nombre_tipo_usuario'
            $stmt = $this->pdo->prepare("SELECT * FROM obtener_usuarios_existentes()");
            $stmt->execute();
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $usuarios;

        } catch (PDOException $e) {
            error_log("Error al obtener usuarios (SP obtener_usuarios_existentes): " . $e->getMessage());
            return ['error' => 'Error al cargar los usuarios. Por favor, intente de nuevo más tarde.'];
        }
    }

    /**
     * Registra un nuevo usuario en la base de datos
     * utilizando el procedimiento almacenado 'insertar_usuario()'.
     *
     * @param string $nombre El nombre del usuario.
     * @param string $clave La contraseña del usuario (se recomienda pasarla ya hasheada).
     * @param int $id_tipo_usuario El ID del tipo de usuario (1: Admin, 2: Cliente, 3: Envíos).
     * @return array Un array con 'success' y el ID del nuevo usuario o un mensaje de error.
     */
   public function registrarUsuario(string $nombre, string $clave, int $id_tipo_usuario): array {
    try {
        if (empty($nombre) || empty($clave) || !in_array($id_tipo_usuario, [1, 2, 3])) {
            return ['success' => false, 'message' => 'Datos de usuario incompletos o inválidos.']; // OK, validación básica.
        }

        $hashed_password = password_hash($clave, PASSWORD_DEFAULT); // OK, buen hashing.
        // Asumiendo que 'insertar_usuario' en el SP espera 'contrasena'
        $stmt = $this->pdo->prepare("SELECT insertar_usuario(:nombre, :contrasena, :id_tipo_usuario)"); // CAMBIO: Usar ':contrasena'
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':contrasena', $hashed_password); // CAMBIO: Bindear a ':contrasena'
        $stmt->bindParam(':id_tipo_usuario', $id_tipo_usuario, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $newUserId = $stmt->fetchColumn(); // OK
            if ($newUserId) {
                return ['success' => true, 'newUserId' => $newUserId];
            } else {
                return ['success' => false, 'message' => 'El procedimiento almacenado no devolvió un ID de usuario válido.'];
            }
        } else {
            return ['success' => false, 'message' => 'No se pudo registrar el usuario a través del procedimiento almacenado.'];
        }

    } catch (PDOException $e) {
        error_log("Error al registrar usuario (SP insertar_usuario): " . $e->getMessage());
        return ['success' => false, 'message' => 'Error de base de datos al registrar usuario.'];
    }
}

    /**
     * Elimina un usuario de la base de datos
     * utilizando el procedimiento almacenado 'eliminar_usuario()'.
     *
     * @param int $id El ID del usuario a eliminar.
     * @return array Un array con 'success' o un mensaje de error.
     */
    /**
     * Obtiene los detalles de un usuario específico por su ID
     * utilizando el procedimiento almacenado 'obtener_usuario_por_id()'.
     *
     * @param int $id ID del usuario.
     * @return array|null El array asociativo del usuario o null si no se encuentra o hay un error.
     */
    public function obtenerUsuarioPorId(int $id): ?array {
        try {
            // Llama al procedimiento almacenado
            $stmt = $this->pdo->prepare("SELECT * FROM obtener_usuario_por_id(:id)");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            // Fetch the result as an associative array
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // If the stored procedure returns a single row or NULL, fetch() is appropriate.
            // If it can return an empty result set (0 rows), fetch() would return false.
            return $usuario ?: null; // Return null if not found

        } catch (PDOException $e) {
            error_log("Error al obtener usuario por ID (SP obtener_usuario_por_id): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualiza la información de un usuario existente
     * utilizando el procedimiento almacenado 'actualizar_usuario()'.
     *
     * @param int $id ID del usuario a actualizar.
     * @param string $nombre Nuevo nombre del usuario.
     * @param string|null $clave Nueva clave del usuario (opcional, si es null no se actualiza).
     * @param int $id_tipo_usuario Nuevo ID del tipo de usuario.
     * @return array Un array con 'success' o un mensaje de error.
     */
  public function actualizarUsuario(int $id, string $nombre, ?string $clave = null, int $id_tipo_usuario): array {
    try {
        $hashed_password = $clave !== null && !empty($clave) ? password_hash($clave, PASSWORD_DEFAULT) : null; // OK

        
        $stmt = $this->pdo->prepare("SELECT actualizar_usuario(:id, :nombre, :id_tipo_usuario, :nueva_contrasena)");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':id_tipo_usuario', $id_tipo_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':nueva_contrasena', $hashed_password); // CAMBIO: Usar ':nueva_contrasena'

        if ($stmt->execute()) {
            $result = $stmt->fetchColumn();
            if ($result === true || $result === 't') {
                return ['success' => true, 'message' => 'Usuario actualizado correctamente.'];
            } else {
                return ['success' => false, 'message' => 'No se realizaron cambios o el usuario no fue encontrado (SP).'];
            }
        } else {
            return ['success' => false, 'message' => 'No se pudo actualizar el usuario a través del procedimiento almacenado.'];
        }

    } catch (PDOException $e) {
        error_log("Error al actualizar usuario (SP actualizar_usuario): " . $e->getMessage());
        return ['success' => false, 'message' => 'Error de base de datos al actualizar usuario.'];
    }
}
// Dentro de la clase GestorUsuarios// Este método parece duplicar funcionalidad con registrarUsuario y actualizarUsuario.
// Generalmente, es mejor tener métodos específicos para cada acción.
// Si lo mantienes, debe llamar a los métodos específicos.
public function procesarUsuario($id, $nombre, $clave, $tipoUsuarioId, $accion) {
    try {
        if ($accion === 'register') {
            // Llama a registrarUsuario que ya maneja hashing y SP.
            return $this->registrarUsuario($nombre, $clave, $tipoUsuarioId);

        } elseif ($accion === 'update') {
            // Llama a actualizarUsuario que ya maneja hashing y SP.
            return $this->actualizarUsuario($id, $nombre, $clave, $tipoUsuarioId);

        } else {
            throw new Exception("Acción de usuario no válida.");
        }
    } catch (Exception $e) { // Captura Exception, no solo PDOException
        error_log("Error al procesar usuario: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error de procesamiento: ' . $e->getMessage()];
    }
}
 // ✅ Solo este método fue modificado para usar el SP en PostgreSQL
    public function eliminarUsuario($id) {
    try {
        $stmt = $this->pdo->prepare("CALL sp_eliminar_usuario(:id)");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return ['success' => true, 'message' => 'Usuario eliminado con éxito.'];
    } catch (PDOException $e) {
        // Si el SP lanza EXCEPTION (como cuando no existe el usuario)
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

}