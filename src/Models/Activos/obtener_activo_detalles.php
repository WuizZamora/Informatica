<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/ActivosModel.php'; // Incluir el modelo

$model = new ActivosModel(); // Crear una instancia del modelo

// Obtener el IDActivo si está presente en la URL
$idActivo = isset($_GET['IDActivo']) ? intval($_GET['IDActivo']) : null;

header('Content-Type: application/json');
try {
    if ($idActivo) {
        $activo = $model->obtenerActivoDetalles($idActivo);
        // Aquí puedes verificar qué se devuelve
        if (isset($activo['error'])) {
            echo json_encode(['error' => $activo['error']]);
        } else {
            echo json_encode($activo);
        }
    } else {
        echo json_encode(['error' => 'No se ha proporcionado un ID de activo válido.']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Ocurrió un error: ' . $e->getMessage()]);
    error_log($e->getMessage()); // Log de error para verificar en el servidor
}