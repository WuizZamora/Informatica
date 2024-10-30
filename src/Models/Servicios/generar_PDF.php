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
                body {font-family: Arial, sans-serif; margin: 0; padding: 0;}

                .header img{margin:0 0.5rem 0;}

                .texto {font-size: 0.8rem; padding:1rem; font-weight: bold; align-items: center;}

                .sub-texto{text-align: center;font-size: 0.7rem;}

                .content {padding: 0.8rem;font-size: 1rem;}

                .content-firma {padding: 1rem; font-size: 1rem; font-weight: bold; text-align: center;}
                
                .section-title {font-weight: bold; text-align: center;}
                    
                .parent-container {display: flex; align-items: center; text-align: right;}

                .content-folio {font-weight: bold; border: solid;display: inline-block;}

                .table th{text-align: justify;}

                .table td {padding: 0.8rem; max-width: 30rem; word-wrap: break-word; white-space: normal; vertical-align: top;}
                
                .footer {position: fixed; bottom: 0; left: 0; width: 100%; height: 3rem; display: flex; align-items: center; padding: 0 1rem; box-sizing: border-box;}

                .footer img {position: absolute; right: 1rem; bottom: 1rem; height: 1rem; object-fit: contain;}
            </style>
        </head>
        <body>
            <div class="header">
                <img src="http://localhost/INFORMATICA/public/images/logo_cdmx_new.png" alt="LOGO CDMX" width="120">
                <span class="texto">INSTITUTO DE VERIFICACIÓN ADMINISTRATIVA DE LA CDMX </span>
                <img src="http://localhost/INFORMATICA/public/images/logo_identidad_gris.png" alt="LOGO INVEA" width="120">
            </div>
            <div class="sub-texto">DIRECCIÓN DE ADMINISTRACIÓN Y FINANZAS</div>
            <hr>
            <div class="content">
            <div class="parent-container">
            <div class="content-folio">Folio: ' . $data[0]['Folio'] . '</div>
            </div>
            <div><strong>Área:</strong> Unidad Departamental de Tecnologías de la información</div>
            <table class="table">
                <tr>
                    <th>Solicitante</th>
                    <td colspan="3">' . $data[0]['Solicitante'] . '</td>
                </tr>';
        if ($data[0]['TipoServicio'] != 'ENTREGA MATERIAL FÍLMICO') {
            $html .= '<tr>
                    <th>Área solicitante</th>
                    <td colspan="3">' . $data[0]['Area'] . '</td>
                </tr>';
        }
        // Verificar si el valor de 'Oficio' no es 'S/O'
        if ($data[0]['Oficio'] !== 'S/O') {
            $html .= '
                        <tr>
                            <th>Oficio</th>
                            <td colspan="3">' . $data[0]['Oficio'] . '</td>
                        </tr>';
        }

        $html .= '<tr>
                    <th>Tipo de Servicio</th>
                    <td colspan="3">' . $data[0]['TipoServicio'] . '</td>
                </tr>
                <tr>
                    <th>Fecha de Solicitud</th>
                    <td>' . $data[0]['FechaSolicitud'] . '</td>
                    <th>Fecha de Atención</th>
                    <td>' . $data[0]['FechaAtencion'] . '</td>
                </tr>
                <tr>
                    <th>Atiende</th>
                    <td colspan="3">' . $data[0]['Atiende'] . '</td>
                </tr>
        ';

        // Agregar Observaciones solo si no está vacío
        if (!empty($data[0]['Observaciones'])) {
            $html .= '
            <tr>
                <th>Observaciones</th>
                <td colspan="3">' . $data[0]['Observaciones'] . '</td>
            </tr>
            ';
        }

        $html .= '</tbody>
        </table>
        <hr>
';
        // Mostrar campos adicionales según el tipo de servicio
        if ($data[0]['TipoServicio'] === 'ENTREGA MATERIAL FÍLMICO') {
            $html .= '
                <div class="section-title">DETALLES DEL MATERIAL FÍLMICO</div>
                <table class="table">
                    <tr>
                        <th>Cantidad de videos</th>
                        <td>' . $data[0]['CantidadVideos'] . '</td>
                        <th>Periodo</th>
                        <td>' . $data[0]['Periodo'] . '</td>
                    </tr>
                    <tr>
                        <th>Periodo Inicial</th>
                        <td>' . $data[0]['PeriodoInicial'] . '</td>
                            <th>Periodo Final</th>
                        <td>' . $data[0]['PeriodoFinal'] . '</td>
                    </tr>
                    <tr>
                        <th>Equipo</th>
                        <td>' . $data[0]['Equipo'] . '</td>
                    </tr>';
            if (isset($data[0]['DescripcionVideo']) && trim($data[0]['DescripcionVideo']) !== '') {
                $html .= '
                            <tr>
                                <th>Descripción</th>
                                <td>' . $data[0]['DescripcionVideo'] . '</td>
                            </tr>
                        ';
            }
            $html .= '<br><br><br>
                </table>
                <div class="content-firma">
                ___________________________________________<br>Firma del solicitante
                </div>';
        } elseif ($data[0]['TipoServicio'] === 'TÉCNICO') {
            $html .= '
            <div class="section-title">DETALLES DEL SERVICIO TÉCNICO</div>
            <table style="width: 100%; border-collapse: collapse; text-align:center; margin:1rem;">
                <thead>
                    <tr>
                        <th style="padding: 8px;background-color: #f2f2f2;">#</th>
                        <th style="padding: 8px;background-color: #f2f2f2;">ID Activo</th>
                        <th style="padding: 8px;background-color: #f2f2f2;">Descripción</th>
                        <th style="padding: 8px;background-color: #f2f2f2;">Numero de Inventario</th>
                        <th style="padding: 8px;background-color: #f2f2f2;">Estado</th>
                    </tr>
                </thead>
                <tbody>';

            // Inicializar el contador
            $counter = 1;

            // Iterar sobre los datos del servicio técnico
            foreach ($data as $item) {
                // Formatear el Progresivo como un número de 6 dígitos
                $progresivoFormateado = str_pad($item['Progresivo'], 6, '0', STR_PAD_LEFT);

                $html .= '
                    <tr>
                        <td style="padding: 8px;">' . $counter . '</td>
                        <td style="padding: 8px;">' . $item['Fk_IDActivo_Activos'] . '</td>
                        <td style="padding: 8px;">' . $item['DescripcionActivo'] . '</td>
                        <td style="padding: 8px;">' . $item['CABMS'] . '-' . $progresivoFormateado . '</td>
                        <td style="padding: 8px;">' . $item['Evaluacion'] . '</td>
                    </tr>';

                // Incrementar el contador
                $counter++;
            }

            $html .= '
            <tr style="text-align: justify;">
                <th style="text-align: justify;" colspan="2">Descripción del servicio:</th>
                <td colspan="4" style="max-width: 30rem; word-wrap: break-word; white-space: normal;">' . $data[0]['DescripcionTecnico'] . '</td>
            </tr>
            </tbody>
            </table><br><br><br>';

            if ($data[0]['Evaluacion'] === 'NO FUNCIONAL') {
                $html .= '
                <div class="content-firma">
                ___________________________________________<br>LIC. ' . $data[0]['Nombre_JUD_IT'] . '
                </div>';
            } else {
                $html .= '
                <div class="content-firma">
                ___________________________________________<br>Firma del solicitante
                </div>';
            }
        } elseif ($data[0]['TipoServicio'] === 'INCIDENCIA') {
            $html .= '
                <div class="section-title">DETALLES DE LA INCIDENCIA</div>
                <table class="table">
                    <tr>
                        <th>Servicio solicitado</th>
                        <td>' . $data[0]['ServicioSolicitado'] . '</td>
                    </tr>
                    <tr>
                        <th>Descripción</th>
                        <td>' . $data[0]['DescripcionIncidencia'] . '</td>
                    </tr>
                </table><br><br><br>
                <div class="content-firma">
                ___________________________________________<br>Firma del solicitante
                </div>';
        }

        $html .= '</div>
        <script type="text/php">
            if (isset($pdf)) {
                $x = 20;
                $y = 800;
                $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
                $font = null;
                $size = 9;
                $color = array(0,0,0);
                $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
            }
        </script>
        <div class="footer"> 
            <img src="http://localhost/INFORMATICA/public/images/Ciudad_Innovadora.jpg" alt="Logo Footer">
        </div>
        </body>
        </html>';

        // Opciones de Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('tempDir', '/tmp');

        $options->set('isPhpEnabled', TRUE);

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
