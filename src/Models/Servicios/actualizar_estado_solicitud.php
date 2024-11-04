<?php
require_once __DIR__ . '/ServicioModel.php'; // Incluir el modelo

$model = new ServicioModel(); // Crear una instancia del modelo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idServicio = $_POST['Pk_IDServicio'];
    $estadoSolicitud = $_POST['EstadoSolicitud'];
    $observaciones = $_POST['Observaciones'];

    // Manejar el archivo de soporte documental si se ha subido
    if (isset($_FILES['SoporteDocumental']) && $_FILES['SoporteDocumental']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['SoporteDocumental']['tmp_name'];
        $fileSize = $_FILES['SoporteDocumental']['size'];
        $fileType = $_FILES['SoporteDocumental']['type'];

        // Obtener la extensión del archivo
        $extension = pathinfo($_FILES['SoporteDocumental']['name'], PATHINFO_EXTENSION);

        // Generar un identificador único
        $identificadorUnico = uniqid('', true);

        // Definir el nombre del archivo en el formato deseado
        $nombreArchivo = "soporte_{$idServicio}_{$identificadorUnico}.{$extension}";

        // Define la carpeta de destino
        $uploadFileDir = './uploads/';
        $dest_path = $uploadFileDir . $nombreArchivo;

        // Mueve el archivo a la carpeta de destino
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $servicio = $model->actualizarEstadoSolicitudCompleto($idServicio, $estadoSolicitud, $dest_path, $observaciones);
            echo json_encode(['success' => true, 'message' => 'Solicitud guardada exitosamente.', 'fileName' => $nombreArchivo]);
        } else {
            echo json_encode(['error' => 'Error al mover el archivo subido.']);
        }
    } else {
        $servicio = $model->actualizarEstadoSolicitud($idServicio, $estadoSolicitud, $observaciones);
        echo json_encode(['success' => true, 'message' => 'Solicitud guardada exitosamente sin archivo.']);
    }
} else {
    echo json_encode(['error' => 'Método de solicitud no permitido.']);
}
