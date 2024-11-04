<?php
header('Content-Type: application/json');
require_once __DIR__ . '/PersonalModel.php'; // Incluir el modelo

$model = new PersonalModel(); // Crear una instancia del modelo

try {
    $personal = $model->obtenerAllPersonal(); // Obtener el personal desde el modelo
    // Retornar los resultados en formato JSON
    echo json_encode($personal);
} catch (Exception $e) {
    echo json_encode(['error' => 'Ocurrió un error: ' . $e->getMessage()]);
}
?>
