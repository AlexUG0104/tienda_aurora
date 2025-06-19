<?php
require_once '../config_sesion.php';

if (isset($_SESSION['user_id']) && $_SESSION['user_type'] == 2) {
    header("Location: ../VentaGeneral/ventageneral.php");
    exit();
}

$mensaje = '';
if (isset($_SESSION['registro_exitoso'])) {
    $mensaje = $_SESSION['registro_exitoso'];
    unset($_SESSION['registro_exitoso']);
}
if (isset($_SESSION['login_error'])) {
    $mensaje = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" href="imagenes/AB.ico" type="image/x-icon">

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Cliente - Aurora Boutique</title>
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
        .password-wrapper {
            position: relative;
        }
        .password-wrapper i {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #555;
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
        .info-message {
            color: #17a2b8;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .register-link {
            margin-top: 20px;
            font-size: 0.95rem;
        }
        .register-link a {
            color: #007bff;
            text-decoration: none;
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
            <option value="/cliente/login_cliente.php" selected>Iniciar Sesión como Cliente</option>
            <option value="/PersonalEnvío/login.php">Iniciar Sesión como Personal de Envíos</option>
        </select>

    </div>
</nav>

<div class="main-content-wrapper">
    <div class="login-container">
        <h2>Iniciar Sesión como Cliente</h2>

        <?php if (!empty($mensaje)): ?>
            <p class="<?php echo strpos($mensaje, 'error') !== false ? 'error-message' : 'info-message'; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </p>
        <?php endif; ?>

        <form action="procesar_login_cliente.php" method="post">
            <label for="username">Nombre Usuario:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Contraseña:</label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password" required>
                <i class="fas fa-eye" id="togglePassword"></i>
            </div>

            <input type="submit" value="Entrar">
        </form>

        <p class="register-link">
            ¿No tienes cuenta? <a href="/cliente/registrar_cliente.php">Crear cuenta</a>

        </p>
    </div>
</div>

<script>
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });
</script>

</body>
</html>
    