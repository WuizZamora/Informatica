<?php
class ServicioController
{
    public function index($rol)
    {
        include './src/Views/Servicios/servicios.php';
    }

    public function informeServicios($rol)
    {
        include './src/Views/Servicios/servicios_informe_periodos.php';
    }
}