<?php
require_once '../config_sesion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" href="imagenes/AB.ico" type="image/x-icon">

    <meta charset="UTF-8" />
    <title>Pedido Exitoso - Aurora Boutique</title>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
            color: #333;
        }

        .success-box {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .success-box h1 {
            color: #28a745;
        }

        .success-box a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .success-box a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="success-box">
    <h1>¡Pedido Realizado con Éxito!</h1>
    <p>Su código de pedido es: <strong><?php echo htmlspecialchars($_GET['codigo']); ?></strong></p>
    <a href="realizar_pedido.php">Realizar otro pedido</a>
</div>

</body>
</html>
