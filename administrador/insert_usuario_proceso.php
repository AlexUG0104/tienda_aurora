<?php
require '../db.php';

$nombre = $_POST['nombre'];
$clave = $_POST['clave'];
$tipo_usuario = $_POST['id_credencial'];

try {
    $sql = "SELECT insertar_usuario(:nombre, :clave, :tipo_usuario) AS new_user_id";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':nombre' => $nombre,
        ':clave' => $clave,
        ':tipo_usuario' => $tipo_usuario
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $newUserId = $result['new_user_id'];

    echo "Usuario insertado con éxito. ID: " . $newUserId . ". <a href='../index.php'>Volver al menú</a>";

} catch (PDOException $e) {
    echo "Error al insertar el usuario: " . $e->getMessage();
}
?>