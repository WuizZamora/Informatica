<?php
header('Content-Type: application/json');
require_once __DIR__ . '/ActivosModel.php';
$model = new ActivosModel();

// Obtener el valor de CABMS desde la solicitud
$cabms = isset($_GET['cabms']) ? $_GET['cabms'] : null;
try {
    $activos = $model->obtenerProgresivo($cabms); // Pasar CABMS al modelo
    echo json_encode($activos);
} catch (Exception $e) {
    echo json_encode(['error' => 'Ocurrió un error: ' . $e->getMessage()]);
}
