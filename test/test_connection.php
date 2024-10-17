<?php
require_once __DIR__ . '/config/conexion.php'; // Asegúrate de que la ruta sea correcta

$conn = Conexion::conectar();

if ($conn) {
    echo "Conexión exitosa";
} else {
    echo "Fallo en la conexión";
}
?>
