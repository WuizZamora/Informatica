<?php
require_once __DIR__ . '/../../../vendor/autoload.php'; // Asegúrate de requerir el autoload de Composer
use Dompdf\Dompdf;
use Dompdf\Options;

if (isset($_GET['IDServicio'])) {
    $idServicio = htmlspecialchars($_GET['IDServicio']);

    // Aquí asumo que ya tienes una instancia de tu modelo
    require './ServicioModel.php';
    $servicioModel = new ServicioModel();
    $data = $servicioModel->consultarServicio($idServicio);

    if ($data) {
        // Crear una nueva instancia de Dompdf
        $dompdf = new Dompdf();

        // Crear el contenido HTML del PDF
        $html = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Reporte de Servicio</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                }
                .container {
                    width: 100%;
                    padding: 20px;
                }
                .row {
                    margin-bottom: 15px;
                }
                .label {
                    font-weight: bold;
                }
                .text-center {
                    text-align: center;
                }
                .border-bottom {
                    border-bottom: 1px solid black;
                    width: 30%;
                    display: inline-block;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1 class="text-center">Reporte de Servicio</h1>
                <div class="row"><div class="col"><span class="label"># Servicio:</span> ' . $data['Pk_IDServicio'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Solicitante:</span> ' . $data['Solicitante'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Entrega:</span> ' . $data['Entrega'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Oficio:</span> ' . $data['Oficio'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Tipo de Servicio:</span> ' . $data['TipoServicio'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Fecha de Solicitud:</span> ' . $data['FechaSolicitud'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Atiende:</span> ' . $data['Atiende'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Folio:</span> ' . $data['Folio'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Fecha de Atención:</span> ' . $data['FechaAtencion'] . '</div></div>';

        // Mostrar campos adicionales según el tipo de servicio
        if ($data['TipoServicio'] === 'ENTREGA MATERIAL FÍLMICO') {
            $html .= '
                <div class="row"><div class="col"><span class="label">Cantidad de videos:</span> ' . $data['CantidadVideos'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Periodo:</span> ' . $data['Periodo'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Periodo Inicial:</span> ' . $data['PeriodoInicial'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Periodo Final:</span> ' . $data['PeriodoFinal'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Equipo:</span> ' . $data['Equipo'] . '</div></div><br><br><br><br><br><br>
                <div class="row text-center"><span class="border-bottom"></span><br>Firma del solicitante</div>';
        } elseif ($data['TipoServicio'] === 'TÉCNICO') {
            $html .= '
                <div class="row"><div class="col"><span class="label">Área solicitante:</span> ' . $data['Area'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Activo:</span> ' . $data['Fk_IDActivo_Activos'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Descripción del Activo:</span> ' . $data['DescripcionActivo'] . '</div></div>
                <div class="row"><div class="col"><span class="label">CABMS:</span> ' . $data['CABMS'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Progresivo:</span> ' . $data['Progresivo'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Descripción del Servicio:</span> ' . $data['Descripcion'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Resultado de la dictaminación:</span> ' . $data['Evaluacion'] . '</div></div><br><br><br><br><br><br>
                <div class="row text-center"><span class="border-bottom"></span><br>LIC. ' . $data['Nombre_JUD_IT'] . '</div>';
        } elseif ($data['TipoServicio'] === 'INCIDENCIA') {
            $html .= '
                <div class="row"><div class="col"><span class="label">Área solicitante:</span> ' . $data['Area'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Servicio solicitado:</span> ' . $data['ServicioSolicitado'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Descripción:</span> ' . $data['Descripcion'] . '</div></div>
                <div class="row"><div class="col"><span class="label">Observaciones:</span> ' . $data['Observaciones'] . '</div></div><br><br><br><br><br><br>
                <div class="row text-center"><span class="border-bottom"></span><br>LIC. ' . $data['Nombre_JUD_IT'] . '</div>';
        }

        $html .= '</div></body></html>';

        // Crear opciones
        $options = new Options();
        $options->set('tempDir', '/tmp'); // Establece el directorio temporal

        // Crear una nueva instancia de Dompdf con las opciones
        $dompdf = new Dompdf($options);

        // Cargar el contenido HTML al Dompdf
        $dompdf->loadHtml($html);

        // Configurar el tamaño y la orientación del papel
        $dompdf->setPaper('A4', 'portrait');

        // Renderizar el PDF
        $dompdf->render();

        // Cabeceras para enviar el PDF al navegador
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="reporte_servicio.pdf"');
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: public');

        // Mostrar el PDF directamente en el navegador
        echo $dompdf->output();
    } else {
        echo "No se encontró el servicio.";
    }
} else {
    echo "ID de servicio no proporcionado.";
}
