<?php
class ServicioController
{
    public function index($rol)
    {
        // Lógica para obtener servicios
        include './src/Views/Servicios/servicios.php';
    }
}

?>