<?php
header('Content-Type: application/json');
require_once __DIR__ . '/ServicioModel.php'; // Incluir el modelo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $fecha_inicio = $data['fecha_inicio'];
    $fecha_fin = $data['fecha_fin'];

    $servicioModel = new ServicioModel();

    // Llamar al primer procedimiento 'Servicios_SELECT_Date'
    $servicios = $servicioModel->ServicioFechasPasadas($fecha_inicio, $fecha_fin);

    // Combinar ambos resultados en un solo array
    $resultado = [
        'servicios' => $servicios
    ];

    // Devolver los resultados en formato JSON
    echo json_encode($resultado);
} else {
    echo json_encode(['error' => 'Método no permitido']);
}
