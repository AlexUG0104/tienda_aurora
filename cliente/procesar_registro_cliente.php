<?php
require_once '../config_sesion.php';
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validación: usuario duplicado
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM credencial WHERE nombre = :nombre");
        $stmt->execute([':nombre' => $_POST['usuario']]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['registro_error'] = "El nombre de usuario ya está registrado.";
            header("Location: registrar_cliente.php");
            exit();
        }

        // Validación: identificación duplicada
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cliente WHERE identificacion = :identificacion");
        $stmt->execute([':identificacion' => $_POST['identificacion']]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['registro_error'] = "Ya existe un cliente con esa identificación.";
            header("Location: registrar_cliente.php");
            exit();
        }

        $pdo->beginTransaction();

        // Paso 1: Crear credencial usando función
        $stmtCred = $pdo->prepare("SELECT sp_insertar_credencial(:nombre, :contrasena, :id_tipo_usuario)");
        $stmtCred->execute([
            ':nombre' => $_POST['usuario'],
            ':contrasena' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            ':id_tipo_usuario' => 2 // Cliente
        ]);
        $id_credencial = $stmtCred->fetchColumn();

        // Paso 2: Crear cliente usando función
        $stmtCliente = $pdo->prepare("SELECT sp_insertar_cliente(:identificacion, :nombre, :apellido, :id_credencial)");
        $stmtCliente->execute([
            ':identificacion' => $_POST['identificacion'],
            ':nombre' => $_POST['nombre'],
            ':apellido' => $_POST['apellido'],
            ':id_credencial' => $id_credencial
        ]);
        $id_cliente = $stmtCliente->fetchColumn();

        // ✅ Resolver el país seleccionado
        $pais_final = ($_POST['pais'] === 'Otro') ? $_POST['pais_otro'] : $_POST['pais'];

        // Paso 3: Dirección con procedure
        $stmtDireccion = $pdo->prepare("CALL sp_insertar_cliente_direccion(:id_cliente, :pais, :provincia, :canton, :distrito, :barrio)");
        $stmtDireccion->execute([
            ':id_cliente' => $id_cliente,
            ':pais' => $pais_final,
            ':provincia' => $_POST['provincia'],
            ':canton' => $_POST['canton'],
            ':distrito' => $_POST['distrito'],
            ':barrio' => $_POST['barrio']
        ]);

        // Paso 4: Teléfonos
        $stmtTelefono = $pdo->prepare("CALL sp_insertar_cliente_telefono(:id_cliente, :numero, :tipo)");
        foreach ($_POST['telefonos'] as $telefono) {
            $stmtTelefono->execute([
                ':id_cliente' => $id_cliente,
                ':numero' => $telefono['numero'],
                ':tipo' => $telefono['tipo']
            ]);
        }

        // Paso 5: Correos
        $stmtCorreo = $pdo->prepare("CALL sp_insertar_cliente_correo(:id_cliente, :correo, :tipo)");
        foreach ($_POST['correos'] as $correo) {
            $stmtCorreo->execute([
                ':id_cliente' => $id_cliente,
                ':correo' => $correo['correo'],
                ':tipo' => $correo['tipo']
            ]);
        }

        $pdo->commit();
        $_SESSION['registro_exitoso'] = "Cuenta creada exitosamente. Por favor inicia sesión.";
        header("Location: login_cliente.php");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error en el registro de cliente: " . $e->getMessage());
        $_SESSION['registro_error'] = "Error al crear la cuenta: " . $e->getMessage();
        header("Location: registrar_cliente.php");
        exit();
    }
} else {
    header("Location: registrar_cliente.php");
    exit();
}
