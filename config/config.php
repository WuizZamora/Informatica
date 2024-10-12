<?php
// Nombre del sitio
define('SITE_NAME', 'INFORMATICA | INVEA');

// Rutas base
define('BASE_URL', 'http://localhost/INFORMATICA/');
define('BASE_PATH', __DIR__ . '/../'); 
define('ASSETS_URL', BASE_URL . 'public/');


// Modo de desarrollo (habilita o deshabilita la visualización de errores)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// // Configuración de sesiones
// ini_set('session.cookie_lifetime', 86400); // 1 día
// ini_set('session.gc_maxlifetime', 86400);  // 1 día

// Roles de usuario
define('ADMIN_ROLE', 1);
define('USER_ROLE', 2);

// Configuración de paginación
define('RESULTS_PER_PAGE', 20);
?>
