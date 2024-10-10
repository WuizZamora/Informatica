<?php
require_once __DIR__ . '/ActivosModel.php'; // Ajusta la ruta si es necesario
$model = new ActivosModel();

// Obtener el valor de CABMS desde la solicitud
$cabms = isset($_GET['cabms']) ? $_GET['cabms'] : null;

// Intenta obtener los servicios
try {
    $activos = $model->obtenerProgresivo($cabms); // Pasar CABMS al modelo

    // Configurar la cabecera para indicar que es JSON
    header('Content-Type: application/json');

    // Retornar los resultados en formato JSON
    echo json_encode($activos);
} catch (Exception $e) {
    // Manejar errores y devolver un mensaje en JSON
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Ocurrió un error: ' . $e->getMessage()]);
}
?>
