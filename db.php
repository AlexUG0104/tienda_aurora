<?php
$host = '100.87.123.117';
$dbname = 'aurora';   
$user = 'admin';        
$password = '1234';   

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conexión exitosa"; // prueba
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}
?>
