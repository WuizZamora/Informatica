<?php
require_once __DIR__ . '/../../../config/conexion.php'; // Asegúrate de ajustar la ruta correcta

class ServicioModel
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar(); // Conexión a la BD
    }

    // Método para obtener todos los servicios
    public function obtenerServicios()
    {
        $query = "SELECT * FROM Servicios_informacion_general
            ORDER BY Pk_IDServicio DESC
        "; // Ajusta la consulta según tu tabla
        $result = $this->db->query($query);
        $VistaDeServicios = $result->fetch_all(MYSQLI_ASSOC);
        return $VistaDeServicios;
    }

    public function consultarEstadoSolicitud($idServicio)
    {
        $query = "SELECT Pk_IDServicio, EstadoSolicitud, SoporteDocumental, Observaciones FROM Servicios WHERE Pk_IDServicio = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $idServicio);
        $stmt->execute();

        $result = $stmt->get_result();
        $servicio = $result->fetch_assoc();

        return $servicio;
    }

    public function obtenerServicioDetalles($idServicio)
    {
        // Llamar al procedimiento almacenado
        $query = "CALL Servicios_SELECT_ToUPDATE(?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $idServicio);
        $stmt->execute();

        // Obtener los resultados
        $result = $stmt->get_result();
        $serviciosDetalles = [];

        // Recorrer todos los resultados
        while ($row = $result->fetch_assoc()) {
            $serviciosDetalles[] = $row;
        }

        // Retornar todos los datos del servicio
        return $serviciosDetalles;
    }

    public function consultarServicio($idServicio)
    {
        // Llamar al procedimiento almacenado
        $query = "CALL Servicios_SELECT_Reporte(?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $idServicio);
        $stmt->execute();

        // Obtener los resultados
        $result = $stmt->get_result();

        // Inicializar un array para almacenar todos los servicios
        $servicios = [];

        // Iterar sobre los resultados y almacenar cada fila en el array
        while ($row = $result->fetch_assoc()) {
            $servicios[] = $row;
        }

        // Retornar todos los datos del servicio
        return $servicios;
    }

    public function guardarServicio($PersonalSolicitante, $PersonalAtiende, $IDTipoServicio, $FechaAtencion, $oficio, $fechaSolicitud)
    {
        // Inserta los datos en la tabla Servicios
        $query = "INSERT INTO Servicios (Fk_Solicitante_Personal, Fk_Atiende_Personal, TipoServicio, FechaAtencion, Oficio, FechaSolicitud)
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iissss", $PersonalSolicitante, $PersonalAtiende, $IDTipoServicio, $FechaAtencion, $oficio, $fechaSolicitud);

        if ($stmt->execute()) {
            // Retorna el ID del servicio recién insertado
            return $stmt->insert_id;
        } else {
            return false;
        }

        $stmt->close();
    }

    public function guardarIncidencia($idServicio, $campos)
    {
        // Asegúrate de que ServicioSolicitado sea un array y eliminar duplicados
        if (is_array($campos['ServicioSolicitado'])) {
            // Eliminar duplicados
            $serviciosSinDuplicados = array_unique($campos['ServicioSolicitado']);
            // Concatenar todos los servicios solicitados en una sola cadena
            $serviciosConcatenados = implode(", ", $serviciosSinDuplicados);
        } else {
            // Manejar el caso donde no se recibe un array
            $serviciosConcatenados = ''; // O manejarlo de otra manera
        }

        // Log para verificar los valores antes de la inserción
        error_log("Antes de la inserción: ID Servicio: $idServicio, Servicios Solicitados: $serviciosConcatenados, Detalles: {$campos['DetallesServicioIncidencia']}");

        // Guardar en tabla Servicios_Incidencias
        $query = "INSERT INTO Servicios_Incidencias (Fk_IDServicio_Servicios, ServicioSolicitado, Descripcion) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);

        $result = true; // Variable para rastrear errores

        // Ejecutar la consulta con la cadena concatenada
        $stmt->bind_param(
            "iss",
            $idServicio,
            $serviciosConcatenados, // Cadena concatenada de servicios solicitados
            $campos['DetallesServicioIncidencia']
        );

        // Ejecutar la consulta y manejar errores
        if (!$stmt->execute()) {
            error_log("Error en la consulta: " . $stmt->error);
            $result = false; // Si hay un error, marca como false
        }

        return $result; // Retornar el estado de la inserción
    }

    public function guardarVideos($idServicio, $campos)
    {
        // Guardar en tabla Atencion_Videos
        $query = "INSERT INTO Servicios_Videos (Fk_IDServicio_Servicios, Descripcion, CantidadVideos, Equipo, PeriodoInicial, PeriodoFinal, Periodo) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param(
            "issssss",
            $idServicio,
            $campos['DescripcionVideos'],
            $campos['CantidadVideos'],
            $campos['Equipo'],
            $campos['PeriodoInicial'],
            $campos['PeriodoFinal'],
            $campos['Periodo']
        );

        return $stmt->execute();
    }

    public function guardarTecnico($idServicio, $campos)
    {
        // Inicializa $pkIDActivo en null
        $pkIDActivo = null;

        // Primero, consulta el Pk_IDActivo en la tabla Activos usando CABMS y Progresivo
        $queryActivo = "SELECT Pk_IDActivo FROM Activos WHERE CABMS = ? AND Progresivo = ? AND Estatus = 'ACTIVO'";
        $stmtActivo = $this->db->prepare($queryActivo);
        $stmtActivo->bind_param("ii", $campos['CABMSDictaminacion'], $campos['ProgresivoDictaminacion']);
        $stmtActivo->execute();
        $stmtActivo->bind_result($pkIDActivo);
        $stmtActivo->fetch();
        $stmtActivo->close();

        // Si encontramos un activo con el CABMS y Progresivo dados
        if ($pkIDActivo) {
            $queryServicioTecnico = "INSERT INTO Servicios_Tecnicos (Fk_IDServicio_Servicios, Fk_IDActivo_Activos, Descripcion, Evaluacion) VALUES (?, ?, ?, ?)";
            $stmtServicioTecnico = $this->db->prepare($queryServicioTecnico);
            $stmtServicioTecnico->bind_param(
                "iiss",
                $idServicio,
                $pkIDActivo,
                $campos['DescripcionEstado'],
                $campos['EstadoConservacion']
            );

            $resultado = $stmtServicioTecnico->execute();
            $stmtServicioTecnico->close();

            // Si la inserción fue exitosa y el EstadoConservacion es 'No funcional'
            if ($resultado && $campos['EstadoConservacion'] === 'No funcional') {
                $queryBaja = "CALL Activo_UPDATE_Estatus_Baja(?)";
                $stmtBaja = $this->db->prepare($queryBaja);
                $stmtBaja->bind_param("i", $pkIDActivo);
                $stmtBaja->execute();
                $stmtBaja->close();
            }

            return $resultado;
        } else {
            return false;
        }
    }

    public function actualizarServicioTecnico($idServicio, $datos)
    {
        try {
            // Iniciar la transacción
            $this->db->begin_transaction();

            // Validar datos
            if (empty($datos->solicitante) || empty($datos->atiende)) {
                throw new Exception('Los campos solicitante y atiende son obligatorios.');
            }

            // Preparar y ejecutar el procedimiento principal
            $stmt1 = $this->db->prepare("CALL Servicio_Tecnico_UPDATE(?, ?, ?, ?, ?, ?)");
            if (!$stmt1) {
                throw new Exception('Error en la preparación de la consulta: ' . $this->db->error);
            }

            $stmt1->bind_param(
                "iiisss",
                $idServicio,
                $datos->solicitante,
                $datos->atiende,
                $datos->fechaSolicitud,
                $datos->oficio,
                $datos->DescripcionTecnico
            );

            if (!$stmt1->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt1->error);
            }

            // Procesar activos si existen
            if (isset($datos->activos) && is_array($datos->activos)) {
                foreach ($datos->activos as $activo) {
                    $stmt2 = $this->db->prepare("CALL Servicios_Tecnicos_EachRegistro_UPDATE(?, ?, ?, ?, ?)");
                    if (!$stmt2) {
                        throw new Exception('Error en la preparación de la consulta de activos: ' . $this->db->error);
                    }
                    $stmt2->bind_param(
                        "iisss",
                        $idServicio,
                        $activo->idPK,
                        $activo->cabmsTecnico,
                        $activo->progresivoTecnico,
                        $activo->EvaluacionTecnico
                    );

                    if (!$stmt2->execute()) {
                        throw new Exception('Error al ejecutar la consulta de activos: ' . $stmt2->error);
                    }
                    $stmt2->close();
                }
            }

            // Confirmar la transacción
            $this->db->commit();

            return ['success' => true, 'message' => 'Servicio técnico actualizado exitosamente'];
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            return ['success' => false, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function actualizarServicioIncidencia($idServicio, $datos)
    {
        try {
            // Preparar la llamada al procedimiento almacenado
            $stmt = $this->db->prepare("CALL Servicio_Incidencia_UPDATE(?, ?, ?, ?, ?, ?, ?)");

            // Vincular los parámetros
            $stmt->bind_param("iiissss", $idServicio, $datos->solicitante, $datos->atiende, $datos->fechaSolicitud, $datos->oficio, $datos->ServicioSolicitado, $datos->DescripcionIncidencia);

            // Ejecutar la consulta
            $stmt->execute();

            return ['success' => true, 'message' => 'Incidencia actualizada exitosamente'];
        } catch (mysqli_sql_exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function actualizarServicioVideo($idServicio, $datos)
    {
        try {
            // Preparar la llamada al procedimiento almacenado
            $stmt = $this->db->prepare("CALL Servicio_Video_UPDATE(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            // Vincular los parámetros
            $stmt->bind_param("iiisssissss", $idServicio, $datos->solicitante,  $datos->atiende, $datos->fechaSolicitud, $datos->oficio, $datos->DescripcionVideos, $datos->CantidadVideos, $datos->PVideos, $datos->PIVideos, $datos->PFVideos, $datos->Equipo);

            // Ejecutar la consulta
            $stmt->execute();

            return ['success' => true, 'message' => 'Entrega de material fílmico actualizada exitosamente'];
        } catch (mysqli_sql_exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function actualizarEstadoSolicitudCompleto($idServicio, $estadoSolicitud, $desth_path, $observaciones)
    {
        try {
            // Preparar la llamada al procedimiento almacenado
            $stmt = $this->db->prepare("CALL Servicio_UPDATE_FileAndEstado(?, ?, ?, ?)");

            // Vincular los parámetros
            $stmt->bind_param("isss", $idServicio, $estadoSolicitud, $desth_path, $observaciones);

            // Ejecutar la consulta
            $stmt->execute();

            return ['success' => true, 'message' => 'Entrega de material fílmico actualizada exitosamente'];
        } catch (mysqli_sql_exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function actualizarEstadoSolicitud($idServicio, $estadoSolicitud, $observaciones)
    {
        try {
            // Preparar la llamada al procedimiento almacenado
            $stmt = $this->db->prepare("CALL Servicio_UPDATE_EstadoSolicitud(?, ?, ?)");

            // Vincular los parámetros (i = integer, s = string)
            $stmt->bind_param("iss", $idServicio, $estadoSolicitud, $observaciones); // Solo 2 parámetros

            // Ejecutar la consulta
            $stmt->execute();

            return ['success' => true, 'message' => 'Entrega de material fílmico actualizada exitosamente'];
        } catch (mysqli_sql_exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function obtenerRutaArchivo($idServicio)
    {
        $query = "SELECT SoporteDocumental FROM Servicios WHERE Pk_IDServicio=?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $idServicio);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        // Retorna solo la ruta del archivo o null si no existe
        return $data['SoporteDocumental'] ?? null; // Maneja la posibilidad de que no exista
    }

    public function eliminarSoporte($idServicio)
    {
        // Consulta SQL para eliminar el servicio basado en idServicio
        $query = "UPDATE Servicios SET SoporteDocumental = NULL WHERE Pk_IDServicio=?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $idServicio);

        // Ejecutar la consulta y manejar errores
        if (!$stmt->execute()) {
            echo json_encode(['error' => 'Error en la consulta: ' . $stmt->error]);
            exit; // Detiene la ejecución si hay un error
        }

        // Devuelve verdadero si la consulta se ejecutó sin errores
        return true; // Cambia esto según lo que necesites
    }

    public function ServicioFechas($dateInicial, $dateFinal)
    {
        // Llamar al procedimiento almacenado
        $query = "CALL Servicios_SELECT_Date(?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $dateInicial, $dateFinal);
        $stmt->execute();

        // Obtener los resultados
        $result = $stmt->get_result();
        $servicios = [];

        // Almacenar todos los resultados en un array
        while ($row = $result->fetch_assoc()) {
            $servicios[] = $row;
        }

        // Retornar los datos del servicio
        return $servicios;
    }

    public function ObtenerTecnicoPorPeriodo($fechaInicio, $fechaFin)
    {
        $query = "CALL Servicios_Tecnicos_SELECT_Date(?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $fechaInicio, $fechaFin);
        $stmt->execute();

        $result = $stmt->get_result();
        $reporte = [];

        while ($row = $result->fetch_assoc()) {
            $reporte[] = $row;
        }

        return $reporte;
    }

    public function ObtenerIncidenciasPorPeriodo($fechaInicio, $fechaFin)
    {
        $query = "CALL Servicios_Incidencias_SELECT_Date(?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $fechaInicio, $fechaFin);
        $stmt->execute();

        $result = $stmt->get_result();
        $incidencias = [];

        while ($row = $result->fetch_assoc()) {
            $incidencias[] = $row;
        }

        return $incidencias;
    }

    public function ObtenerVideosPorPeriodo($fechaInicio, $fechaFin)
    {
        $query = "CALL Servicios_Videos_SELECT_Date(?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $fechaInicio, $fechaFin);
        $stmt->execute();

        $result = $stmt->get_result();
        $videos = [];

        while ($row = $result->fetch_assoc()) {
            $videos[] = $row;
        }

        return $videos;
    }

    public function consultarDetallesEquipo($equipo, $fechaInicio, $fechaFin)
    {
        $query = "CALL Servicios_Videos_SELECT_DetalleEquipo(?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sss", $fechaInicio, $fechaFin, $equipo);
        $stmt->execute();

        $result = $stmt->get_result();
        $equipoDetalles = [];

        while ($row = $result->fetch_assoc()) {
            $equipoDetalles[] = $row;
        }

        return $equipoDetalles;
    }
}
