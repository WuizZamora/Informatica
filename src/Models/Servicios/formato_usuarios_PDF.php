<?php
require_once __DIR__ . '/../../../vendor/autoload.php'; // Asegúrate de requerir el autoload de Composer
use Dompdf\Dompdf;
use Dompdf\Options;

date_default_timezone_set('America/Mexico_City');

// Crear un objeto DateTime con la fecha actual
$fecha = new DateTime();

// Crear un formateador de fecha en español
$formatter = new IntlDateFormatter(
    'es_ES', // Configuración regional en español
    IntlDateFormatter::LONG, // Formato de fecha largo (ej: "4 de noviembre de 2024")
    IntlDateFormatter::NONE // Sin formato de hora
);

// Formatear la fecha
$fecha_hoy = $formatter->format($fecha);

if (isset($_GET['solicitante']) && isset($_GET['user']) && isset($_GET['pass'])) {
    $solicitante = htmlspecialchars($_GET['solicitante']);
    $user = htmlspecialchars($_GET['user']);
    $pass = htmlspecialchars($_GET['pass']);
    // Asumimos que ya tienes una instancia de tu modelo
    require '../Personal/PersonalModel.php';
    $personalModel = new PersonalModel();
    $data = $personalModel->obtenerPersonalAndPlaza($solicitante);
    // Crear una nueva instancia de Dompdf
    $dompdf = new Dompdf();

    // Contenido HTML del PDF
    $html = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Formato usuarios</title>
        <style>
            body {font-family: Arial, sans-serif; margin: 0; padding: 0;}
            .texto {font-size: 0.9rem; padding:1rem; text-align: justify;}
            .footer {position: fixed; bottom: 0; left: 0; width: 100%; height: 3rem; display: flex; align-items: center; padding: 0 1rem; box-sizing: border-box;}
            .footer img { right: 1rem; bottom: 1rem; height: 2rem; object-fit: contain;}
        </style>
    </head>
    <body>
        <img src="http://localhost/INFORMATICA/public/images/cabecera_completa.png" alt="LOGO CDMX" width=700>
        <p style="padding-left:23rem;">Ciudad de México, a ' . $fecha_hoy . '</p>
        <p class="texto">
        <strong>' . $data['NombreCompleto'] . '<br>Puesto: ' . $data['Puesto'] . '</strong> '. '<br><strong>Contraseña:</strong> '. $pass . '<br><br>
        <strong>PRESENTE</strong><br>
        Con base en las Políticas del Instituto de Verificación Administrativa de la Ciudad de México sobre Usuarios, Contraseñas, Internet, Intranet, Correo Electrónico, Telefonía e Internet Móvil, Telefonía Local y Dispositivos Móviles, descritas de acuerdo al  aviso en la Gaceta Oficial de la Ciudad de México número 239 de fecha 12 de enero de 2018, y que pueden ser consultadas el siguiente enlace electrónico <a href"http://lto7.ddns.net/invea/informes/usuarios.php">http://lto7.ddns.net/invea/informes/usuarios.php</a> así como una implementación de seguridad para el personal de este Instituto, me permito informarle que a partir del día de hoy, le es asignado su nombre de usuario y contraseña, cuya acción permitirá a usted tener la certeza de que la contraseña es UNICA y que personal ajeno a usted no la posee. <br>

        Asimismo, acepta y se compromete a cumplir con las políticas para el uso adecuado de los servicios informáticos proporcionados por el Instituto de Verificación Administrativa de la Ciudad de México en los siguientes términos:<br>

        1. La contraseña es intransferible. <br>
        2. Toda la actividad realizada con el usuario y contraseña será susceptible de ser auditado y/o verificado.<br>
        3. Toda la actividad generada por este usuario en los equipos de cómputo y/o servicios de red, así como la transmisión, resguardo y modificación de datos, a partir de mi ingreso y hasta la solicitud de baja, será <strong>UNICA Y EXCLUSIVA</strong>, asumiendo toda la responsabilidad del uso de la información en los sistemas del Instituto de Verificación Administrativa de la Ciudad de México.<br>
        4. Por este medio certifico, que he leído y comprendo completamente el contenido del Presente, la cual valido con mi firma, misma que certifica el conocimiento, aceptación y apego a las Políticas de Usuarios, Contraseñas, Internet, Intranet, Correo Electrónico, Telefonía e Internet Móvil, Telefonía Local y Dispositivos Móviles.<br>

        En virtud de lo anterior su firma abajo manifiesta el conocimiento, aceptación y apego a las políticas, reglas y procedimientos de la organización en relación al usuario y contraseña que me permite el acceso a los diferentes servicios proporcionados por el Instituto de verificación Administrativa. <br>

        La Dirección de Administración y Finanzas hace entrega de la nueva contraseña para el ingreso a los servicios informáticos propiedad del Instituto de Verificación Administrativa de la Ciudad de México.
        <p style="text-align: center;">Acuse de recibo<br><br>_______________________________<br><strong>'.$data['NombreCompleto'].'</strong></p>
        </p>
    ';

    $html .= '</div>
    <script type="text/php">
        if (isset($pdf)) {
            $x = 490;
            $y = 775;
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $font = null;
            $size = 9;
            $color = array(0,0,0);
            $pdf->page_text($x, $y, $text, $font, $size, $color);
        }
    </script>
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
