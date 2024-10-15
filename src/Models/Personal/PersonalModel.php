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
        // Consulta base con orden alfabético por nombre
        $query = "SELECT * FROM Personal 
        ORDER BY Nombre ASC";

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

    // Otros métodos (si lo necesitas)
}
