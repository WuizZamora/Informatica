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
    if (isset($_GET['IDServicio'])) {
        $idServicio = htmlspecialchars($_GET['IDServicio']);
    } else {
        $idServicio = null;
    }
    ?>
    <div id="resultado" class="mt-4"></div>
    <script>
        const idServicio = "<?php echo $idServicio; ?>";
        if (idServicio) {
            fetch(`/INFORMATICA/src/Models/Servicios/consultar_servicio.php?IDServicio=${idServicio}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        document.getElementById('resultado').innerHTML = `<div class="alert alert-danger" role="alert">${data.error}</div>`;
                    } else {
                        let servicioHtml = `
                        <div class="container">
                            <div class="row mb-3">
                                <div class="col-md-12 mb-3">
                                    <strong># Servicio:</strong> ${data.Pk_IDServicio}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Solicitante:</strong> ${data.Solicitante}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Entrega:</strong> ${data.Entrega}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Oficio:</strong> ${data.Oficio}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Tipo de Servicio:</strong> ${data.TipoServicio}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Fecha de Solicitud:</strong> ${data.FechaSolicitud}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Atiende:</strong> ${data.Atiende}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Fecha de Atención:</strong> ${data.FechaAtencion}
                                </div>`;

                        // Mostrar campos adicionales según el tipo de servicio
                        if (data.TipoServicio === 'ENTREGA MATERIAL FÍLMICO') {
                            servicioHtml += `
                            <div class="col-md-6 mb-3">
                                <strong>Cantidad de videos:</strong> ${data.CantidadVideos}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Periodo:</strong> ${data.Periodo}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Periodo Inicial:</strong> ${data.PeriodoInicial}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Periodo Final:</strong> ${data.PeriodoFinal}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Equipo:</strong> ${data.Equipo}
                            </div>
                            <div class="col-md-12 mb-3 text-center">
                                <span style="display: inline-block; border-bottom: 1px solid black; width: 30%;"></span><br>
                                Firma del solicitante
                            </div>
                        `;
                        } else if (data.TipoServicio === 'TÉCNICO') {
                            servicioHtml += `
                            <div class="col-md-6 mb-3">
                                <strong>Area solicitante:</strong> ${data.Area}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Activo:</strong> ${data.Fk_IDActivo_Activos}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Descripción del Activo:</strong> ${data.DescripcionActivo}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>CABMS:</strong> ${data.CABMS}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Progresivo:</strong> ${data.Progresivo}
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Descripción del Servicio:</strong> ${data.Descripcion}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Resultado de la dictaminación:</strong> ${data.Evaluacion}
                            </div>
                            <div class="col-md-12 mb-3 text-center">
                                <span style="display: inline-block; border-bottom: 1px solid black; width: 30%;"></span><br>
                                LIC. ${data.Nombre_JUD_IT}
                            </div>
                            `;
                        } else if (data.TipoServicio === 'INCIDENCIA') {
                            servicioHtml += `
                             <div class="col-md-6 mb-3">
                                <strong>Area solicitante:</strong> ${data.Area}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Servicio solictado:</strong> ${data.ServicioSolicitado}
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Descripción:</strong> ${data.Descripcion}
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Observaciones:</strong> ${data.Observaciones}
                            </div>
                            <div class="col-md-12 mb-3 text-center">
                                <span style="display: inline-block; border-bottom: 1px solid black; width: 30%;"></span><br>
                                LIC. ${data.Nombre_JUD_IT}
                            </div>
                            `;
                        }

                        servicioHtml += `</div></div>`;
                        document.getElementById('resultado').innerHTML = servicioHtml;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('resultado').innerHTML = `<div class="alert alert-danger" role="alert">Ocurrió un error</div>`;
                });
        }
    </script>

</body>

</html>