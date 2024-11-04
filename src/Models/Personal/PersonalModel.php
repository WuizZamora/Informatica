<?php
require_once __DIR__ . '/../../../config/conexion.php'; // Asegúrate de ajustar la ruta correcta

class PersonalModel
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar(); // Conexión a la BD
    }

    public function obtenerPersonal($filtrarPuesto = false)
    {
        // Consulta base con orden alfabético por nombre
        $query = "SELECT p.Pk_NumeroEmpleado, p.Nombre FROM Personal p
        JOIN Plaza pl ON p.FK_IDPlaza_Plaza = pl.Pk_IDPlaza
        WHERE p.Estatus = 1";

        // Si se requiere filtrar por puesto
        if ($filtrarPuesto) {
            $query .= " AND (pl.Puesto LIKE '%LÍDER COORDINADOR DE PROYECTOS DE REDES Y TELE%' 
                            OR pl.Puesto LIKE '%SISTEMAS%' 
                            OR pl.Puesto LIKE '%JEFE DE UNIDAD DEPARTAMENTAL DE TECNOLOGÍAS%')";
        }

        // Ordenar por nombre de manera alfabética
        $query .= " ORDER BY p.Nombre ASC";

        $result = mysqli_query($this->db, $query);

        if (!$result) {
            throw new Exception("Error en la consulta: " . mysqli_error($this->db));
        }

        $personal = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $personal[] = $row;
        }
        return $personal;
    }

    public function obtenerAllPersonal()
    {
        $query = "SELECT * FROM Personal ORDER BY Nombre ASC";

        $result = mysqli_query($this->db, $query);

        if (!$result) {
            throw new Exception("Error en la consulta: " . mysqli_error($this->db));
        }
        $personal = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $personal[] = $row;
        }
        return $personal;
    }

    public function obtenerPersonalDetalles($numeroEmpleado)
    {
        $query = "SELECT * FROM Personal WHERE Pk_NumeroEmpleado = ?";
        $stmt = $this->db->prepare($query);

        // Verifica si la preparación del statement fue exitosa
        if ($stmt === false) {
            return ['error' => 'Error al preparar la consulta: ' . $this->db->error];
        }

        $stmt->bind_param('i', $numeroEmpleado);
        $stmt->execute();

        // Obtener los resultados
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $personal = $result->fetch_assoc();
            return $personal;
        } else {
            return ['error' => 'Activo no encontrado.']; // Mensaje de error si no se encuentra
        }
    }

    public function actualizarPersonal($numeroEmpleado, $nombre, $rfc, $plaza, $fechaInicial, $estatusUpdate)
    {
        try {
            $stmt = $this->db->prepare("CALL Personal_UPDATE_PersonalAndPlaza(?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ississ", $numeroEmpleado, $nombre, $rfc, $plaza, $fechaInicial, $estatusUpdate);

            $stmt->execute();

            return ['success' => true, 'message' => 'Personal actualizado exitosamente'];
        } catch (mysqli_sql_exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function obtenerPlaza($todas = false)
    {
        $query = $todas ?
            "SELECT Pk_IDPlaza, Puesto FROM Plaza ORDER BY Pk_IDPlaza ASC" :
            "SELECT Pk_IDPlaza, Puesto FROM Plaza WHERE EstatusPlaza = 1 ORDER BY Pk_IDPlaza ASC";

        $result = mysqli_query($this->db, $query);

        if (!$result) {
            throw new Exception("Error en la consulta: " . mysqli_error($this->db));
        }
        $plazas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $plazas[] = $row;
        }
        return $plazas;
    }

    public function guardarPersonal($numeroEmpleado, $nombreEmpleado, $rfcEmpleado, $plazaEmpleado, $fechaInicial, $estatusEmpleado)
    {
        try {
            $stmt = $this->db->prepare("CALL InsertarPersonal(?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ississ", $numeroEmpleado, $nombreEmpleado, $rfcEmpleado, $plazaEmpleado, $fechaInicial, $estatusEmpleado);

            $stmt->execute();

            return ['success' => true, 'message' => 'Personal registrado exitosamente'];
        } catch (mysqli_sql_exception $e) {
            // Captura el error y devuelve un mensaje adecuado
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
