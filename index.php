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
    $nombrePersonal = $row['Nombre'];
}

$Activos = new ActivosController();
$Personal = new PersonalController();
$Servicios = new ServicioController();

// Cerrar el statement y la conexión
$stmt->close();
$conn->close();

// Establecer página predeterminada si el rol es 2
$page = isset($_GET['page']) ? $_GET['page'] : '';

if ($rol == 2 && empty($page)) {
    $page = 'servicios';
} elseif (empty($page)) {
    $page = ''; // Mantenerlo vacío para mostrar el mensaje de selección
}
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
    if (!empty($page)) {
        switch ($page) {
            case 'activos':
                $Activos->index();
                break;
            case 'personal':
                $Personal->index();
                break;
            case 'servicios':
                $Servicios->index($rol);
                break;
            default:
                echo "<div class='alert alert-warning'>Página no encontrada.</div>";
                break;
        }
    } else {
        echo "
            <div class='container text-center align-items-center justify-content-center d-flex mt-3'>
                <div class='alert alert-info w-50'>Selecciona una opción del menú para continuar.</div>
            </div>
        ";
    }
    ?>
</body>

</html>