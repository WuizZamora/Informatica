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
        $query = "SELECT A.Pk_IDActivo, A.NumeroInventario, A.CABMS, A.Progresivo, A.Descripcion, A.Estatus, 
        CONCAT(P.PrimerApellido, ' ', P.SegundoApellido, ' ', P.Nombres) AS NombreResguardante
        FROM Activos A
        LEFT JOIN 
            Personal P ON A.Fk_Resguardante_Personal = P.Pk_NumeroEmpleado
        ORDER BY Pk_IDActivo DESC";

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

    public function guardarActivo($numeroInventario, $cabmsActivo, $progresivoActivo, $descripcionActivo, $resguardanteActivo, $estatusActivo)
    {
        $query = "INSERT INTO Activos (NumeroInventario, CABMS, Progresivo, Descripcion, Fk_Resguardante_Personal, Estatus)
            VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);

        if ($stmt) {
            $stmt->bind_param("siisis", $numeroInventario, $cabmsActivo, $progresivoActivo, $descripcionActivo, $resguardanteActivo, $estatusActivo);

            if ($stmt->execute()) {
                $insertId = $stmt->insert_id;
                $stmt->close();
                return $insertId;
            } else {
                $stmt->close();
                return false;
            }
        } else {
            return false;
        }
    }

    public function obtenerActivoDetalles($idActivo)
    {
        $query = "SELECT * FROM Activos WHERE Pk_IDActivo = ?";
        $stmt = $this->db->prepare($query);

        // Verifica si la preparación del statement fue exitosa
        if ($stmt === false) {
            return ['error' => 'Error al preparar la consulta: ' . $this->db->error];
        }

        $stmt->bind_param('i', $idActivo);
        $stmt->execute();

        // Obtener los resultados
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $activo = $result->fetch_assoc();
            return $activo;
        } else {
            return ['error' => 'Activo no encontrado.'];
        }
    }

    public function actualizarActivo($idActivo, $numeroInventario, $cabms, $progresivo, $descripcionActivo, $resguardante, $estatusUpdate)
    {
        try {
            $stmt = $this->db->prepare("CALL Activo_UPDATE(?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssss", $idActivo, $numeroInventario, $cabms, $progresivo, $descripcionActivo, $resguardante, $estatusUpdate);

            $stmt->execute();

            return ['success' => true, 'message' => 'Activo actualizado exitosamente'];
        } catch (mysqli_sql_exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
