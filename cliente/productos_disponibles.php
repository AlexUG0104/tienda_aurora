<?php
// -----------------------------------------------------------------------------
// INICIO: Configuración inicial y conexión a la base de datos
// -----------------------------------------------------------------------------

// Iniciar sesión y cargar configuración (verifica que esta ruta sea correcta)
require_once '../config_sesion.php';

// Conexión a la base de datos (usando PDO, definido en db.php)
require_once '../db.php';

// -----------------------------------------------------------------------------
// SECCIÓN: Obtener productos disponibles desde la base de datos
// -----------------------------------------------------------------------------
//
// Esta consulta trae:
// - Todos los productos cuyo stock es mayor a 0
// - Información adicional como talla, color, categoría, descripción, etc.
// - Los resultados están ordenados alfabéticamente por nombre
//
// ⚠️ IMPORTANTE:
// Si en el futuro deseas usar una vista, función o procedimiento almacenado,
// solo necesitas modificar esta parte (la variable $sql y su ejecución).

try {
    $sql = "SELECT 
                p.codigo_producto,
                p.nombre,
                p.descripcion,
                p.precio_unitario,
                p.stock,
                p.talla,
                p.color,
                c.nombre AS categoria
            FROM producto p
            LEFT JOIN categoria c ON p.id_categoria = c.id
            WHERE p.stock > 0
            ORDER BY p.nombre ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Guardamos los productos en un arreglo asociativo
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si ocurre un error, se registra en el log y se carga un arreglo vacío
    error_log("Error al obtener productos: " . $e->getMessage());
    $productos = [];
}

// -----------------------------------------------------------------------------
// FIN de la lógica de base de datos
// -----------------------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Productos Disponibles - Aurora Boutique</title>

    <!-- Íconos y fuentes externas -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />

    <!-- Estilos CSS internos -->
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background-color: #f0f2f5;
            padding: 20px;
        }

        /* Barra de navegación */
        nav {
            background-color: #abc1b2;
            height: 70px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            margin-bottom: 20px;
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

        /* Título principal */
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 2em;
        }

        /* Grid de productos responsivo */
        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        /* Tarjetas de producto */
        .producto-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 20px;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .producto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.15);
        }

        .producto-card h3 {
            color: #007bff;
            margin-bottom: 10px;
        }

        .producto-card p {
            color: #555;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .producto-precio {
            color: #28a745;
            font-weight: bold;
            font-size: 1.2rem;
            margin-top: auto;
        }

        /* Diseño adaptable para móviles */
        @media (max-width: 480px) {
            nav {
                flex-direction: column;
                height: auto;
                padding: 10px 15px;
            }
        }
    </style>
</head>
<body>

    <!-- Barra de navegación -->
    <nav>
        <div class="nav-left">
            <a href="index.php"><i class="fas fa-home"></i> Aurora Boutique - Cliente</a>
        </div>
        <div class="nav-right">
            Bienvenido <?php echo $_SESSION['user_name'] ?? 'Cliente'; ?>
        </div>
    </nav>

    <!-- Título principal -->
    <h2>Productos Disponibles</h2>

    <!-- Contenedor de productos -->
    <div class="productos-grid">
        <?php if (count($productos) > 0): ?>
            <?php foreach ($productos as $producto): ?>
                <div class="producto-card">
                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                    <p><strong>Código:</strong> <?php echo htmlspecialchars($producto['codigo_producto']); ?></p>
                    <p><strong>Categoría:</strong> <?php echo htmlspecialchars($producto['categoria'] ?? 'Sin categoría'); ?></p>
                    <p><strong>Descripción:</strong> <?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
                    <p><strong>Talla:</strong> <?php echo htmlspecialchars($producto['talla']); ?></p>
                    <p><strong>Color:</strong> <?php echo htmlspecialchars($producto['color']); ?></p>
                    <p><strong>Stock disponible:</strong> <?php echo htmlspecialchars($producto['stock']); ?></p>
                    <div class="producto-precio">
                        ₡<?php echo number_format($producto['precio_unitario'], 2, ',', '.'); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay productos disponibles en este momento.</p>
        <?php endif; ?>
    </div>

</body>
</html>
