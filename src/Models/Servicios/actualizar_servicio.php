<?php
// actualizar_servicio.php

require_once 'ServicioModel.php'; // Asegúrate de incluir el modelo

// Obtener el cuerpo de la petición
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    // Extraer los datos comunes
    $idServicio = $data['idServicio'];
    $solicitante = $data['solicitante'];
    $entrega = $data['entrega'];
    $atiende = $data['atiende'];
    $oficio = $data['oficio'];
    $fechaSolicitud = $data['fechaSolicitud'];
    $tipoServicio = $data['tipoServicio'];

    // Preparar los datos adicionales dependiendo del tipo de servicio
    if ($tipoServicio === "TÉCNICO") {
        $descripcionTecnico = $data['descripcionTecnico'];
        $evaluacion = $data['evaluacion'];
        $fk_IDActivo_Activos = $data['fk_IDActivo_Activos'];
        // Llama al procedimiento almacenado para el tipo técnico
        $result = $model->actualizarServicioTecnico($idServicio, $solicitante, $entrega, $atiende, $oficio, $fechaSolicitud, $descripcionTecnico, $evaluacion, $fk_IDActivo_Activos);
    } elseif ($tipoServicio === "INCIDENCIA") {
        $servicioSolicitado = implode(", ", $data['servicioSolicitado']);
        $descripcionIncidencia = $data['descripcionIncidencia'];
        $observaciones = $data['observaciones'];
        // Llama al procedimiento almacenado para el tipo incidencia
        $result = $model->actualizarServicioIncidencia($idServicio, $solicitante, $entrega, $atiende, $oficio, $fechaSolicitud, $servicioSolicitado, $descripcionIncidencia, $observaciones);
    } elseif ($tipoServicio === "ENTREGA MATERIAL FÍLMICO") {
        $cantidadVideos = $data['cantidadVideos'];
        $periodoInicial = $data['periodoInicial'];
        $periodoFinal = $data['periodoFinal'];
        $periodo = $data['periodo'];
        $equipo = $data['equipo'];
        $descripcionVideo = $data['descripcionVideo'];
        // Llama al procedimiento almacenado para el tipo entrega de material
        $result = $model->actualizarServicioEntregaMaterial($idServicio, $solicitante, $entrega, $atiende, $oficio, $fechaSolicitud, $descripcionVideo, $cantidadVideos, $periodo, $periodoInicial, $periodoFinal, $equipo);
    }

    // Verifica el resultado y devuelve la respuesta
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo actualizar el servicio.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Datos no válidos.']);
}
?>
