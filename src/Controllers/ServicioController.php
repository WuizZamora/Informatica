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

    public function informeServiciosPasados($rol)
    {
        include './src/Views/Servicios/servicios_pasados_informe_periodos.php';
    }
}