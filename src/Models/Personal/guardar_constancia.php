<?php
require_once __DIR__ . '/PersonalModel.php'; // Incluir el modelo
header('Content-Type: application/json'); // Establecer encabezado

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica que todos los campos necesarios estén presentes
    if (isset($_POST['PersonalSolicitante'], $_POST['FechaSeparacion'] ,$_POST['Oficio'] ,$_POST['Constancia'],$_POST['OficioTI'])) {

        // Captura los datos
        $personalSolicitante = $_POST['PersonalSolicitante'];
        $fechaSeparacion = $_POST['FechaSeparacion'];
        $oficio = $_POST['Oficio'];
        $constancia = $_POST['Constancia'];
        $oficioTI = $_POST['OficioTI'];

        $personalModel = new PersonalModel();

        // Guarda en la tabla Servicios
        $personalInsertConstancia = $personalModel->guardarConstanciaPersonal($personalSolicitante,$fechaSeparacion, $oficio, $constancia, $oficioTI);

        // Verifica si el servicio se guardó correctamente
        if ($personalInsertConstancia['success']) {
            echo json_encode(['success' => true, 'message' => 'Constancia guardada exitosamente.']);
        } else {
            $errorMessage = isset($personalInsertConstancia['error']) ? $personalInsertConstancia['error'] : 'Error al guardar la constancia.';
            echo json_encode(['success' => false, 'message' => $errorMessage]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Faltan datos en el formulario.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>