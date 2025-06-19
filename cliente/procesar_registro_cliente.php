<?php
require_once '../config_sesion.php';
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Paso 1: Insertar en credencial
        $sqlCred = "INSERT INTO credencial (nombre, contrasena, id_tipo_usuario) 
            VALUES (:nombre, :contrasena, :id_tipo_usuario)";
        $stmtCred = $pdo->prepare($sqlCred);
        $stmtCred->execute([
            ':nombre' => $_POST['usuario'],
            ':contrasena' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            ':id_tipo_usuario' => 2 // Cliente
        ]);


        // Paso 2: Insertar en cliente (NO PASAMOS id)
        $sqlCliente = "INSERT INTO cliente (identificacion, nombre, apellido, id_credencial)
                       VALUES (:identificacion, :nombre, :apellido, :id_credencial)";
        $stmtCliente = $pdo->prepare($sqlCliente);
        $stmtCliente->execute([
            ':identificacion' => $_POST['identificacion'],
            ':nombre' => $_POST['nombre'],
            ':apellido' => $_POST['apellido'],
            ':id_credencial' => 2
        ]);

        // Obtener correctamente el id del cliente generado
        $cliente_id_stmt = $pdo->query("SELECT currval('cliente_id_seq')");
        $cliente_id = $cliente_id_stmt->fetchColumn();

        // Paso 3: cliente_direccion
        $sqlDireccion = "INSERT INTO cliente_direccion (id_cliente, pais, provincia, canton, distrito, barrio)
                         VALUES (:id_cliente, :pais, :provincia, :canton, :distrito, :barrio)";
        $stmtDireccion = $pdo->prepare($sqlDireccion);
        $stmtDireccion->execute([
            ':id_cliente' => $cliente_id,
            ':pais' => $_POST['pais'],
            ':provincia' => $_POST['provincia'],
            ':canton' => $_POST['canton'],
            ':distrito' => $_POST['distrito'],
            ':barrio' => $_POST['barrio']
        ]);

        // Paso 4: teléfonos
        foreach ($_POST['telefonos'] as $telefono) {
            $sqlTelefono = "INSERT INTO cliente_telefono (id_cliente, numero, tipo) VALUES (:id_cliente, :numero, :tipo)";
            $stmtTelefono = $pdo->prepare($sqlTelefono);
            $stmtTelefono->execute([
                ':id_cliente' => $cliente_id,
                ':numero' => $telefono['numero'],
                ':tipo' => $telefono['tipo']
            ]);
        }

        // Paso 5: correos
        foreach ($_POST['correos'] as $correo) {
            $sqlCorreo = "INSERT INTO cliente_correo (id_cliente, correo, tipo) VALUES (:id_cliente, :correo, :tipo)";
            $stmtCorreo = $pdo->prepare($sqlCorreo);
            $stmtCorreo->execute([
                ':id_cliente' => $cliente_id,
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
        error_log("Error registro cliente: " . $e->getMessage());
        $_SESSION['registro_error'] = "Error al crear la cuenta: " . $e->getMessage();
        header("Location: registrar_cliente.php");
        exit();
    }
} else {
    header("Location: registrar_cliente.php");
    exit();
}
