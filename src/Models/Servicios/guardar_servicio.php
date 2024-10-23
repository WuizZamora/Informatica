<?php
require_once __DIR__ . '/ServicioModel.php'; // Incluir el modelo
header('Content-Type: application/json'); // Establecer encabezado

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica que todos los campos necesarios estén presentes
    if (isset($_POST['PersonalSolicitante'], $_POST['PersonalAtiende'], $_POST['IDTipoServicio'], $_POST['FechaAtencion'], $_POST['Oficio'], $_POST['FechaSolicitud'])) {
        
        // Captura los datos
        $personalSolicitante = $_POST['PersonalSolicitante'];
        $personalAtiende = $_POST['PersonalAtiende'];
        $idTipoServicio = $_POST['IDTipoServicio'];
        $fechaAtencion = $_POST['FechaAtencion'];
        $oficio = $_POST['Oficio'];
        $fechaSolicitud = $_POST['FechaSolicitud'];

        $servicioModel = new ServicioModel();

        // Guarda en la tabla Servicios
        $idServicio = $servicioModel->guardarServicio($personalSolicitante, $personalAtiende, $idTipoServicio, $fechaAtencion, $oficio, $fechaSolicitud);

        if ($idServicio) {
            $camposDinamicos = $_POST; // Captura los campos adicionales
            $resultadoAtencion = false;

            // Dependiendo del tipo de servicio, guarda en la tabla correspondiente
            if ($idTipoServicio == "INCIDENCIA") {
                $resultadoAtencion = $servicioModel->guardarIncidencia($idServicio, $camposDinamicos);
            } elseif ($idTipoServicio == "ENTREGA MATERIAL FÍLMICO") {
                $resultadoAtencion = $servicioModel->guardarVideos($idServicio, $camposDinamicos);
            } elseif ($idTipoServicio == "TÉCNICO") {
                $resultadoAtencion = $servicioModel->guardarTecnico($idServicio, $camposDinamicos);
            }

            if ($resultadoAtencion) {
                echo json_encode(['success' => true, 'message' => 'Servicio guardado exitosamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar la atención del servicio.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar el servicio.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Faltan datos en el formulario.']);
    }
}
?>