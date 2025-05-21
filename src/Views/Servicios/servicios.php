    <div class="container">
        <h3 class="text-center">ALTA DE SERVICIOS</h3>
        <hr>
        <form id="servicioForm" class="row g-3 needs-validation text-center" autocomplete="off" enctype="multipart/form-data" novalidate>
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
                <label class="form-label" for="PersonalAtiende">Personal que atiende</label>
                <select class="form-select" name="PersonalAtiende" id="PersonalAtiende" required>
                    <option disabled selected value="" class="text-center">Selecciona un empleado</option>
                </select>
                <div class="invalid-feedback">
                    Selecciona un empleado válido
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
                <input class="form-control" type="text" id="Oficio" name="Oficio" maxlength="250" required>
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

            <!-- Formulario Incidencias -->
            <div id="formIncidencia" style="display:none;">
                <h3>Datos de incidencias</h3>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label" for="ServicioSolicitado">Servicio solicitado</label>
                        <select class="form-select text-center" id="ServicioSolicitado" name="ServicioSolicitado[]" size="9" multiple>
                            <option value="default">Elige una opción</option>
                            <option value="GESTIÓN DE EQUIPOS">GESTIÓN DE EQUIPOS</option>
                            <option value="CARPETA COMPARTIDA">CARPETA COMPARTIDA</option>
                            <option value="CREACIÓN DE USUARIO">CREACIÓN DE USUARIO</option>
                            <option value="CORREO ELECTRÓNICO INSTITUCIONAL">CORREO ELECTRÓNICO INSTITUCIONAL</option>
                            <option value="SOPORTE TÉCNICO">SOPORTE TÉCNICO</option>
                            <option value="ENTREGA DE INSUMOS NUEVOS PARA EQUIPO DE COMPUTO">ENTREGA DE INSUMOS NUEVOS PARA EQUIPO DE COMPUTO</option>
                            <option value="CONECTIVIDAD">CONECTIVIDAD</option>
                            <option value="GESTIÓN DE USUARIOS">GESTIÓN DE USUARIOS</option>
                            <option value="CAPACITACIÓN Y ASESORÍA">CAPACITACIÓN Y ASESORÍA</option>
                            <option value="OTROS">OTROS</option>
                        </select>
                        <div class="invalid-feedback">
                            Ingresa una opción
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="DetallesServicioIncidencia">Detalles del servicio</label>
                        <textarea class="form-control" name="DetallesServicioIncidencia" id="DetallesServicioIncidencia" rows="8" maxlength="400" required> </textarea>
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
                            <option value="TRANSPORTE 1">TRANSPORTE 1</option>
                            <option value="TRANSPORTE 2">TRANSPORTE 2</option>
                            <option value="TRANSPORTE 3">TRANSPORTE 3</option>
                            <option value="TRANSPORTE 4">TRANSPORTE 4</option>
                            <option value="TRANSPORTE 5">TRANSPORTE 5</option>
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
                        <select name="Periodo" id="Periodo" class="form-select text-center">
                            <option selected disabled value="" class="text-center">Elige una opción</option>
                            <option value="N/A">No hay periodo</option>
                            <option value="Primera quincena">1era quincena</option>
                            <option value="Segunda quincena">2da quincena</option>
                        </select>
                    </div>
                    <!-- CALCULA LAS QUINCENAS -->
                    <script>
                        const userRole = <?php echo json_encode($rol); ?>; // Pasar el rol como variable JavaScript
                        const periodoInicial = document.getElementById('PeriodoInicial');
                        const periodoFinal = document.getElementById('PeriodoFinal');
                        const periodoOutput = document.getElementById('Periodo');

                        function calcularPeriodo() {
                            const fechaInicio = periodoInicial.value; // Valor de la fecha inicial
                            const fechaFin = periodoFinal.value; // Valor de la fecha final

                            // Verifica si las fechas son válidas
                            if (!fechaInicio || !fechaFin) {
                                periodoOutput.value = 'N/A';
                                return;
                            }

                            const [anioInicio, mesInicio, diaInicio] = fechaInicio.split('-').map(Number); // Extraer año, mes y día de la fecha inicial
                            const [anioFin, mesFin, diaFin] = fechaFin.split('-').map(Number); // Extraer año, mes y día de la fecha final

                            // Lógica para determinar el periodo
                            if (mesInicio !== mesFin || anioInicio !== anioFin) {
                                periodoOutput.value = 'N/A'; // Diferentes meses o años
                                return;
                            }

                            // Verificar el periodo exacto
                            if (diaInicio === 1 && diaFin === 15) {
                                periodoOutput.value = 'Primera quincena';
                            } else if ((diaInicio === 16 && (diaFin === 30 || diaFin === 31))) {
                                periodoOutput.value = 'Segunda quincena';
                            } else {
                                periodoOutput.value = 'N/A';
                            }
                        }

                        // Agrega un listener para que calcule el periodo al cambiar las fechas
                        periodoInicial.addEventListener('change', calcularPeriodo);
                        periodoFinal.addEventListener('change', calcularPeriodo);
                    </script>
                    <div class="col-md-6">
                        <label class="form-label" for="DescripcionVideos">Descripción</label>
                        <textarea class="form-control text-center" name="DescripcionVideos" id="DescripcionVideos" maxlength="400" rows="8"> </textarea>
                    </div>
                </div>
            </div>

            <!-- Formulario Dictaminacion -->
            <div id="formDictaminacion" style="display: none;">
                <h3>Datos del Activo</h3>
                <!-- Contenedor para los bloques dinámicos -->
                <div id="activosContainer"></div>
                <!-- Botón para agregar un nuevo bloque de activo -->
                <button type="button" id="addActivoBtn" class="btn btn-success mb-3">+</button>
                <!-- Descripción del estado (solo una vez) -->
                <div class="col-md-5 mt-3">
                    <label class="form-label" for="DescripcionEstado">Descripción del estado</label>
                    <textarea class="form-control text-center" name="DescripcionEstado" id="DescripcionEstado" rows="8" maxlength="400" required></textarea>
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
        <!--  -->
        <h3 class="text-center">SEGUIMIENTO DE SERVICIOS</h3>
        <hr>
        <div class="container mt-3 text-center">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="search-icon">
                            <i class="bi bi-search"></i> <!-- Ícono de búsqueda de Bootstrap Icons -->
                        </span>
                        <input
                            type="text"
                            id="searchInput"
                            class="form-control text-center"
                            placeholder="Buscar por Folio, Oficio o Solicitante"
                            aria-label="Buscar por Folio, Oficio o Solicitante"
                            aria-describedby="search-icon"
                            oninput="actualizarServicios()" />
                    </div>
                </div>
            </div>

            <table class="table table-striped-columns table-hover" id="serviciosTable">
                <thead class="table-warning">
                    <tr>
                        <th>FOLIO</th>
                        <th>Personal Solicitante</th>
                        <th>Fecha de solicitud</th>
                        <th>Oficio</th>
                        <th>Fecha de atención</th>
                        <th>Tipo de servicio</th>
                        <th>Estado de la solicitud</th>
                        <th>Soporte Documental</th>
                        <th>Acciones</th>
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

        <!-- MODAL UPDATE -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Editar Servicio</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center" id="modalBody">
                        <!-- Aquí se mostrará la información del servicio -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" id="saveButton">Guardar cambios</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL ESTADO SOLICITUD Y SOPORTE -->
        <div class="modal fade" id="servicioModal" tabindex="-1" aria-labelledby="servicioModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="servicioModalLabel">Detalles del Servicio</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="resultadoModal">
                        <!-- Aquí se mostrará la información del servicio -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" id="saveButtonEstado">Guardar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL FORMATO -->
        <div class="modal fade" id="formatModal" tabindex="-1" role="dialog" aria-labelledby="formatModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formatModalLabel">Servicio incidencias</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center" id="formatModalBody">
                        <!-- Aquí se mostrará la información del servicio -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-success" id="formatSaveButton">Generar formato</button>
                    </div>
                </div>
            </div>
        </div>


        <div id="pdfDataForm" style="display: none;">
            <h4>Ingrese los siguientes datos:</h4>
            <label for="nombreUsuario">Nombre de Usuario:</label>
            <input type="text" id="nombreUsuario" required><br>
            <label for="correoUsuario">Correo Electrónico:</label>
            <input type="email" id="correoUsuario" required><br>
            <button id="generatePdfButton">Generar PDF</button>
        </div>

    </div>
    <script src="./src/Views/Servicios/js/servicios.js"></script>