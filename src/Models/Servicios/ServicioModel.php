<?php
require_once __DIR__ . '/../../../config/conexion.php'; // Asegúrate de ajustar la ruta correcta
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

    public function obtenerServicioDetalles($idServicio)
    {
        // Llamar al procedimiento almacenado
        $query = "CALL Servicios_SELECT_ToUPDATE(?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $idServicio);
        $stmt->execute();

        // Obtener los resultados
        $result = $stmt->get_result();
        $VistaServicio = $result->fetch_assoc();

        // Retornar los datos del servicio
        return $VistaServicio;
    }

    public function consultarServicio($idServicio)
    {
        // Llamar al procedimiento almacenado
        $query = "CALL 	Servicios_SELECT_Reporte(?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $idServicio);
        $stmt->execute();

        // Obtener los resultados
        $result = $stmt->get_result();
        $VistaServicio = $result->fetch_assoc();

        // Retornar los datos del servicio
        return $VistaServicio;
    }

    public function guardarServicio($PersonalSolicitante, $personalEntrega, $PersonalAtiende, $IDTipoServicio, $FechaAtencion, $oficio, $fechaSolicitud)
    {
        // Inserta los datos en la tabla Servicios
        $query = "INSERT INTO Servicios (Fk_Solicitante_Personal, Fk_Entrega_Personal, Fk_Atiende_Personal, TipoServicio, FechaAtencion, Oficio, FechaSolicitud)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iiissss", $PersonalSolicitante, $personalEntrega, $PersonalAtiende, $IDTipoServicio, $FechaAtencion, $oficio, $fechaSolicitud);

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
        error_log("Antes de la inserción: ID Servicio: $idServicio, Servicios Solicitados: $serviciosConcatenados, Detalles: {$campos['DetallesServicioIncidencia']}, Observaciones: {$campos['ObservacionesServicioIncidencia']}");
    
        // Guardar en tabla Servicios_Incidencias
        $query = "INSERT INTO Servicios_Incidencias (Fk_IDServicio_Servicios, ServicioSolicitado, Descripcion, Observaciones) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        
        $result = true; // Variable para rastrear errores
    
        // Ejecutar la consulta con la cadena concatenada
        $stmt->bind_param(
            "isss",
            $idServicio,
            $serviciosConcatenados, // Cadena concatenada de servicios solicitados
            $campos['DetallesServicioIncidencia'],
            $campos['ObservacionesServicioIncidencia']
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

    public function actualizarServicioTecnico($idServicio, $solicitante, $entrega, $atiende, $oficio, $fechaSolicitud, $descripcionTecnico, $evaluacion, $fk_IDActivo_Activos) {
        $sql = "CALL NombreDelProcedimientoAlmacenado(?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idServicio, $solicitante, $entrega, $atiende, $oficio, $fechaSolicitud, $descripcionTecnico, $evaluacion, $fk_IDActivo_Activos]);
    }

    public function actualizarServicioIncidencia($idServicio, $solicitante, $entrega, $atiende, $oficio, $fechaSolicitud, $servicioSolicitado, $descripcionIncidencia, $observaciones) {
        $sql = "CALL NombreDelProcedimientoAlmacenado(?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idServicio, $solicitante, $entrega, $atiende, $oficio, $fechaSolicitud, $servicioSolicitado, $descripcionIncidencia, $observaciones]);
    }

    public function actualizarServicioEntregaMaterial($idServicio, $solicitante, $entrega, $atiende, $oficio, $fechaSolicitud, $descripcionVideo, $cantidadVideos, $periodo, $periodoInicial, $periodoFinal, $equipo) {
        $sql = "CALL NombreDelProcedimientoAlmacenado(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idServicio, $solicitante, $entrega, $atiende, $oficio, $fechaSolicitud, $descripcionVideo, $cantidadVideos, $periodo, $periodoInicial, $periodoFinal, $equipo]);
    }
    
}
