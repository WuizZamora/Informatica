<?php
header('Content-Type: application/json');
require_once __DIR__ . '/PersonalModel.php'; // Incluir el modelo

$model = new PersonalModel(); // Crear una instancia del modelo

try {
    // Llama a la función con el parámetro para filtrar si es necesario
    $personalConstancia = $model->obtenerConstancia();

// Verifica si $personal contiene datos
if (empty($personalConstancia)) {
    $personal = []; // Devolver un array vacío si no hay datos
}

echo json_encode($personalConstancia);
} catch (Exception $e) {
    echo json_encode(['error' => 'Ocurrió un error: ' . $e->getMessage()]);
}
