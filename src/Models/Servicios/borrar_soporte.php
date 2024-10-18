<?php
require_once __DIR__ . '/ServicioModel.php'; // Incluir el modelo

$model = new ServicioModel(); // Crear una instancia del modelo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar los parámetros del cuerpo de la solicitud
    $data = json_decode(file_get_contents('php://input'), true);
    $idServicio = $data['idServicio'] ?? null; // Maneja la posibilidad de que no exista

    // Obtener la ruta del archivo desde la base de datos
    $rutaArchivo = $model->obtenerRutaArchivo($idServicio);

    // Inicializa la respuesta
    $response = [];

    // Verifica si la ruta del archivo existe
    if ($rutaArchivo && file_exists($rutaArchivo)) {
        // Intenta eliminar el archivo
        if (unlink($rutaArchivo)) {
            // Elimina el registro en la base de datos
            $resultado = $model->eliminarSoporte($idServicio);

            if ($resultado) {
                $response['success'] = true;
                $response['message'] = 'Servicio y archivo eliminados exitosamente.';
            } else {
                $response['error'] = 'Error al eliminar el servicio de la base de datos.';
            }
        } else {
            $response['error'] = 'Error al eliminar el archivo.';
        }
    } else {
        $response['error'] = 'El archivo no existe o no se pudo encontrar la ruta.';
    }

    // Agregar la ruta del archivo a la respuesta, si existe
    if ($rutaArchivo) {
        $response['rutaArchivo'] = $rutaArchivo;
    }

    // Devuelve la respuesta como JSON
    echo json_encode($response);
} else {
    echo json_encode(['error' => 'Método de solicitud no permitido.']);
}
