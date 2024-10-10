<?php
require '../../config/conexion.php'; // Asegúrate de incluir tu archivo de conexión
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']); // Eliminamos espacios en blanco
    $password = $_POST['password'];

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
            header('Location: /INFORMATICA/index.php'); // Redirigir a la página principal en la raíz
            exit();
        } else {
            echo '<div class="alert alert-danger">Credenciales incorrectas</div>'; // Mensaje genérico
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
    <title>Iniciar Sesión</title>
</head>

<body>
    <div class="container mt-5">
        <h2>Iniciar Sesión</h2>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
        </form>
    </div>
</body>

</html>