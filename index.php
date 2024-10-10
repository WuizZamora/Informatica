<?php
include './src/Controllers/ServicioController.php';
require_once 'config/conexion.php';
session_start();

// Verificar si la sesión está activa
if (!isset($_SESSION['NumeroEmpleado'])) {
    // Redirigir a la página de inicio de sesión si no hay sesión activa
    header('Location: /INFORMATICA/src/auth/login.php');
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
            $mensaje = "Hola " . $row['Nombre'] . "<br>Tu rol es: " . $row['DescripcionRol'];
            // Aquí puedes agregar más lógica para el rol 1 si es necesario
            break;
        case 2:
            $mensaje = "Hola " . $row['Nombre'] . "<br>Tu rol es: " . $row['DescripcionRol'];
            $controller = new ServicioController();
            // $servicios = $controller->index(); // Captura el contenido de servicios
            break;
        case 3:
            $mensaje = "Hola " . $row['Nombre'] . "<br>Tu rol es: " . $row['DescripcionRol'];
            // Aquí puedes agregar más lógica para el rol 3 si es necesario
            break;
        case 4:
            $mensaje = "Hola " . $row['Nombre'] . "<br>Tu rol es: " . $row['DescripcionRol'];
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
    <title>Página Principal</title>
    <link rel="shortcut icon" href="public/images/logo_cdmx.png">
    <?php include './src/Views/partials/head.php'; ?>
</head>

<body>
    <div class="text-center align-items-center">
        <p><?php echo $mensaje; ?></p>
        <form action="./src/auth/logout.php" method="post">
            <!-- <button type="submit" class="btn btn-primary">Cerrar Sesión</button> -->
            <button type="submit" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z"></path>
                    <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"></path>
                </svg>
                Salir
            </button>
        </form>
    </div>

    <?php
    if ($rol == 2) {
        $controller->index();
    }
    ?>
</body>

</html>