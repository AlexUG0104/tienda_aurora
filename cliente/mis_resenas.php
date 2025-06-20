<?php
require_once '../config_sesion.php';
require_once '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: ../cliente/login_cliente.php");
    exit;
}

$id_credencial = (int) $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM obtener_resenas_cliente(:credencial)");
    $stmt->execute(['credencial' => $id_credencial]);
    $resenas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener reseñas: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Reseñas - Aurora Boutique</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f2f4f5;
            margin: 0;
            padding: 40px 20px;
        }
        .contenedor {
            max-width: 1100px;
            margin: 0 auto;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        h2 {
            color: #2f4f4f;
            margin: 0;
        }
        .btn-regresar {
            background-color: #abc1b2;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.2s ease-in-out;
        }
        .btn-regresar:hover {
            background-color: #8ba996;
        }
        .resena-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .resena-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .codigo {
            font-weight: bold;
            color: #4b635c;
        }
        .productos-lista {
            margin-top: 10px;
            padding-left: 20px;
        }
        .productos-lista li {
            list-style-type: disc;
            color: #333;
            margin-bottom: 4px;
        }
        .comentario {
            font-size: 0.95rem;
            color: #444;
        }
        .fecha {
            font-size: 0.85rem;
            color: #777;
        }
        .estrellas {
            color: #f5b301;
        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="top-bar">
        <h2><i class="fas fa-star"></i> Mis Reseñas</h2>
        <a class="btn-regresar" href="../VentaGeneral/ventageneral.php"><i class="fas fa-arrow-left"></i> Regresar</a>
    </div>

    <?php if (empty($resenas)): ?>
        <p>No has realizado ninguna reseña todavía.</p>
    <?php else: ?>
        <?php foreach ($resenas as $resena): ?>
            <div class="resena-card">
                <div class="resena-header">
                    <span class="codigo">Pedido: <?= htmlspecialchars($resena['codigo_pedido']) ?></span>
                    <span class="fecha"><?= htmlspecialchars($resena['fecha_reseña']) ?></span>
                </div>

                <div>
                    <strong>Productos:</strong>
                    <ul class="productos-lista">
                        <?php
                        $productos = explode(', ', $resena['productos']);
                        foreach ($productos as $producto) {
                            echo "<li>" . htmlspecialchars($producto) . "</li>";
                        }
                        ?>
                    </ul>
                </div>

                <div class="comentario"><?= htmlspecialchars($resena['comentario']) ?></div>
                <div class="estrellas">
                    <?php
                    $calificacion = (int)$resena['calificacion'];
                    for ($i = 0; $i < 5; $i++) {
                        echo $i < $calificacion ? '★' : '☆';
                    }
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
