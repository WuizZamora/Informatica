<?php
require_once __DIR__ . '/ServicioModel.php'; // Incluir el modelo

$model = new ServicioModel(); // Crear una instancia del modelo

$idServicio = isset($_GET['IDServicio']) ? intval($_GET['IDServicio']) : null;

header('Content-Type: application/json');

try {
    if ($idServicio) {
        $servicio = $model->consultarEstadoSolicitud($idServicio);
        echo json_encode($servicio);
    } else {
        echo json_encode(['error' => 'No se ha proporcionado un ID de servicio válido.']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Ocurrió un error: ' . $e->getMessage()]);
}
