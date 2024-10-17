<?php
// Establecer el tipo de contenido a JSON
header('Content-Type: application/json');
require_once __DIR__ . '/ServicioModel.php'; // Incluir el modelo

$model = new ServicioModel(); // Instancia del modelo

// Recibir los datos en formato JSON
$data = json_decode(file_get_contents("php://input"));

// Verificar que los datos se recibieron correctamente
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'error' => 'Datos JSON inválidos']);
    exit;
}

// Verificar que se recibió el ID del servicio
if (!isset($data->idServicio)) {
    echo json_encode(['success' => false, 'error' => 'ID del servicio es requerido']);
    exit;
}

// Llamar a la función adecuada según el tipo de servicio
switch ($data->tipoServicio) {
    case "TÉCNICO":
        $result = $model->actualizarServicioTecnico($data->idServicio, $data);
        break;
    case "INCIDENCIA":
        $result = $model->actualizarServicioIncidencia($data->idServicio, $data);
        break;
    case "ENTREGA MATERIAL FÍLMICO":
        $result = $model->actualizarServicioVideo($data->idServicio, $data);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Tipo de servicio no válido']);
        exit;
}

// Responder con el resultado
echo json_encode($result);
