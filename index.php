<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config_sesion.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>


    <meta charset="UTF-8" />
    <title>Aurora Boutique</title>
    <link rel="icon" href="imagenes/AB.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        html, body {
            overflow-x: hidden;
        }

        body {
            background-color: #f8f8f8;
            padding-top: 90px; /* Ajustado para navbar más alto */
        }

        /* NAVBAR */
        nav {
            background: linear-gradient(135deg, #abc1b2 0%, #9bb4a3 100%);
            height: 70px;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 0 0 15px 15px;
            font-weight: 700;
            font-size: 1.2rem;
            color: #fff;
            letter-spacing: 1px;
            user-select: none;
        }

        .nav-left a {
            color: #f1f1f1;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 10px;
            transition: background-color 0.3s ease, color 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .nav-left a i {
            font-size: 1.5rem;
            filter: drop-shadow(1px 1px 1px rgba(0,0,0,0.3));
        }

        .nav-left a:hover {
            background-color: rgba(255, 255, 255, 0.25);
            color: #e6e6e6;
        }

        .nav-right {
            color: #f0f0f0;
            font-size: 1.3rem;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.4);
        }

        /* CARRUSEL (original) */
        .carrusel-container {
            width: 100%;
            height: 360px;
            overflow: hidden;
            position: relative;
        }

        .carrusel {
            display: flex;
            transition: transform 0.5s ease-in-out;
            width: max-content; /* importante para evitar overflow oculto */
        }

        .carrusel img {
            width: 50vw;
            height: 360px;
            object-fit: cover;
            flex-shrink: 0;
            user-select: none;
        }


        /* Segundo carrusel: 1 imagen a la vez, más grande */
        .carrusel-secundario {
            width: 100vw;
            height: 720px; /* Doble altura */
            overflow: hidden;
            position: relative;
            margin-top: 60px;
        }

        .carrusel-secundario .carrusel {
            display: flex;
            width: calc(3 * 100vw); /* 3 imágenes, cada una 100vw */
            transition: transform 0.5s ease-in-out;
            height: 100%;
        }

        .carrusel-secundario .carrusel img {
            width: 100vw;
            height: 720px; /* Doble altura también */
            object-fit: cover; /* Ajusta bien la imagen llenando el espacio */
            user-select: none;
            flex-shrink: 0;
        }


        /* BOTONES */
        .boton {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.4);
            color: white;
            border: none;
            padding: 10px;
            font-size: 1.5rem;
            cursor: pointer;
            z-index: 10;
            border-radius: 50%;
            user-select: none;
        }

        .boton.izquierda {
            left: 20px;
        }

        .boton.derecha {
            right: 20px;
        }

        /* SECCIÓN CATEGORÍAS */
        .categorias-index {
            max-width: 1100px;
            margin: 60px auto;
            padding: 0 20px;
            text-align: center;
        }

        .categorias-index h2 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 30px;
        }

        .categorias-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 30px;
        }

        .categoria-card {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
            padding-bottom: 20px;
        }

        .categoria-card:hover {
            transform: translateY(-5px);
        }

        .categoria-card img {
            width: 100%;
            height: 240px;
            object-fit: cover;
        }

        .categoria-card h3 {
            margin-top: 15px;
            font-size: 1.3rem;
            color: #444;
        }

        .categoria-card p {
            color: #666;
            font-size: 0.95rem;
            padding: 0 15px;
            margin: 10px 0;
        }

        .btn-ver-mas {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 20px;
            background-color: #abc1b2;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            transition: background 0.3s;
        }

        .btn-ver-mas:hover {
            background-color: #8ea393;
        }

        /* CONTENIDO */
        .contenido {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            text-align: center;
        }

        .contenido h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .contenido p {
            margin-bottom: 15px;
            line-height: 1.6;
        }

        /* PIE DE PÁGINA */
        footer {
            background-color: #abc1b2;
            color: white;
            padding: 30px;
            text-align: center;
            margin-top: 60px;
        }

        .suscripcion {
        background-color: #ffffff;
        padding: 50px 20px;
        text-align: center;
        margin: 60px auto;
        border-top: 2px solid #abc1b2;
        border-bottom: 2px solid #abc1b2;
    }

    .suscripcion h2 {
        font-size: 2rem;
        color: #444;
        margin-bottom: 15px;
    }

    .suscripcion p {
        color: #666;
        font-size: 1rem;
        margin-bottom: 25px;
    }

    .btn-suscribirse {
        padding: 12px 30px;
        background-color: #abc1b2;
        color: white;
        text-decoration: none;
        border-radius: 25px;
        font-weight: bold;
        font-size: 1rem;
        transition: background 0.3s;
    }

    .btn-suscribirse:hover {
        background-color: #8ea393;
    }

    </style>
</head>
<body>

    <nav>
        <div class="nav-left">
            <a href="cliente/login_cliente.php">
                <i class="fas fa-user-circle"></i> Iniciar Sesión
            </a>
        </div>
        <div class="nav-right">Aurora Boutique</div>
    </nav>

    <!-- CARRUSEL 1 -->
    <div class="carrusel-container">
        <button class="boton izquierda" onclick="moverCarrusel(-1)">&#10094;</button>
        <button class="boton derecha" onclick="moverCarrusel(1)">&#10095;</button>

        <div class="carrusel" id="carrusel">
            <img src="imagenes/portada/fotocarrusel1.jpg" alt="Imagen 1" />
            <img src="imagenes/portada/fotocarrusel2.jpg" alt="Imagen 2" />
            <img src="imagenes/portada/fotocarrusel3.jpg" alt="Imagen 3" />
            <img src="imagenes/portada/fotocarrusel4.jpg" alt="Imagen 4" />
            <img src="imagenes/portada/fotocarrusel5.jpg" alt="Imagen 5" />
            <img src="imagenes/portada/fotocarrusel6.jpg" alt="Imagen 6" />
        </div>
    </div>

    <!-- CATEGORÍAS DESTACADAS -->
    <section class="categorias-index">
        <h2>Categorías</h2>
        <div class="categorias-grid">

            <div class="categoria-card">
                <img src="imagenes/portada/portadamujer.jpg" alt="Mujer" />
                <h3>Prendas disponibles</h3>
                <p>Estilo y elegancia para cada ocasión.</p>
                <a class="btn-ver-mas" href="VentaGeneral/ventageneral.php">Ver</a>
            </div>
        </div>
    </section>


    <!-- CONTENIDO PRINCIPAL -->
    <div class="contenido">
        <h2>Nuestros Productos</h2>
        <p>
            Descubre las últimas tendencias en moda y accesorios. En Aurora Boutique,
            cada prenda está cuidadosamente seleccionada para realzar tu estilo único.
        </p>
        <p>
            Explora nuestras colecciones exclusivas y aprovecha promociones especiales cada semana.
        </p>
    </div>

    <!-- SECCIÓN SUSCRIPCIÓN -->
    <section class="suscripcion">
        <h2>¿Aún no eres parte de Aurora Boutique?</h2>
        <p>Regístrate y recibe nuestras novedades, descuentos y promociones exclusivas.</p>
        <a href="cliente/registrar_cliente.php" class="btn-suscribirse">Suscribirse</a>
    </section>


    <!-- CARRUSEL 2 -->
    <div class="carrusel-container carrusel-secundario">
        <button class="boton izquierda" onclick="moverCarrusel2(-1)">&#10094;</button>
        <button class="boton derecha" onclick="moverCarrusel2(1)">&#10095;</button>

        <div class="carrusel" id="carrusel2">
            <img src="imagenes/portada/hombre3.jpg" alt="Imagen A" />
            <img src="imagenes/portada/hombre2.jpg" alt="Imagen B" />
            <img src="imagenes/portada/hombre4.jpg" alt="Imagen C" />
        </div>
    </div>
    <footer>
        <p>Aurora Boutique &copy; 2025. Todos los derechos reservados.</p>
    </footer>



<script>

    // Variables para carrusel 1
    let indice = 0;
    const carrusel = document.getElementById('carrusel');
    const imagenes = carrusel.querySelectorAll('img');
    const totalImgs = imagenes.length;
    const imagenesPorVista = 2; // 50vw + 50vw = 100vw

    function moverCarrusel(direccion) {
        indice += direccion;
        if (indice < 0) {
            indice = Math.floor((totalImgs - imagenesPorVista));
        }
        if (indice > totalImgs - imagenesPorVista) {
            indice = 0;
        }
        carrusel.style.transform = `translateX(${-indice * 50}vw)`;
    }


    // Variables para segundo 2
    let indice2 = 0;
    const carrusel2 = document.getElementById('carrusel2');
    const totalImgs2 = carrusel2.querySelectorAll('img').length;

    function moverCarrusel2(direccion) {
        indice2 += direccion;
        if (indice2 < 0) {
            indice2 = totalImgs2 - 1;
        }
        if (indice2 > totalImgs2 - 1) {
            indice2 = 0;
        }
        carrusel2.style.transform = `translateX(${-indice2 * 100}vw)`;
    }
</script>

</body>
</html>
