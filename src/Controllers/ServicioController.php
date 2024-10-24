<?php
class ServicioController
{
    public function index($rol)
    {
        // Lógica para obtener servicios
        include './src/Views/Servicios/servicios.php';
    }

    public function informeServicios($rol)
    {
        // Lógica para obtener servicios
        include './src/Views/Servicios/informe_servicios.php';
    }
}

?>