<?php
session_start(); // Iniciar sesión
session_unset(); // Eliminar todas las variables de sesión
session_destroy(); // Destruir la sesión

// Redirigir a la página de inicio de sesión
header('Location: login.php'); // Cambia esto al archivo de tu inicio de sesión
exit();
?>
