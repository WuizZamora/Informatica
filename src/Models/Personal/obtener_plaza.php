<?php
header('Content-Type: application/json');
require_once __DIR__ . '/PersonalModel.php'; // Incluir el modelo

$model = new PersonalModel(); // Crear una instancia del modelo

try {
    // Obtener el valor del parámetro 'todas' de la consulta (GET)
    $todas = isset($_GET['todas']) && $_GET['todas'] === 'true';
    // Llama a la función con el parámetro para filtrar si es necesario
    $plazas = $model->obtenerPlaza($todas); // Pasar el parámetro a la función
    // Retornar los resultados en formato JSON
    echo json_encode($plazas);
} catch (Exception $e) {
    echo json_encode(['error' => 'Ocurrió un error: ' . $e->getMessage()]);
}
?>
