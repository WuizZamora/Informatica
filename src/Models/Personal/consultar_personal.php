<?php
require_once __DIR__ . '/PersonalModel.php'; // Incluir el modelo

$model = new PersonalModel(); // Crear una instancia del modelo

try {
    // Llama a la función con el parámetro para filtrar si es necesario
    $personal = $model->obtenerAllPersonal(); // Obtener el personal desde el modelo

    // Configurar la cabecera para indicar que es JSON
    header('Content-Type: application/json');

    // Retornar los resultados en formato JSON
    echo json_encode($personal);
} catch (Exception $e) {
    // Manejar errores y devolver un mensaje en JSON
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Ocurrió un error: ' . $e->getMessage()]);
}
?>
