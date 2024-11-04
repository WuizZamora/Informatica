<?php
header('Content-Type: application/json');
require_once __DIR__ . '/ActivosModel.php';
$model = new ActivosModel();
try {
    $activos = $model->obtenerActivos(); // Obtener los servicios desde el modelo
    // Retornar los resultados en formato JSON
    echo json_encode($activos);
} catch (Exception $e) {
    echo json_encode(['error' => 'Ocurrió un error: ' . $e->getMessage()]);
}
