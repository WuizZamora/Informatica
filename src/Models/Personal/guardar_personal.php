<?php
require_once __DIR__ . '/PersonalModel.php'; // Incluir el modelo
header('Content-Type: application/json'); // Establecer encabezado

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica que todos los campos necesarios estén presentes
    if (isset($_POST['NumeroEmpleado'], $_POST['PrimerApellidoEmpleado'] ,$_POST['SegundoApellidoEmpleado'] ,$_POST['NombreEmpleado'] ,$_POST['RFCEmpleado'], $_POST['PlazaEmpleado'], $_POST['FechaInicial'], $_POST['EstatusEmpleado'])) {

        // Captura los datos
        $numeroEmpleado = $_POST['NumeroEmpleado'];
        $primerApellidoEmpleado = $_POST['PrimerApellidoEmpleado'];
        $segundoApellidoEmpleado = $_POST['SegundoApellidoEmpleado'];
        $nombreEmpleado = $_POST['NombreEmpleado'];
        $rfcEmpleado = $_POST['RFCEmpleado'];
        $plazaEmpleado = $_POST['PlazaEmpleado'];
        $fechaInicial = $_POST['FechaInicial'];
        $estatusEmpleado = $_POST['EstatusEmpleado'];

        $personalModel = new PersonalModel();

        // Guarda en la tabla Servicios
        $personalInsert = $personalModel->guardarPersonal($numeroEmpleado, $primerApellidoEmpleado,$segundoApellidoEmpleado, $nombreEmpleado, $rfcEmpleado, $plazaEmpleado, $fechaInicial, $estatusEmpleado);

        // Verifica si el servicio se guardó correctamente
        if ($personalInsert['success']) {
            echo json_encode(['success' => true, 'message' => 'Personal guardado exitosamente.']);
        } else {
            $errorMessage = isset($personalInsert['error']) ? $personalInsert['error'] : 'Error al guardar al personal.';
            echo json_encode(['success' => false, 'message' => $errorMessage]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Faltan datos en el formulario.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
