<?php
require_once __DIR__ . '/PersonalModel.php'; // Incluir el modelo

$model = new PersonalModel(); // Crear una instancia del modelo

try {
    // Obtener el valor del parámetro 'todas' de la consulta (GET)
    $todas = isset($_GET['todas']) && $_GET['todas'] === 'true';

    // Llama a la función con el parámetro para filtrar si es necesario
    $plazas = $model->obtenerPlaza($todas); // Pasar el parámetro a la función

    // Configurar la cabecera para indicar que es JSON
    header('Content-Type: application/json');

    // Retornar los resultados en formato JSON
    echo json_encode($plazas);
} catch (Exception $e) {
    // Manejar errores y devolver un mensaje en JSON
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Ocurrió un error: ' . $e->getMessage()]);
}
?>
