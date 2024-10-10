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
        $query = "SELECT * FROM Servicios
            ORDER BY Pk_IDServicio DESC
        "; // Ajusta la consulta según tu tabla
        $result = $this->db->query($query);
        $VistaDeServicios = $result->fetch_all(MYSQLI_ASSOC);
        return $VistaDeServicios;
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
        // Guardar en tabla Atencion_Soporte
        $query = "INSERT INTO Servicios_Incidencias (Fk_IDServicio_Servicios, ServicioSolicitado, Descripcion, Observaciones) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param(
            "isss",
            $idServicio,
            $campos['ServicioSolicitado'],
            $campos['DetallesServicioIncidencia'],
            $campos['ObservacionesServicioIncidencia'],
        );

        if (!$stmt->execute()) {
            error_log("Error en la consulta: " . $stmt->error);
            return false; // O lanzar una excepción
        }

        return true; // Retornar true si la inserción fue exitosa
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
            // Ahora guarda en la tabla Servicios_Tecnicos
            $queryServicioTecnico = "INSERT INTO Servicios_Tecnicos (Fk_IDServicio_Servicios, Fk_IDActivo_Activos, Descripcion, Evaluacion) VALUES (?, ?, ?, ?)";
            $stmtServicioTecnico = $this->db->prepare($queryServicioTecnico);
            $stmtServicioTecnico->bind_param(
                "iiss",
                $idServicio,
                $pkIDActivo,
                $campos['DescripcionEstado'],
                $campos['EstadoConservacion'] // Aquí debes usar la evaluación correspondiente ('FUNCIONAL' o 'NO FUNCIONAL')
            );
    
            return $stmtServicioTecnico->execute();
        } else {
            // Si no encontramos un activo, devolvemos false
            return false;
        }
    }
    
    

    // Otros métodos (si lo necesitas) como crearServicio, actualizarServicio, eliminarServicio
}
