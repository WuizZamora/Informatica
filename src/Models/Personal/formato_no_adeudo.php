<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__ . '/../../../vendor/autoload.php';
// ... resto del código
use Dompdf\Dompdf;
use Dompdf\Options;

if (isset($_GET['IDConstancia'])) {
    $IDConstancia = htmlspecialchars($_GET['IDConstancia']);
    // Asumimos que ya tienes una instancia de tu modelo
    require '../Personal/PersonalModel.php';
    $personalModel = new PersonalModel();
    // Crear una nueva instancia de Dompdf
    $dompdf = new Dompdf();

    $data = $personalModel->obtenerConstanciaID($IDConstancia);
    // Contenido HTML del PDF
    $html = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>CONSTANCIA DE NO ADEUDO</title>
        <style>
            body {font-family: "Arial", sans-serif; margin: 0; padding: 0;}
            .texto {font-size: 1rem; padding:1rem; text-align: justify;}
            .footer {position: fixed; bottom: 0; left: 0; width: 100%; height: 4rem; display: flex; align-items: center; padding: 0 1rem; box-sizing: border-box;}
            .footer img { right: 1rem; bottom: 1rem; object-fit: contain; margin-bottom:2rem;}
        </style>
    </head>
    <body>
        <img src="http://localhost/INFORMATICA/public/images/encabezado_2025_corto.png" alt="LOGO CDMX" width="200" height="150">
        <p style="float: right; color: #757575";><strong>INSTITUTO DE VERIFICACIÓN <br>ADMINISTRATIVA</strong><br> DIRECCIÓN DE ADMINISTRACIÓN Y FINANZAS</p>
        <p style="padding-left:23rem;">Ciudad de México, a ' . $data['FechaEmision'] . '<br> <strong>INVEACDMX/DG/DAF/JUDTI/'.$data['OficioTI'].'/2025</strong></p>
        <p class="texto">
        <strong>LCDO. ' . $data['Nombre_Finanzas'] . '</strong><br>Coordinador de Finanzas ' . '<br>
        <strong>PRESENTE</strong><br><br>
        En atención al oficio '.$data['Oficio'].' mediante el cual se remite el formato de Constancia de No Adeudo con folió "'.$data['NumeroConstancia'].'"
        y solicita el sello y firma correspondiente de esta Unidad Departamental de Tecnologías de la Información a favor de el (la) C. <strong>'.$data['Nombre'].'</strong>, 
        por la separación al cargo de <strong>'.$data['Puesto'].'</strong>, a partir del día '.$data['FechaSeparacion'].'.
        <br><br>
        Por lo anterior, remito a usted el formato Constancia de No Adeudo con folio "'.$data['NumeroConstancia'].'" debidamente sellado y firmado para los trámites correspondientes.
        <br><br>
        Cabe señalar que se hicieron los trabajos técnicos conducentes para asegurar que no tenga acceso a los equipos de cómputo mediante el cambio de contraseña asignada para sus labores en las oficinas de este Instituto.
        <br><br>
        Sin más por el momento, le envío un cordial saludo.
        <br><br><br>
        <strong>A T E N T A M E N T E<br>
        JEFE DE UNIDAD DEPARTAMENTAL<br>TECNOLOGÍAS DE LA INFORMACIÓN
        <br><br><br><br><br><br>'. $data['Nombre_JUD_IT'] . '<br>
        </strong>
        <p class="texto">C.c.c.e.p.-'.$data['Director_DAF'].'. Director de Administración y Finanzas.</p>
    ';

    $html .= '</div>
    <script type="text/php">
        if (isset($pdf)) {
            $x = 280;
            $y = 770;
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $font = null;
            $size = 9;
            $color = array(0,0,0);
            $pdf->page_text($x, $y, $text, $font, $size, $color);
        }
    </script>
    <img src="http://localhost/INFORMATICA/public/images/logo_2025_newww.png" alt="Logo Tenochtitlan" width="250" height="100" style="float: right;">
    
    <div class="footer"> 
        <img src="http://localhost/INFORMATICA/public/images/direccion_invea.png" alt="Logo Footer" width=300>
        </div>
    </body>
    </html>';

    // Opciones de Dompdf
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('tempDir', '/tmp');
    $options->set('isPhpEnabled', true);

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
    header('Content-Disposition: inline; filename="formato_usuarios.pdf"');
    header('Cache-Control: public, must-revalidate, max-age=0');
    header('Pragma: public');

    echo $dompdf->output();
} else {
    echo "No se encontró el servicio.";
}
