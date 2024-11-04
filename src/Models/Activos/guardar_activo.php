<?php
require_once __DIR__ . '/ActivosModel.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['NumeroInventario'], $_POST['CABMSActivo'], $_POST['ProgresivoActivo'], $_POST['DescripcionActivo'], $_POST['ResguardanteActivo'], $_POST['EstatusActivo'])) {

        $numeroInventario = $_POST['NumeroInventario'];
        $cabmsActivo = $_POST['CABMSActivo'];
        $progresivoActivo = $_POST['ProgresivoActivo'];
        $descripcionActivo = $_POST['DescripcionActivo'];
        $resguardanteActivo = $_POST['ResguardanteActivo'];
        $estatusActivo = $_POST['EstatusActivo'];

        $activoModel = new ActivosModel();

        try {
            $activoInsertado = $activoModel->guardarActivo($numeroInventario, $cabmsActivo, $progresivoActivo, $descripcionActivo, $resguardanteActivo, $estatusActivo);

            if ($activoInsertado) {
                echo json_encode(['success' => true, 'message' => 'Activo guardado exitosamente.', 'activoInsertado' => $activoInsertado]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar el activo.']);
            }
        } catch (mysqli_sql_exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo json_encode(['success' => false, 'message' => 'Error: Los datos CABMS y Progresivo deben ser únicos.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Faltan datos en el formulario.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
