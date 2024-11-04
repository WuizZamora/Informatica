<?php
header('Content-Type: application/json');
require_once __DIR__ . '/PersonalModel.php'; // Incluir el modelo

$model = new PersonalModel(); // Crear una instancia del modelo

// Obtener el IDActivo si está presente en la URL
$numeroEmpleado = isset($_GET['NumeroEmpleado']) ? intval($_GET['NumeroEmpleado']) : null;

try {
    if ($numeroEmpleado) {
        $personal = $model->obtenerPersonalDetalles($numeroEmpleado);
        // Aquí puedes verificar qué se devuelve
        if (isset($personal['error'])) {
            echo json_encode(['error' => $personal['error']]);
        } else {
            echo json_encode($personal);
        }
    } else {
        echo json_encode(['error' => 'No se ha proporcionado un ID de personal válido']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Ocurrió un error: ' . $e->getMessage()]);
    error_log($e->getMessage()); // Log de error para verificar en el servidor
}