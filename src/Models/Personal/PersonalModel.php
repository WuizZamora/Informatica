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
        $query = "SELECT 
            p.Pk_NumeroEmpleado, 
            CONCAT(p.PrimerApellido, ' ', p.SegundoApellido, ' ', p.Nombres) AS NombreCompleto
        FROM 
            Personal p
        JOIN 
            Plaza pl ON p.Fk_IDPlaza_Plaza = pl.Pk_IDPlaza
        WHERE 
            p.Estatus = 1";

        // Si se requiere filtrar por puesto
        if ($filtrarPuesto) {
            $query .= " AND (pl.Puesto LIKE '%LÍDER COORDINADOR DE PROYECTOS DE REDES Y TELE%' 
                            OR pl.Puesto LIKE '%SISTEMAS%' 
                            OR pl.Puesto LIKE '%JEFE DE UNIDAD DEPARTAMENTAL DE TECNOLOGÍAS%')";
        }

        // Ordenar por nombre completo alfabéticamente
        $query .= " ORDER BY NombreCompleto ASC";

        $result = mysqli_query($this->db, $query);

        if (!$result) {
            throw new Exception("Error en la consulta: " . mysqli_error($this->db));
        }

        $personal = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $personal[] = $row;
        }

        // Verifica si la variable $personal está vacía
        if (empty($personal)) {
            return []; // Retorna un array vacío si no se encontraron registros
        }

        return $personal;
    }

    public function obtenerAllPersonal()
    {
        $query = "SELECT 
        Pk_NumeroEmpleado, 
        CONCAT(PrimerApellido, ' ', SegundoApellido, ' ',Nombres) AS NombreCompleto,
        RFC, 
        Estatus
        FROM Personal ORDER BY NombreCompleto ASC";

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

    public function actualizarPersonal($numeroEmpleado, $primerApellido,$segundoApellido, $nombre, $rfc, $plaza, $fechaInicial, $estatusUpdate, $usuarioUpdate, $passUpdate)
    {
        try {
            $stmt = $this->db->prepare("CALL Personal_UPDATE_PersonalAndPlaza(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssissss", $numeroEmpleado,$primerApellido, $segundoApellido, $nombre, $rfc, $plaza, $fechaInicial, $estatusUpdate, $usuarioUpdate, $passUpdate);

            $stmt->execute();

            return ['success' => true, 'message' => 'Personal actualizado exitosamente'];
        } catch (mysqli_sql_exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function obtenerPlaza($todas = false)
    {
        $query = $todas ?
            "SELECT Pk_IDPlaza, Puesto FROM Plaza ORDER BY Puesto ASC" :
            "SELECT Pk_IDPlaza, Puesto FROM Plaza WHERE EstatusPlaza = 1 ORDER BY Puesto ASC";

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

    public function guardarPersonal($numeroEmpleado, $primerApellidoEmpleado,$segundoApellidoEmpleado, $nombreEmpleado, $rfcEmpleado, $plazaEmpleado, $fechaInicial, $estatusEmpleado)
    {
        try {
            // Primero, verificamos si el número de empleado ya existe en la base de datos
            $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM Personal WHERE Pk_NumeroEmpleado = ?");
            $stmt->bind_param("i", $numeroEmpleado);
            $stmt->execute();

            // Asociamos el resultado a la variable $count
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $count = $row['total'];
            $stmt->close();

            if ($count > 0) {
                // Si el número de empleado ya existe, devolvemos un error sin necesidad de ejecutar el procedimiento
                return ['success' => false, 'error' => 'El número de empleado ya existe.'];
            }

            // Si no existe, procedemos a llamar al procedimiento almacenado
            $stmt = $this->db->prepare("CALL Personal_INSERT(?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssiss", $numeroEmpleado, $primerApellidoEmpleado, $segundoApellidoEmpleado, $nombreEmpleado, $rfcEmpleado, $plazaEmpleado, $fechaInicial, $estatusEmpleado);

            $stmt->execute();

            return ['success' => true, 'message' => 'Personal registrado exitosamente'];
        } catch (mysqli_sql_exception $e) {
            // Si ocurre cualquier otro error, lo devolvemos como error general
            return ['success' => false, 'error' => 'Error al guardar el personal: ' . $e->getMessage()];
        }
    }


    public function obtenerPersonalAndPlaza($numeroEmpleado)
    {
        $query = "SELECT 
            CONCAT(p.PrimerApellido, ' ', p.SegundoApellido, ' ', p.Nombres) AS NombreCompleto,
            pl.Puesto FROM Personal p
            JOIN Plaza pl ON p.Fk_IDPlaza_Plaza = pl.Pk_IDPlaza
            WHERE p.Pk_NumeroEmpleado = ?";
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
            return ['error' => 'Personal no encontrado.']; // Mensaje de error si no se encuentra
        }
    }
}
