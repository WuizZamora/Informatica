<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento de Servicios</title>
    <?php include '../partials/head.php'; ?>
</head>

<body>
    <?php
    $idServicio = isset($_GET['IDServicio']) ? htmlspecialchars($_GET['IDServicio']) : null;
    ?>

    <div id="resultado"></div>

    <script>
        const idServicio = "<?php echo $idServicio; ?>";

        if (idServicio) {
            fetch(`/INFORMATICA/src/Models/Servicios/consultar_estado_solicitud.php?IDServicio=${idServicio}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la solicitud');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        document.getElementById('resultado').innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                    } else {
                        mostrarDatos(data);
                    }
                })
                .catch(error => {
                    document.getElementById('resultado').innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
                });
        } else {
            document.getElementById('resultado').innerHTML = '<div class="alert alert-warning">No se proporcionó un ID de servicio.</div>';
        }

        function mostrarDatos(servicio) {
            const {
                Pk_IDServicio,
                EstadoSolicitud,
                SoporteDocumental
            } = servicio;
            const resultadoDiv = document.getElementById('resultado');

            resultadoDiv.innerHTML = `
                <form id="servicioForm" class="needs-validation">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <label for="idServicio" class="form-label"><strong>ID Servicio:</strong></label>
                            <input type="text" class="form-control" id="idServicio" name="Pk_IDServicio" value="${Pk_IDServicio}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label for="estadoSolicitud" class="form-label"><strong>Estado de Solicitud:</strong></label>
                            <select class="form-select" id="estadoSolicitud" name="EstadoSolicitud" required>
                                <option selected disabled value="">Elige una opción</option>
                                <option value="COMPLETADO" ${EstadoSolicitud === 'COMPLETADO' ? 'selected' : ''}>COMPLETADO</option>
                                <option value="PENDIENTE" ${EstadoSolicitud === 'PENDIENTE' ? 'selected' : ''}>PENDIENTE</option>
                                <option value="CANCELADO" ${EstadoSolicitud === 'CANCELADO' ? 'selected' : ''}>CANCELADO</option>
                            </select>
                            <div class="invalid-feedback">Por favor, selecciona un estado.</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><strong>Soporte Documental:</strong></label>
                            ${SoporteDocumental ? `
                                <br><a href="${SoporteDocumental}" target="_blank" class="btn btn-link">Ver documento</a>
                            ` : `
                                <input type="file" class="form-control" id="soporteDocumental" name="SoporteDocumental" required>
                                <div class="invalid-feedback">Por favor, sube un documento.</div>
                            `}
                        </div>
                    </div>  
                </form>
            `;
        }
    </script>

</body>

</html>