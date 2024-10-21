<?php
require_once __DIR__ . '/../../../vendor/autoload.php'; // Asegúrate de requerir el autoload de Composer
use Dompdf\Dompdf;
use Dompdf\Options;

if (isset($_GET['IDServicio'])) {
    $idServicio = htmlspecialchars($_GET['IDServicio']);

    // Asumimos que ya tienes una instancia de tu modelo
    require './ServicioModel.php';
    $servicioModel = new ServicioModel();
    $data = $servicioModel->consultarServicio($idServicio);

    if ($data) {
        // Crear una nueva instancia de Dompdf
        $dompdf = new Dompdf();

        // Contenido HTML del PDF
        $html = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Reporte de Servicio</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                }

                .header {
                    display: flex;
                    align-items: center; /* Centra verticalmente los elementos */
                }

                .header img{
                    margin:0 0.5rem 0;     
                }

                .texto {
                    font-size: 0.8rem; /* Ajusta el tamaño de la fuente según sea necesario */
                    padding:1rem;
                    font-weight: bold;
                    align-items: center;
                }

                .sub-texto{
                    text-align: center;
                    font-size: 0.7rem;
                }

                .content {
                    padding: 0.8rem;
                    font-size: 1rem;
                }

                .content-firma {
                    padding: 1rem;
                    font-size: 1rem;
                    font-weight: bold;
                    text-align: center;

                }
                .section-title {
                    font-weight: bold;
                    text-align: center;
                }
                    
                .parent-container {
                    text-align: right; /* Alinea el contenido hijo a la derecha */
                }

                .content-folio {
                    font-weight: bold;
                    border: solid;
                    display: inline-block; /* Ajusta el borde al texto */
                }

                .table {
                    border:none;
                }

                .table th, .table td {
                    border: none;
                    padding: 0.8rem;
                    text-align: center;
                }

                .table td {
                    border: none;
                    padding: 0.8rem;
                    text-align: center;
                    max-width: 30rem; /* Cambia este valor según sea necesario */
                    word-wrap: break-word; /* Permite que el texto se ajuste */
                    white-space: normal; /* Permite múltiples líneas */
                    vertical-align: top; /* Alinea el texto en la parte superior de la celda */
                }


                .table th {
                    background-color: #f2f2f2;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="http://localhost/INFORMATICA/public/images/logo_cdmx.png" alt="LOGO CDMX" width="120">
                <span class="texto">INSTITUTO DE VERIFICACIÓN ADMINISTRATIVA DE LA CDMX </span>
                <img src="http://localhost/INFORMATICA/public/images/logo_identidad_gris.png" alt="LOGO INVEA" width="120">
            </div>
            <div class="sub-texto">DIRECCIÓN DE ADMINISTRACIÓN</div>
            <hr>
            <div class="content">
                <div class="parent-container">
                    <div class="content-folio">Folio:' . $data['Folio'] . '</div>
                </div>
                <table class="table">
                    <tr>
                        <th>Solicitante</th>
                        <td>' . $data['Solicitante'] . '</td>
                    </tr>
                    <tr>
                        <th>Entrega</th>
                        <td>' . $data['Entrega'] . '</td>
                    </tr>
                    <tr>
                        <th>Oficio</th>
                        <td>' . $data['Oficio'] . '</td>
                    </tr>
                    <tr>
                        <th>Tipo de Servicio</th>
                        <td>' . $data['TipoServicio'] . '</td>
                    </tr>
                    <tr>
                        <th>Fecha de Solicitud</th>
                        <td>' . $data['FechaSolicitud'] . '</td>
                    </tr>
                    <tr>
                        <th>Atiende</th>
                        <td>' . $data['Atiende'] . '</td>
                    </tr>
                    <tr>
                        <th>Fecha de Atención</th>
                        <td>' . $data['FechaAtencion'] . '</td>
                    </tr>
                </table>
                <hr>';

        // Mostrar campos adicionales según el tipo de servicio
        if ($data['TipoServicio'] === 'ENTREGA MATERIAL FÍLMICO') {
            $html .= '
                <div class="section-title">DETALLES DEL MAETERIAL FÍLMICO</div>
                <table class="table">
                    <tr>
                        <th>Cantidad de videos</th>
                        <td>' . $data['CantidadVideos'] . '</td>
                    </tr>
                    <tr>
                        <th>Periodo</th>
                        <td>' . $data['Periodo'] . '</td>
                    </tr>
                    <tr>
                        <th>Periodo Inicial</th>
                        <td>' . $data['PeriodoInicial'] . '</td>
                    </tr>
                    <tr>
                        <th>Periodo Final</th>
                        <td>' . $data['PeriodoFinal'] . '</td>
                    </tr>
                    <tr>
                        <th>Equipo</th>
                        <td>' . $data['Equipo'] . '</td>
                    </tr>
                    <tr>
                        <th>Descripción</th>
                        <td>' . $data['DescripcionVideo'] . '</td>
                    </tr>
                </table><br><br>
                <div class="content-firma">
                ___________________________________________<br>Firma del solicitante
                </div>';
        } elseif ($data['TipoServicio'] === 'TÉCNICO') {
            $html .= '
                <div class="section-title">DETALLES DEL SERVICIO TÉCNICO</div>
                <table class="table">
                    <tr>
                        <th>Área solicitante</th>
                        <td>' . $data['Area'] . '</td>
                    </tr>
                    <tr>
                        <th>Activo</th>
                        <td>' . $data['Fk_IDActivo_Activos'] . '</td>
                    </tr>
                    <tr>
                        <th>Descripción del Activo</th>
                        <td>' . $data['DescripcionActivo'] . '</td>
                    </tr>
                    <tr>
                        <th>CABMS</th>
                        <td>' . $data['CABMS'] . '</td>
                    </tr>
                    <tr>
                        <th>Progresivo</th>
                        <td>' . $data['Progresivo'] . '</td>
                    </tr>
                    <tr>
                        <th>Descripción del Servicio</th>
                        <td>' . $data['DescripcionTecnico'] . '</td>
                    </tr>
                    <tr>
                        <th>Resultado de la dictaminación</th>
                        <td>' . $data['Evaluacion'] . '</td>
                    </tr>
                </table><br>
                <div class="content-firma">
                ___________________________________________<br>LIC. ' . $data['Nombre_JUD_IT'] . '
                </div>';
        } elseif ($data['TipoServicio'] === 'INCIDENCIA') {
            $html .= '
                <div class="section-title">DETALLES DE LA INCIDENCIA</div>
                <table class="table">
                    <tr>
                        <th>Área solicitante</th>
                        <td>' . $data['Area'] . '</td>
                    </tr>
                    <tr>
                        <th>Servicio solicitado</th>
                        <td>' . $data['ServicioSolicitado'] . '</td>
                    </tr>
                    <tr>
                        <th>Descripción</th>
                        <td>' . $data['DescripcionIncidencia'] . '</td>
                    </tr>
                    <tr>
                        <th>Observaciones</th>
                        <td>' . $data['Observaciones'] . '</td>
                    </tr>
                </table><br>
                <div class="content-firma">
                ___________________________________________<br>Firma del solicitante
                </div>';
        }

        $html .= '</div>
        </body>
        </html>';

        // Opciones de Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('tempDir', '/tmp');

        // Crear la instancia de Dompdf con opciones
        $dompdf = new Dompdf($options);

        // Cargar HTML en Dompdf
        $dompdf->loadHtml($html);

        // Configurar el tamaño y la orientación del papel
        $dompdf->setPaper('A4', 'portrait');

        // Renderizar el PDF
        $dompdf->render();

        // Enviar el PDF al navegador
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="reporte_servicio.pdf"');
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: public');

        echo $dompdf->output();
    } else {
        echo "No se encontró el servicio.";
    }
} else {
    echo "ID de servicio no proporcionado.";
}
