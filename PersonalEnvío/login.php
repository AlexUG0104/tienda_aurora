<?php
require_once '../config_sesion.php';

// Detectar si el usuario ya está logueado y redirigir
if (isset($_SESSION['usuario']) && $_SESSION['tipo_usuario'] == 3) {
    header("Location: pedidos_por_enviar.php");
    exit();
}

$login_type = 'delivery'; // tipo fijo para este login
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Personal de Envíos - Aurora Boutique</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }
        body {
            background-color: #f0f2f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        nav {
            background-color: #abc1b2;
            height: 70px;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .nav-left a {
            color: #333;
            text-decoration: none;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
        }
        .nav-left a i {
            margin-right: 8px;
        }
        .nav-right {
            font-size: 1.1rem;
            color: #333;
            font-weight: bold;
        }
        .login-type-select {
            padding: 8px 12px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1rem;
            background-color: #f9f9f9;
            cursor: pointer;
            outline: none;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .main-content-wrapper {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 70px;
        }
        .login-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 30px;
            color: #333;
            font-size: 2em;
        }
        .login-container label {
            display: block;
            text-align: left;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: calc(100% - 22px);
            padding: 12px;
            margin-bottom: 25px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .login-container input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        .login-container input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: #dc3545;
            margin-bottom: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-left">
            <a href="../index.php"><i class="fas fa-store"></i> Aurora Boutique</a>
        </div>
        <div class="nav-right">
            <select class="login-type-select" onchange="location = this.value;">
                <option value="/admin/login.php">Iniciar Sesión como Administrador</option>
                <option value="/cliente/login_cliente.php" >Iniciar Sesión como Cliente</option>
                <option value="/PersonalEnvío/login.php"selected>Iniciar Sesión como Personal de Envíos</option>
            </select>

        </div>
    </nav>

    <div class="main-content-wrapper">
        <div class="login-container">
            <h2>Iniciar Sesión como Personal de Envíos</h2>
            <?php
            if (isset($_SESSION['login_error'])) {
                echo '<p class="error-message">' . $_SESSION['login_error'] . '</p>';
                unset($_SESSION['login_error']);
            }
            ?>
            <form method="post" action="verificar_login.php">
                <label>Nombre de Usuario:</label>
                <input type="text" name="usuario" required>
                <label>Contraseña:</label>
                <input type="password" name="contrasena" required>
                <input type="submit" value="Ingresar">
            </form>
        </div>
    </div>
</body>
</html>
