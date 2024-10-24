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
                }

                .table td {
                    border: none;
                    padding: 0.8rem;
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
                <div class="content-folio">Folio: ' . $data[0]['Folio'] . '</div>
            </div>
            <table class="table">
                <tr>
                    <th>Solicitante</th>
                    <td>' . $data[0]['Solicitante'] . '</td>
                </tr>';

        // Verificar si el valor de 'Oficio' no es 'S/O'
        if ($data[0]['Oficio'] !== 'S/O') {
            $html .= '
                        <tr>
                            <th>Oficio</th>
                            <td>' . $data[0]['Oficio'] . '</td>
                        </tr>';
        }

        $html .= '<tr>
                    <th>Tipo de Servicio</th>
                    <td>' . $data[0]['TipoServicio'] . '</td>
                </tr>
                <tr>
                    <th>Fecha de Solicitud</th>
                    <td>' . $data[0]['FechaSolicitud'] . '</td>
                </tr>
                <tr>
                    <th>Atiende</th>
                    <td>' . $data[0]['Atiende'] . '</td>
                </tr>
                <tr>
                    <th>Fecha de Atención</th>
                    <td>' . $data[0]['FechaAtencion'] . '</td>
                </tr>
            </table>
            <hr>';

        // Mostrar campos adicionales según el tipo de servicio
        if ($data['TipoServicio'] === 'ENTREGA MATERIAL FÍLMICO') {
            $html .= '
                <div class="section-title">DETALLES DEL MATERIAL FÍLMICO</div>
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
                    </tr>';
            if (isset($data['DescripcionVideo']) && trim($data['DescripcionVideo']) !== '') {
                $html .= '
                            <tr>
                                <th>Descripción</th>
                                <td>' . $data['DescripcionVideo'] . '</td>
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
                    <th style="background-color: #f2f2f2;">Descripción del servicio:</th>
                    <td colspan="5">' . $data[0]['DescripcionTecnico'] . '</td>
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
        }elseif ($data['TipoServicio'] === 'INCIDENCIA') {
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
                </table><br><br><br>
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
