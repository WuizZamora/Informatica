<?php
require_once __DIR__ . '/../../../config/conexion.php'; // Asegúrate de ajustar la ruta correcta

class ActivosModel
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar(); // Conexión a la BD
    }

    public function obtenerActivos()
    {
        $query = "SELECT * FROM CABMS_ACTIVOS";
        $result = mysqli_query($this->db, $query);

        if (!$result) {
            throw new Exception("Error en la consulta: " . mysqli_error($this->db));
        }

        $activos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $activos[] = $row;
        }
        return $activos;
    }

    public function obtenerProgresivo($cabms = null)
    {
        // Modificar la consulta según si se recibe un CABMS
        $query = "SELECT DISTINCT Progresivo FROM Activos WHERE Estatus = 'ACTIVO'";
        if ($cabms) {
            $query .= " AND CABMS = '" . mysqli_real_escape_string($this->db, $cabms) . "'"; // Asegúrate de escapar la variable
        }
        $query .= " ORDER BY Progresivo";
        
        $result = mysqli_query($this->db, $query);
    
        if (!$result) {
            throw new Exception("Error en la consulta: " . mysqli_error($this->db));
        }
    
        $activos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $activos[] = $row;
        }
        return $activos;
    }    

    public function obtenerAllActivos()
    {
        // Consulta base con orden alfabético por nombre
        $query = "SELECT * FROM Activos 
        ORDER BY Pk_IDActivo ASC";

        $result = mysqli_query($this->db, $query);

        if (!$result) {
            throw new Exception("Error en la consulta: " . mysqli_error($this->db));
        }
        $Activos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $Activos[] = $row;
        }
        return $Activos;
    }

}
