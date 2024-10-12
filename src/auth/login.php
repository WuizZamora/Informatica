<?php
require '../../config/conexion.php';
require '../../config/config.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']); // Eliminamos espacios en blanco
    $password = $_POST['pass'];

    // Validación básica del correo electrónico
    if (!filter_var($usuario, FILTER_VALIDATE_EMAIL)) {
        echo 'Formato de correo inválido';
        exit();
    }

    // Consulta para verificar el usuario
    $sql = "SELECT Correo, Pass, Fk_NumeroEmpleado_Personal FROM Usuarios WHERE Correo = ?";
    $conn = Conexion::conectar();
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario); // Solo un string para el correo electrónico
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verifica la contraseña (suponiendo que esté hasheada en la base de datos)
        if (password_verify($password, $row['Pass'])) {
            // Guardar datos en la sesión
            $_SESSION['NumeroEmpleado'] = $row['Fk_NumeroEmpleado_Personal']; // Agregar el correo a la sesión (opcional)
            header('Location: '.BASE_URL. 'index.php'); // Redirigir a la página principal en la raíz
            exit();
        } else {
            echo '<div class="container alert alert-danger">Credenciales incorrectas</div>'; // Mensaje genérico
        }
    } else {
        echo '<div class="alert alert-danger">Credenciales incorrectas</div>'; // Mensaje genérico
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <?php include BASE_PATH.'src/Views/partials/head.php'; ?>
</head>
<body>
    
    <div class="container form-signin w-100 m-auto d-flex justify-content-center">
        <form action="login.php" method="POST">
            <h1 class="text-center">INFORMÁTICA</h1>
            <h4 class="h3 mb-3 fw-normal text-center">Inicio de Sesión</h4>
            <div class="text-center mb-4">
            <img src="<?php echo ASSETS_URL; ?>images/cdmx_logo_completo.png" alt="LOGO" width="200">
            </div>
            <div class="mb-3">
                <div class="form-floating">
                    <input type="text" id="usuario" name="usuario" class="form-control" autocomplete="off" required>
                    <label for="usuario" class="form-label">Usuario:</label>
                </div>
            </div>
            <div class="mb-3">
                <div class="form-floating">
                    <input type="password" id="pass" name="pass" class="form-control" autocomplete="off" required>
                    <label for="pass" class="form-label">Contraseña:</label>
                </div>
            </div>
            <div>
                <button type="submit" class="btn btn-primary w-100 py-2">Iniciar sesión</button>
            </div>            
        </form>
    </div>
</body>
</html>