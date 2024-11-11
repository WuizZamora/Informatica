<?php
header('Content-Type: application/json');

require_once __DIR__ . '/ServicioModel.php'; // Incluir el modelo

$model = new ServicioModel(); // Crear una instancia del modelo

$data = json_decode(file_get_contents('php://input'), true);
$equipo = $data['equipo'];
$fechaInicio = $data['fecha_inicio'];
$fechaFin = $data['fecha_fin'];

try {
    if ($equipo && $fechaInicio && $fechaFin) {
        $detallesEquipo = $model->consultarDetallesEquipoPasado($equipo, $fechaInicio, $fechaFin);
        if (empty($detallesEquipo)) {
            echo json_encode(['error' => 'No se encontraron detalles para el equipo']);
        } else {
            echo json_encode($detallesEquipo); // Retorna todos los registros
        }
    } else {
        echo json_encode(['error' => 'No se ha proporcionado los datos completos']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Ocurrió un error: ' . $e->getMessage()]);
}
