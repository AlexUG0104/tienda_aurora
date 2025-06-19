<?php
require_once '../config_sesion.php';

// Redirección si no es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 1) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" href="imagenes/AB.ico" type="image/x-icon">

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Menú de Acciones - Aurora Boutique</title>
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
        .main-content-wrapper {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 100px;
        }
        .menu-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        .menu-container h2 {
            margin-bottom: 30px;
            color: #333;
            font-size: 2em;
        }
        .menu-option {
            display: block;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 1.2rem;
            transition: background-color 0.3s ease;
        }
        .menu-option:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-left">
            <a href="../index.php"><i class="fas fa-store"></i> Aurora Boutique</a>
        </div>
        <div class="nav-right">
            Administrador
        </div>
    </nav>

    <div class="main-content-wrapper">
        <div class="menu-container">
            <h2>Menú de Acciones</h2>
            <a class="menu-option" href="../administrador/gestionar_usuarios.php"><i class="fas fa-users"></i> Gestionar Usuarios</a>
            <a class="menu-option" href="../administrador/gestionar_productos.php"><i class="fas fa-box"></i> Gestionar Productos</a>
            <a class="menu-option" href="../administrador/gestionar_pedidos.php"><i class="fas fa-clipboard-list"></i> Gestionar Pedidos</a>
            <a class="menu-option" href="../administrador/ver_ventas.php"><i class="fas fa-chart-line"></i> Ver Ventas</a>
            <a class="menu-option" href="../logout_admin.php"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </div>
    </div>
</body>
</html>
