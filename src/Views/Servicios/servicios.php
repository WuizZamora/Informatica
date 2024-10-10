<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios | Alta</title>
    <script src="./src/Views/Servicios/js/servicios.js"></script>
</head>

<body>
    <div class="container">
        <h3 class="text-center">ALTA DE SERVICIOS</h3>
        <hr>
        <form id="servicioForm" class="row g-3 needs-validation text-center" autocomplete="off" enctype="multipart/form-data" novalidate>
            <div class="col-md-4">
                <label class="form-label" for="PersonalSolicitante">Personal solicitante</label>
                <select class="form-select" name="PersonalSolicitante" id="PersonalSolicitante" required>
                    <option disabled selected value="" class="text-center">Selecciona un empleado</option>
                </select>
                <div class="invalid-feedback">
                    Selecciona un empleado válido
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="PersonalEntrega">Personal que entrega</label>
                <select class="form-select" name="PersonalEntrega" id="PersonalEntrega" required>
                    <option disabled selected value="" class="text-center">Selecciona un empleado</option>
                </select>
                <div class="invalid-feedback">
                    Selecciona un empleado válido
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="PersonalAtiende">Personal que atiende</label>
                <select class="form-select" name="PersonalAtiende" id="PersonalAtiende" required>
                    <option disabled selected value="" class="text-center">Selecciona un empleado</option>
                </select>
                <div class="invalid-feedback">
                    Selecciona un empleado válido
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="IDTipoServicio">Tipo de servicio</label>
                <select class="form-select text-center" id="IDTipoServicio" name="IDTipoServicio" onchange="mostrarFormulario()" required>
                    <option selected disabled value="">Elige una opción</option>
                    <option value="INCIDENCIA">Incidencia</option>
                    <option value="ENTREGA MATERIAL FÍLMICO">Entrega de Material Fílmico</option>
                    <option value="TÉCNICO">Dictaminación/Mantenimiento</option>
                </select>
                <div class="invalid-feedback">
                    Ingresa una opción
                </div>
            </div>

            <div class="col-md-4" id="ConfirmacionCampoOficio" style="display: none;">
                <label class="form-label" for="ConfirmacionOficio">¿Existe un oficio?</label>
                <select class="form-select text-center" id="ConfirmacionOficio" name="ConfirmacionOficio" onchange="manejarOficio(this.value)">
                    <option selected disabled value="">Elige una opción</option>
                    <option value="SI">Si</option>
                    <option value="NO">No</option>
                </select>
                <div class="invalid-feedback">
                    Ingresa una opción
                </div>
            </div>

            <div class="col-md-4" id="CampoOficio" style="display: none;">
                <label for="Oficio" class="form-label">Oficio</label>
                <input class="form-control" type="text" id="Oficio" name="Oficio" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="FechaSolicitud">Fecha de solicitud</label>
                <input type="date" class="form-control text-center" name="FechaSolicitud" id="FechaSolicitud" required>
            </div>
            <div class="col-md-3" style="display: none;">
                <label class="form-label" for="FechaAtencion">Fecha de atención</label>
                <input type="datetime-local" class="form-control text-center" name="FechaAtencion" id="FechaAtencion" required readonly>
                <div class="invalid-feedback">
                    Ingresa una fecha
                </div>
            </div>
            <div class="col-md-4" style="display: none;">
                <label for="DocumentoSoporte" class="form-label">Documento soporte del servicio</label>
                <input class="form-control" type="file" id="DocumentoSoporte" name="DocumentoSoporte">
            </div>

            <!-- Formulario Incidencias -->
            <div id="formIncidencia" style="display:none;">
                <h3>Datos de incidencias</h3>
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label" for="ServicioSolicitado">Servicio solicitado</label>
                        <select class="form-select text-center" id="ServicioSolicitado" name="ServicioSolicitado">
                            <option selected disabled value="">Elige una opción</option>
                            <option value="GESTIÓN DE EQUIPOS">GESTIÓN DE EQUIPOS </option>
                            <option value="CONECTIVIDAD">CONECTIVIDAD</option>
                            <option value="GESTIÓN DE USUARIOS">GESTIÓN DE USUARIOS</option>
                            <option value="CAPACITACIÓN Y ASESORÍA">CAPACITACIÓN Y ASESORÍA</option>
                        </select>

                        <div class="invalid-feedback">
                            Ingresa una opción
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="DetallesServicioIncidencia">Detalles del servicio</label>
                        <textarea class="form-control text-center" name="DetallesServicioIncidencia" id="DetallesServicioIncidencia" rows="8" required> </textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="ObservacionesServicioIncidencia">Observaciones del servicio</label>
                        <textarea class="form-control text-center" name="ObservacionesServicioIncidencia" id="ObservacionesServicioIncidencia" rows="8" required> </textarea>
                    </div>
                </div>
            </div>

            <!-- Formulario Videos-->
            <div id="formVideos" style="display:none;">
                <h3>Datos de videos</h3>
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label" for="CantidadVideos">Cantidad de videos</label>
                        <input type="number" class="form-control text-center" name="CantidadVideos" id="CantidadVideos" min="1" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="Equipo">Equipo</label>
                        <select name="Equipo" id="Equipo" class="form-select">
                            <option selected disabled value="" class="text-center">Elige una opción</option>
                            <option value="Álvaro Obregón">Álvaro Obregón</option>
                            <option value="Azcapotzalco">Azcapotzalco</option>
                            <option value="Benito Juárez">Benito Juárez</option>
                            <option value="Coyoacán">Coyoacán</option>
                            <option value="Cuajimalpa">Cuajimalpa</option>
                            <option value="Cuauhtémoc">Cuauhtémoc</option>
                            <option value="Gustavo A. Madero">Gustavo A. Madero</option>
                            <option value="Iztacalco">Iztacalco</option>
                            <option value="Iztapalapa">Iztapalapa</option>
                            <option value="Magdalena Contreras">Magdalena Contreras</option>
                            <option value="Miguel Hidalgo">Miguel Hidalgo</option>
                            <option value="Milpa Alta">Milpa Alta</option>
                            <option value="Tláhuac">Tláhuac</option>
                            <option value="Tlalpan">Tlalpan</option>
                            <option value="Venustiano Carranza">Venustiano Carranza</option>
                            <option value="Xochimilco">Xochimilco</option>
                            <option value="INVEA 1">INVEA 1</option>
                            <option value="INVEA 2">INVEA 2</option>
                            <option value="INVEA 3">INVEA 3</option>
                            <option value="INVEA 4">INVEA 4</option>
                            <option value="INVEA 5">INVEA 5</option>
                            <option value="TRANSPORTE 1">TRANSPORTE 1</option>
                            <option value="TRANSPORTE 2">TRANSPORTE 2</option>
                            <option value="TRANSPORTE 3">TRANSPORTE 3</option>
                            <option value="TRANSPORTE 4">TRANSPORTE 4</option>
                            <option value="TRANSPORTE 5">TRANSPORTE 5</option>
                            <option value="TRANSPORTE 6">TRANSPORTE 6</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="PeriodoInicial">Periodo inicial</label>
                        <input type="date" class="form-control text-center" name="PeriodoInicial" id="PeriodoInicial" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="PeriodoFinal">Periodo final</label>
                        <input type="date" class="form-control text-center" name="PeriodoFinal" id="PeriodoFinal" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="Periodo">Periodo</label>
                        <input type="text" class="form-control text-center" name="Periodo" id="Periodo" required readonly>
                    </div>
                    <!-- CALCULA LAS QUINCENAS -->
                    <script>
                        const periodoInicial = document.getElementById('PeriodoInicial');
                        const periodoFinal = document.getElementById('PeriodoFinal');
                        const periodoOutput = document.getElementById('Periodo');

                        function calcularPeriodo() {
                            const fechaInicio = periodoInicial.value; // Valor de la fecha inicial
                            const fechaFin = periodoFinal.value; // Valor de la fecha final

                            // Verifica si las fechas son válidas
                            if (!fechaInicio || !fechaFin) {
                                periodoOutput.value = 'No hay periodo';
                                return;
                            }

                            const diaInicio = parseInt(fechaInicio.split('-')[2]); // Extraer día de la fecha inicial
                            const diaFin = parseInt(fechaFin.split('-')[2]); // Extraer día de la fecha final
                            const mesInicio = parseInt(fechaInicio.split('-')[1]); // Extraer mes de la fecha inicial
                            const mesFin = parseInt(fechaFin.split('-')[1]); // Extraer mes de la fecha final

                            // Lógica para determinar el periodo
                            if (mesInicio !== mesFin) {
                                periodoOutput.value = 'No hay periodo'; // Diferentes meses
                                return;
                            }

                            if (diaInicio >= 1 && diaFin <= 15) {
                                periodoOutput.value = 'Primera quincena';
                            } else if (diaInicio >= 16 && diaFin <= 31) {
                                periodoOutput.value = 'Segunda quincena';
                            } else {
                                periodoOutput.value = 'No hay periodo';
                            }
                        }

                        // Agrega un listener para que calcule el periodo al cambiar las fechas
                        periodoInicial.addEventListener('change', calcularPeriodo);
                        periodoFinal.addEventListener('change', calcularPeriodo);
                    </script>
                    <div class="col-md-3">
                        <label class="form-label" for="DescripcionVideos">Descripción</label>
                        <textarea class="form-control text-center" name="DescripcionVideos" id="DescripcionVideos" rows="8" required> </textarea>
                    </div>
                </div>
            </div>

            <!-- Formulario Dictaminacion-->
            <div id="formDictaminacion" style="display:none;">
                <h3>Datos del Activo</h3>
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label" for="CABMSDictaminacion">CABMS</label>
                        <select class="form-select text-center" name="CABMSDictaminacion" id="CABMSDictaminacion">
                            <option disabled selected value="">Selecciona un activo</option>
                        </select>
                        <div class="invalid-feedback">
                            Selecciona un activo válido
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="ProgresivoDictaminacion">Progresivo</label>
                        <select class="form-select text-center" name="ProgresivoDictaminacion" id="ProgresivoDictaminacion">
                            <option disabled selected value="">Selecciona un progresivo</option>
                        </select>
                        <div class="invalid-feedback">
                            Selecciona un activo válido
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="EstadoConservacion">Estado de conservación</label>
                        <select class="form-select text-center" id="EstadoConservacion" name="EstadoConservacion">
                            <option selected disabled value="">Elige una opción</option>
                            <option value="Funcional">Funcional</option>
                            <option value="No funcional">Baja</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" for="DescripcionEstado">Descripción del estado</label>
                        <textarea class="form-control text-center" name="DescripcionEstado" id="DescripcionEstado" rows="8" required> </textarea>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <button class="btn btn-danger" type="submit">Guardar</button>
            </div>
            <!-- Modal de Confirmación -->
            <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmModalLabel">Confirmación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Por favor, revisa la información antes de guardar:</p>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <!-- <tr>
                                        <th>Campo</th>
                                        <th>Valor</th>
                                    </tr> -->
                                </thead>
                                <tbody id="formDataReview">
                                    <!-- Los datos del formulario se llenarán aquí dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="confirmSubmit">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <br>
        <p id="mensaje" style="display:none;" class="alert alert-success text-center" role="alert"></p> <!-- Párrafo para mostrar mensajes -->


        <!--  -->
        <h3 class="text-center">SEGUIMIENTO DE SERVICIOS</h3>
        <hr>
        <div class="container mt-5 text-center">
            <table class="table table-striped-columns table-hover" id="serviciosTable">
                <thead class="table-warning">
                    <tr>
                        <th># Servicio</th>
                        <th>Personal Solicitante</th>
                        <th>Personal Atiende</th>
                        <th>Fecha de solicitud</th>
                        <th>Oficio</th>
                        <th>Fecha de atención</th>
                        <th>Tipo de servicio</th>
                    </tr>
                </thead>
                <tbody id="serviciosBody">
                    <!-- Aquí se ven los registros -->
                </tbody>
            </table>
            <nav class="d-flex justify-content-center align-items-center">
                <ul class="pagination" id="pagination">
                    <!-- Aquí se generarán los botones de paginación -->
                </ul>
            </nav>
        </div>

    </div>
</body>

</html>