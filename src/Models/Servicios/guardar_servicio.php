<?php
require_once __DIR__ . '/ServicioModel.php'; // Incluir el modelo
header('Content-Type: application/json'); // Establecer encabezado

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            $resultadoAtencion = true;

            // Asegúrate de que CABMSDictaminacion es un array
            $cabmsDictaminacion = $camposDinamicos['CABMSDictaminacion'] ?? [];
            $progresivoDictaminacion = $camposDinamicos['ProgresivoDictaminacion'] ?? [];
            $estadoConservacion = $camposDinamicos['EstadoConservacion'] ?? [];

            // Verifica que las longitudes de los arrays coincidan
            $cantidadItems = count($cabmsDictaminacion);
            if ($cantidadItems !== count($progresivoDictaminacion) || $cantidadItems !== count($estadoConservacion)) {
                echo json_encode(['success' => false, 'message' => 'Los campos de dictaminación deben tener la misma cantidad de elementos.']);
                exit;
            }

            // Iterar sobre los activos
            for ($i = 0; $i < $cantidadItems; $i++) {
                // Usar los índices para obtener valores
                $camposDinamicos['CABMSDictaminacion'] = $cabmsDictaminacion[$i];
                $camposDinamicos['ProgresivoDictaminacion'] = $progresivoDictaminacion[$i];
                $camposDinamicos['EstadoConservacion'] = $estadoConservacion[$i];

                // Dependiendo del tipo de servicio, guarda en la tabla correspondiente
                if ($idTipoServicio == "TÉCNICO") {
                    $resultadoAtencion = $servicioModel->guardarTecnico($idServicio, $camposDinamicos);
                }
                // Agregar más condiciones si es necesario para otros tipos de servicios
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