<?php
header('Content-Type: application/json');
require_once __DIR__ . '/PersonalModel.php'; // Incluir el modelo

$model = new PersonalModel(); // Crear una instancia del modelo

// Verifica si se ha pasado el parámetro 'filtrar' en la consulta
$filtrarPuesto = isset($_GET['filtrar']) && $_GET['filtrar'] === 'true';

try {
    // Llama a la función con el parámetro para filtrar si es necesario
    $personal = $model->obtenerPersonal($filtrarPuesto);

// Verifica si $personal contiene datos
if (empty($personal)) {
    $personal = []; // Devolver un array vacío si no hay datos
}

echo json_encode($personal);
} catch (Exception $e) {
    echo json_encode(['error' => 'Ocurrió un error: ' . $e->getMessage()]);
}
