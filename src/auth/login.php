<?php
require '../../config/conexion.php';
require '../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']); // Eliminamos espacios en blanco
    $password = $_POST['pass'];

    // Validación básica del correo electrónico
    if (!filter_var($usuario, FILTER_VALIDATE_EMAIL)) {
        echo '<div class="alert alert-danger">Formato de correo inválido</div>';
        exit();
    }

    // Consulta para verificar el usuario
    $sql = "SELECT Correo, Pass, Fk_NumeroEmpleado_Personal FROM Usuarios WHERE Correo = ?";
    $conn = Conexion::conectar();
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verifica la contraseña
        if (password_verify($password, $row['Pass'])) {
            $_SESSION['NumeroEmpleado'] = $row['Fk_NumeroEmpleado_Personal']; // Guardar datos en la sesión
            header('Location: ' . BASE_URL . 'index.php'); // Redirigir a la página principal
            exit();
        } else {
            echo '<div class="alert alert-danger">Credenciales incorrectas</div>';
        }
    } else {
        echo '<div class="alert alert-danger">Credenciales incorrectas</div>';
    }

    // Aquí podrías mover la lógica de obtención del rol a un método separado
    if (isset($_SESSION['NumeroEmpleado'])) {
        $numeroEmpleado = $_SESSION['NumeroEmpleado'];
        $query = "SELECT R.Pk_IDRol
                    FROM Personal P
                    JOIN Plaza PL ON P.Fk_IDPlaza_Plaza = PL.Pk_IDPlaza
                    JOIN Roles R ON PL.Fk_IDRol_Roles = R.Pk_IDRol
                WHERE P.Pk_NumeroEmpleado = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $numeroEmpleado);
        $stmt->execute();
        $result = $stmt->get_result();
        $rol = $result->fetch_assoc();

        if ($rol) {
            $_SESSION['IDRol'] = $rol['Pk_IDRol']; // Guardar en la sesión
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <?php include BASE_PATH . 'src/Views/partials/head.php'; ?>
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
                <div class="form-floating position-relative">
                    <input type="password" id="pass" name="pass" class="form-control" autocomplete="off" required>
                    <label for="pass" class="form-label">Contraseña:</label>

                    <button type="button" id="togglePassword" class="btn position-absolute" style="right: 0; top: 50%; transform: translateY(-50%); background: transparent; border: none;">
                        <i id="eyeIcon" class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div>
                <button type="submit" class="btn btn-primary w-100 py-2">Iniciar sesión</button>
            </div>
        </form>
    </div>
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('pass');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>

</html>