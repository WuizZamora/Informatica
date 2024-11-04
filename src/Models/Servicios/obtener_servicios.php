<?php
header('Content-Type: application/json');
require_once __DIR__ . '/ServicioModel.php'; // Incluir el modelo

$model = new ServicioModel(); // Crear una instancia del modelo

// Intenta obtener los servicios
try {
    $servicios = $model->obtenerServicios(); // Obtener los servicios desde el modelo
    // Retornar los resultados en formato JSON
    echo json_encode($servicios);
} catch (Exception $e) {
    echo json_encode(['error' => 'Ocurrió un error: ' . $e->getMessage()]);
}
