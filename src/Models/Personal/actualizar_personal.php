<?php
// Establecer el tipo de contenido a JSON
header('Content-Type: application/json');
require_once __DIR__ . '/PersonalModel.php'; // Incluir el modelo

$model = new PersonalModel(); // Instancia del modelo

// Recibir los datos en formato JSON
$data = json_decode(file_get_contents("php://input"));

// Verificar que los datos se recibieron correctamente
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'error' => 'Datos JSON inválidos']);
    exit;
}

// Verificar que se recibieron todos los datos requeridos
if (!isset($data->numeroEmpleado, $data->nombre, $data->rfc, $data->plaza, $data->fechaInicial, $data->estatusUpdate)) {
    echo json_encode(['success' => false, 'error' => 'Faltan datos requeridos']);
    exit;
}

try {
    // Llamar al método actualizarActivo del modelo
    $result = $model->actualizarPersonal(
        $data->numeroEmpleado, 
        $data->nombre, 
        $data->rfc, 
        $data->plaza, 
        $data->fechaInicial, 
        $data->estatusUpdate
    );
    
    // Responder con el resultado
    echo json_encode($result);
} catch (Exception $e) {
    // Manejo de excepciones
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}