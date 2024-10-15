<?php
require 'config/config.php';
require 'config/conexion.php';
include './src/Controllers/ServicioController.php';
include './src/Controllers/ActivosController.php';
include './src/Controllers/PersonalController.php';
session_start();

// Verificar si la sesión está activa
if (!isset($_SESSION['NumeroEmpleado'])) {
    // Redirigir a la página de inicio de sesión si no hay sesión activa
    header('Location:' . BASE_URL . 'src/auth/login.php');
    exit();
}

// Obtener Numero de empleado de la sesión
$NumeroEmpleado = $_SESSION['NumeroEmpleado'];
// Obtener la conexión
$conn = Conexion::conectar();

// Consulta para obtener el rol del usuario
$sql = "SELECT r.Pk_IDRol, r.DescripcionRol, p.Nombre 
        FROM Personal p
        JOIN Plaza pl ON p.Fk_IDPlaza_Plaza = pl.Pk_IDPlaza
        JOIN Roles r ON pl.Fk_IDRol_Roles = r.PK_IDRol
        WHERE p.Pk_NumeroEmpleado = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $NumeroEmpleado);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $rol = $row['Pk_IDRol'];

    // Inicializar variable para el mensaje
    $mensaje = '';

    // Controlador y mensajes según el rol
    switch ($rol) {
        case 1:
            $mensaje = $row['Nombre'];
            $sudoPersonal = new PersonalController();
            $sudoActivos = new ActivosController();
            $sudoServicios = new ServicioController();
            break;
        case 2:
            $mensaje = $row['Nombre'];
            $encargadoServicios = new ServicioController();
            break;
        case 3:
            $mensaje = $row['Nombre'];
            $suActivos = new ActivosController();
            $suServicios = new ServicioController();
            break;
        case 4:
            $mensaje = $row['Nombre'];
            // Aquí puedes agregar más lógica para el rol 4 si es necesario
            break;
        default:
            $mensaje = "Hola usuario no identificado";
            break;
    }
} else {
    $mensaje = "Usuario no encontrado";
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?php echo SITE_NAME; ?></title>
    <?php include BASE_PATH . 'src/Views/partials/head.php'; ?>
</head>

<body>
    <?php include BASE_PATH . 'src/Views/partials/header.php'; ?>

    <?php
    if ($rol == 1) {
        $sudoActivos->index();
        $sudoPersonal->index();
        $sudoServicios->index($rol);
    } else if ($rol == 2) {
        $encargadoServicios->index($rol);
    } else if ($rol == 3) {
        $suActivos->index();
        $suServicios->index($rol);
    } else if ($rol == 4) {
        echo "HOLA AMIGUITO: " . $row['Nombre'] . "<br>NO TIENES PERMISOS DE VER NADA CHIQUITIN";
    }
    ?>
</body>

</html>