<div class="container text-center">
    <?php
    if ($rol == 1 || $rol == 3) {
    ?>
        <h3>ALTA DE PERSONAL</h3>
        <hr>
        <form id="personalForm" class="needs-validation" autocomplete="off" novalidate>
            <div class="row">
                <div class="col-md-4">
                    <label for="NumeroEmpleado" class="form-label">Número de empleado</label>
                    <input type="number" class="form-control text-center" id="NumeroEmpleado" name="NumeroEmpleado" min="0" max="214748364" required oninput="checkLength(this)">
                </div>
                <script>
                    function checkLength(element) {
                        if (element.value.length > 9) {
                            element.value = element.value.slice(0, 9);
                        }
                    }
                </script>
                <div class="col-md-4">
                    <label for="NombreEmpleado" class="form-label">Nombre completo</label>
                    <input type="text" class="form-control text-center" id="NombreEmpleado" name="NombreEmpleado" maxlength="150" required>
                </div>
                <div class="col-md-4">
                    <label for="RFCEmpleado" class="form-label">RFC</label>
                    <input type="text" class="form-control text-center" id="RFCEmpleado" name="RFCEmpleado" maxlength="15">
                </div>
                <script>
                    document.getElementById("NombreEmpleado").addEventListener("input", function() {
                        this.value = this.value.toUpperCase();
                    });

                    document.getElementById("RFCEmpleado").addEventListener("input", function() {
                        this.value = this.value.toUpperCase();
                    });
                </script>

                <div class="col-md-4">
                    <label class="form-label" for="PlazaEmpleado">Plaza</label>
                    <select class="form-select" name="PlazaEmpleado" id="PlazaEmpleado" required>
                        <option disabled selected value="" class="text-center">Selecciona una plaza</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="FechaInicial" class="form-label">Fecha inicial</label>
                    <input type="date" class="form-control text-center" id="FechaInicial" name="FechaInicial" required>
                </div>
                <div class="col-md-4" style="display: none;">
                    <label for="EstatusEmpleado" class="form-label">Estatus</label>
                    <select class="form-select text-center" id="EstatusEmpleado" name="EstatusEmpleado" required>
                        <option disabled value="">Selecciona el estatus del activo</option>
                        <option value="1" selected>Vigente</option>
                        <option value="0">No vigente</option>
                    </select>
                </div>
                <div class="col-md-12 mt-3">
                    <button class="btn btn-danger" type="submit">Guardar</button>
                </div>
            </div>
            <!-- Modal de Confirmación -->
            <div class="modal fade" id="confirmModalPersonal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
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
                                <tbody id="formDataReviewPersonal">
                                    <!-- Los datos del formulario se llenarán aquí dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="confirmSubmitPersonal">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php } ?>
    <h3 class="text-center">DETALLES DEL PERSONAL ACTIVO</h3>
    <hr>

    <!-- Filtro de búsqueda -->
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="input-group mb-3">
                <span class="input-group-text" id="search-icon">
                    <i class="bi bi-search"></i> <!-- Ícono de búsqueda -->
                </span>
                <input
                    type="text"
                    id="filtroPersonal"
                    class="form-control"
                    placeholder="Buscar por numero de empleado o nombre"
                    aria-label="Buscar por numero de empleado o nombre"
                    aria-describedby="search-icon"
                    oninput="filtrarPersonal()" />
            </div>
        </div>
    </div>

    <!-- Tabla de Personal -->
    <div class="table-responsive">
        <table class="table table-striped-columns table-hover text-center">
            <thead class="table-success">
                <tr>
                    <th>NÚMERO DE EMPLEADO</th>
                    <th>NOMBRE</th>
                    <th>RFC</th>
                    <?php if ($rol != 2) { ?>
                        <th>Acciones</th>
                    <?php } else { ?>
                    <?php } ?>
                </tr>
            </thead>
            <tbody id="tablaPersonal"></tbody>
        </table>
    </div>
    <nav id="navegacionPaginas" aria-label="Page navigation" class="mt-4"></nav>
</div>
<!-- MODAL UPDATE -->
<div class="modal fade" id="editModalPersonal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabelPersonal">Editar Personal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" id="modalBodyPersonal">
                <!-- Aquí se mostrará la información del personal -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="saveButtonPersonal">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>
<script>
    const userRole = <?php echo json_encode($rol); ?>; // Pasar el rol como variable JavaScript
</script>
<script src="./src/Views/Personal/js/personal.js"></script>