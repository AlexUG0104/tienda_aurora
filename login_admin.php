<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config_sesion.php'; // Esta DEBE ser la primera línea.

// Determinar el tipo de login solicitado (default es administrador)
$login_type = $_GET['type'] ?? 'admin'; // Usar 'admin' como default si no se especifica

// Lógica para forzar el login:
// Si hay un parámetro 'force_login' o si el usuario ya está logueado pero intenta
// acceder a login_admin.php (y no es para cambiar de tipo de usuario)
// y el tipo de login solicitado no es 'admin' o si el usuario actual NO es admin,
// podríamos limpiar la sesión.

// Opción 1: Si se llega a login_admin.php y el usuario ya está logueado,
// se redirige al dashboard correspondiente, A MENOS que se intente cambiar de tipo.
// Para forzar la aparición del login, la opción más sencilla es hacer que el link
// en index.php a login_admin.php incluya un parámetro para destruir la sesión.

// Ejemplo de cómo manejarlo: Si se llega a login_admin.php con un parámetro para "desloguear" antes de login.
// Esto es si quieres que el botón "Iniciar Sesión" en la página principal (index.php)
// te lleve siempre al formulario de login, incluso si ya estás logueado.


if (isset($_GET['force_logout']) && $_GET['force_logout'] === 'true') {
    session_unset();
    session_destroy();
    // Vuelve a iniciar la sesión después de destruirla para que se pueda usar $_SESSION para errores
    session_start();
    // Elimina el parámetro force_logout para no entrar en un bucle
    header("Location: login_admin.php?type=admin");
    exit();
}


// Si el usuario ya está logueado Y NO se solicitó forzar el logout, redirigir según su tipo.
// Esto evita que un usuario logeado vea el formulario de login si ya tiene una sesión válida.
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['user_type']) {
        case 1: // Administrador
            // Si ya es administrador y está en el login de admin, redirigir al panel
            if ($login_type === 'admin') {
                header("Location: administrador/index.php");
                exit();
            }
            // Si es administrador pero intenta ver otro tipo de login, mostrar el selector
            break;
        case 2: // Cliente
            // Si es cliente, redirigir al dashboard de cliente
            header("Location: cliente/login_cliente.php"); // O a clientes/dashboard.php si ya existe
            exit();
        case 3: // Personal de Envíos
            // Si es personal de envíos, redirigir a su panel
            header("Location: PersonalEnvío/login.php"); // O a envios/panel.php si ya existe
            exit();
        default:
            // Si el tipo de usuario no es válido, destruir la sesión y forzar login
            session_unset();
            session_destroy();
            header("Location: index.php"); // Asegúrate de que el login base esté correcto
            exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>    <link rel="icon" href="imagenes/AB.ico" type="image/x-icon">

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Iniciar Sesión - Aurora Boutique</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />

    <style>
        /* Estilos generales */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background-color: #f0f2f5; /* Color de fondo similar a tu login anterior */
            display: flex;
            flex-direction: column; /* Para navbar y contenido */
            min-height: 100vh;
        }

        /* NAVBAR (ajustado de tu CSS) */
        nav {
            background-color: #abc1b2;
            height: 70px;
            width: 100%;
            position: fixed; /* Fijo en la parte superior */
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
            color: #333; /* Cambiado a un color más visible */
            font-weight: bold;
        }
        
        /* Estilos del select de tipo de login */
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
        .login-type-select:hover {
            border-color: #999;
        }
        .login-type-select:focus {
            border-color: #007bff;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1), 0 0 0 3px rgba(0,123,255,0.25);
        }

        /* Contenedor principal para centrar el formulario */
        .main-content-wrapper {
            flex-grow: 1; /* Permite que ocupe el espacio restante */
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 70px; /* Para no quedar debajo del navbar */
        }

        /* Estilos del formulario de login (copiados y ajustados de tu anterior login.php) */
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
            box-sizing: border-box;
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
        .info-message {
            color: #007bff; /* Color azul para mensajes informativos */
            margin-bottom: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <nav>
        <div class="nav-left">
            <a href="index.php"><i class="fas fa-store"></i> Aurora Boutique
            </a>
        </div>
        <div class="nav-right">
            <select class="login-type-select" onchange="location = this.value;">
                <option value="login_admin.php?type=admin" <?php echo ($login_type === 'admin' ? 'selected' : ''); ?>>Iniciar Sesión como Administrador</option>
                <option value="cliente/login_cliente.php" <?php echo ($login_type === 'client' ? 'selected' : ''); ?>>Iniciar Sesión como Cliente</option>
                <option value="PersonalEnvío/login.php" <?php echo ($login_type === 'delivery' ? 'selected' : ''); ?>>Iniciar Sesión como Personal de Envíos</option>
            </select>
        </div>
    </nav>

    <div class="main-content-wrapper">
        <div class="login-container">
            <?php if ($login_type === 'admin'): ?>
                <h2>Iniciar Sesión como Administrador</h2>
                <?php
                if (isset($_SESSION['login_error'])) {
                    echo '<p class="error-message">' . $_SESSION['login_error'] . '</p>';
                    unset($_SESSION['login_error']);
                }
                ?>
                <form action="procesar_login_admin.php" method="post">
                    <input type="hidden" name="login_type" value="admin">
                    <label for="username">Nombre de Usuario:</label>
                    <input type="text" id="username" name="username" required>

                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>

                    <input type="submit" value="Ingresar">
                </form>
            <?php else: ?>
                <h2>Acceso no disponible</h2>
                <p class="info-message">
                    La funcionalidad de inicio de sesión para **<?php 
                        if ($login_type === 'client') echo 'clientes';
                        else if ($login_type === 'delivery') echo 'personal de envíos';
                        else echo 'este tipo de usuario';
                    ?>** aún no está implementada. Por favor, selecciona "Iniciar Sesión como Administrador".
                </p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>