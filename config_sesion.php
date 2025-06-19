<?php
// config_sesion.php - Configuración centralizada de la sesión y encabezados de caché

session_set_cookie_params([
    'lifetime' => 0,          // La cookie expira al cerrar el navegador
    'path' => '/',            // La cookie es válida para todo el dominio
    'domain' => '',           // Deja vacío para el dominio actual
    // IMPORTANTE: Ajusta 'secure' según tu entorno.
    // true: si estás usando HTTPS (RECOMENDADO para producción)
    // false: si estás en desarrollo local usando HTTP
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', // O solo false para desarrollo HTTP
    'httponly' => true,       // Impide que JavaScript acceda a la cookie (seguridad XSS)
    'samesite' => 'Lax'       // Protección contra CSRF
]);

// Reducido a 60 segundos (1 minuto) para pruebas rápidas de expiración por inactividad
ini_set('session.gc_maxlifetime', 60);

// ¡IMPORTANTE! session_start() DEBE estar aquí y después de las configuraciones
session_start();

// Encabezados HTTP para evitar el caché del navegador
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado lejano
?>