<?php
require_once './src/Models/Servicios/VideosScannerModel.php';

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

    public function reporteQuincenal() 
    {
        include './src/Views/Servicios/reporteQuincenal.php';
    }

    public function scanVideos()
    {
        $tipo = $_GET['tipo'] ?? null;
        $equipo = $_GET['equipo'] ?? null;
        $mes = $_GET['mes'] ?? null;
        $quincena = $_GET['quincena'] ?? null;

        $data = null;
        $rutaBase = null;

        if ($tipo && $equipo && $mes && $quincena) {

            $q = ($quincena == 1) ? '1 QUINCENA-' : '2 QUINCENA-';

            switch ($tipo) {
                case 'ALCALDIAS':
                    $rutaBase = "/mnt/videos_alcaldias/$equipo/$mes/$q";
                    break;

                case 'TRANSPORTE':
                    $rutaBase = "/mnt/videos_transporte/$equipo/$mes/$q";
                    break;

                case 'AMBITO':
                    $rutaBase = "/mnt/videos_ambito/$equipo/$mes/$q";
                    break;
            }

            $model = new VideoScannerModel();
            $data = $model->revisarVideos($rutaBase);
        }

        include './src/Views/Servicios/scanvideos.php';
    }

}