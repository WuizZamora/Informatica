    <style>
        .spinner-activos {
            display: none;
            margin: 20px auto;
        }

        .pagination-activos {
            justify-content: center;
        }
    </style>

    <div class="container text-center">
        <?php
        if ($rol == 1 || $rol == 3) {
        ?>
            <h3 class="text-center">ALTA DE ACTIVOS</h3>
            <hr>
            <form id="activosForm" class="needs-validation" autocomplete="off" novalidate>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="NumeroInventario" class="form-label">Número de inventario</label>
                        <input type="text" class="form-control text-center" id="NumeroInventario" name="NumeroInventario" maxlength="100" required>
                    </div>
                    <div class="col-md-4">
                        <label for="CABMSActivo" class="form-label">CABMS</label>
                        <input type="number" min="0" class="form-control text-center" id="CABMSActivo" name="CABMSActivo" oninput="checkLength(this)" required>
                    </div>
                    <div class="col-md-4">
                        <label for="ProgresivoActivo" class="form-label">Progresivo (Sin ceros)</label>
                        <input type="number" min="0" class="form-control text-center" id="ProgresivoActivo" name="ProgresivoActivo" oninput="checkLengthProgresivo(this)" required>
                    </div>
                    <div class="col-md-4">
                        <label for="DescripcionActivo" class="form-label">Descripción</label>
                        <input type="text" class="form-control text-center" id="DescripcionActivo" name="DescripcionActivo" maxlength="150" required>
                    </div>
                    <div class="col-md-4">
                        <label for="ResguardanteActivo" class="form-label">Resguardante</label>
                        <select class="form-select text-center" id="ResguardanteActivo" name="ResguardanteActivo" required>
                            <option disabled selected value="">Selecciona a un resguardante</option>
                        </select>
                    </div>
                    <div class="col-md-4" style="display: none;">
                        <label for="EstatusActivo" class="form-label">Estatus</label>
                        <select class="form-select text-center" id="EstatusActivo" name="EstatusActivo" required>
                            <option disabled value="">Selecciona el estatus del activo</option>
                            <option value="ACTIVO" selected>Activo</option>
                            <option value="PROCESO DE BAJA">Proceso de baja</option>
                        </select>
                    </div>
                    <div class="col-md-12 mt-3">
                        <button class="btn btn-danger" type="submit">Guardar</button>
                    </div>
                </div>
                <!-- Modal de Confirmación -->
                <div class="modal fade" id="confirmModalActivos" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmModalLabel">Confirmación</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Por favor, revisa la información antes de guardar:</p>
                                <table class="table table-bordered table-striped table-responsive">
                                    <thead>
                                    </thead>
                                    <tbody id="formDataReviewActivos">
                                        <!-- Los datos del formulario se llenarán aquí dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-primary" id="confirmSubmitActivos">Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php } ?>
        <h3 class="text-center">DETALLES ACTIVOS</h3>
        <hr>
        <!-- Campo de búsqueda -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="input-group mb-3">
                    <span class="input-group-text" id="search-icon">
                        <i class="bi bi-search"></i> <!-- Ícono de búsqueda -->
                    </span>
                    <input
                        type="text"
                        id="buscarCABMS"
                        class="form-control"
                        placeholder="Buscar por CABMS-Progresivo"
                        aria-label="Buscar por CABMS-Progresivo"
                        aria-describedby="search-icon"
                        oninput="actualizarActivos()" />
                </div>
            </div>
        </div>

        <!-- Spinner de carga -->
        <div class="spinner-border text-primary spinner-activos" role="status" id="spinnerActivos">
            <span class="visually-hidden">Cargando...</span>
        </div>

        <!-- Tabla de Activos -->
        <div class="table-responsive">
            <table class="table table-striped-columns table-hover text-center">
                <thead class="table-primary">
                    <tr>
                        <th>ID ACTIVO</th>
                        <th>
                            CABMS-Progresivo
                        </th>
                        <th>Descripción</th>
                        <th>Estado de Conservación</th>
                        <th>Resguardante</th>
                        <?php if ($rol != 2) { ?>
                            <th>Acciones</th>
                        <?php } else { ?>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody id="tablaActivos"></tbody>
            </table>
        </div>

        <!-- Paginación -->
        <nav>
            <ul class="pagination pagination-activos" id="paginacionActivos"></ul>
        </nav>
    </div>

    <!-- MODAL UPDATE -->
    <div class="modal fade" id="editModalActivos" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabelActivos">Editar Activos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" id="modalBodyActivos">
                    <!-- Aquí se mostrará la información del servicio -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="saveButtonActivos">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const userRole = <?php echo json_encode($rol); ?>; // Pasar el rol como variable JavaScript
    </script>
    <script src="./src/Views/Activos/js/activos.js"></script>