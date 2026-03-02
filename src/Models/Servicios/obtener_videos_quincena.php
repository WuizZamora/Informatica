<?php
header('Content-Type: application/json');

require_once __DIR__ . '/ServicioModel.php';

$model = new ServicioModel();

$anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');

try {
    $data = $model->obtenerVideosPorQuincena($anio);
    echo json_encode($data);
} catch (Exception $e) {
    echo json_encode([
        'error' => 'Error al obtener reporte quincenal',
        'detalle' => $e->getMessage()
    ]);
}
