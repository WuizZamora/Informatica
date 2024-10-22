<?php
require_once __DIR__ . '/ActivosModel.php'; // Incluir el modelo
header('Content-Type: application/json'); // Establecer encabezado

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica que todos los campos necesarios estén presentes
    if (isset($_POST['NumeroInventario'], $_POST['CABMSActivo'], $_POST['ProgresivoActivo'], $_POST['DescripcionActivo'], $_POST['ResguardanteActivo'], $_POST['EstatusActivo'])) {

        // Captura los datos
        $numeroInventario = $_POST['NumeroInventario'];
        $cabmsActivo = $_POST['CABMSActivo'];
        $progresivoActivo = $_POST['ProgresivoActivo'];
        $descripcionActivo = $_POST['DescripcionActivo'];
        $resguardanteActivo = $_POST['ResguardanteActivo'];
        $estatusActivo = $_POST['EstatusActivo'];

        $activoModel = new ActivosModel();

        // Guarda en la tabla Servicios
        $activoInsertado = $activoModel->guardarActivo($numeroInventario, $cabmsActivo, $progresivoActivo, $descripcionActivo, $resguardanteActivo, $estatusActivo);

        // Verifica si el servicio se guardó correctamente
        if ($activoInsertado) {
            echo json_encode(['success' => true, 'message' => 'Activo guardado exitosamente.', 'activoInsertado' => $activoInsertado]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar el activo.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Faltan datos en el formulario.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
