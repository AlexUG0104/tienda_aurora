<?php
require_once '../config_sesion.php';
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Por favor, ingresa usuario y contraseña.";
        header("Location: login_cliente.php");
        exit();
    }

    try {
        $sql = "SELECT id, nombre, contrasena, id_tipo_usuario FROM credencial WHERE nombre = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Usar password_verify para comparar el hash
        if ($user && password_verify($password, $user['contrasena'])) {
            if ($user['id_tipo_usuario'] == 2) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_type'] = 2;

                header("Location: ../VentaGeneral/ventageneral.php");

                exit();
            } else {
                $_SESSION['login_error'] = "No tienes permisos de cliente.";
            }
        } else {
            $_SESSION['login_error'] = "Usuario o contraseña incorrectos.";
        }

        header("Location: login_cliente.php");
        exit();

    } catch (PDOException $e) {
        error_log("Error login cliente: " . $e->getMessage());
        $_SESSION['login_error'] = "Ha ocurrido un error. Intenta más tarde.";
        header("Location: login_cliente.php");
        exit();
    }
} else {
    header("Location: login_cliente.php");
    exit();
}
