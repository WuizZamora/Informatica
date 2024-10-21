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

        <h1>FORM PARA CREAR ACTIVOS</h1>
        <hr>
        <form id="activosForm" class="needs-validation" autocomplete="off" novalidate>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="NumeroInventario" class="form-label">Número de inventario</label>
                    <input type="text" class="form-control text-center" id="NumeroInventario" name="NumeroInventario" required>
                </div>
                <div class="col-md-4">
                    <label for="CABMSActivo" class="form-label">CABMS</label>
                    <input type="number" min="0" class="form-control text-center" id="CABMSActivo" name="CABMSActivo" required>
                </div>
                <div class="col-md-4">
                    <label for="ProgresivoActivo" class="form-label">Progresivo</label>
                    <input type="number" min="0" class="form-control text-center" id="ProgresivoActivo" name="ProgresivoActivo" required>
                </div>
                <div class="col-md-4">
                    <label for="DescripcionActivo" class="form-label">Descripción</label>
                    <input type="text" class="form-control text-center" id="DescripcionActivo" name="DescripcionActivo" required>
                </div>
                <div class="col-md-4">
                    <label for="ResguardanteActivo" class="form-label">Resguardante</label>
                    <select class="form-select text-center" id="ResguardanteActivo" name="ResguardanteActivo" required>
                        <option disabled selected value="">Selecciona a un resguardante</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="EstatusActivo" class="form-label">Estatus</label>
                    <select class="form-select text-center" id="EstatusActivo" name="EstatusActivo" required>
                        <option disabled value="">Selecciona el estatus del activo</option>
                        <option value="ACTIVO" selected>Activo</option>
                        <option value="PROCESO DE BAJA">Proceso de baja</option>
                        <option value="BAJA">Baja</option>
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
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <!-- <tr>
                                        <th>Campo</th>
                                        <th>Valor</th>
                                    </tr> -->
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
        <h1 class="text-center">Activos</h1>
        <hr>
        <p class="text-center">Contenido relacionado con los activos...</p>

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
                        <th>Acciones</th>
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


    <script>
        const userRole = <?php echo json_encode($rol); ?>; // Pasar el rol como variable JavaScript
        const tablaActivos = document.getElementById('tablaActivos');
        const spinnerActivos = document.getElementById('spinnerActivos');
        const paginacionActivos = document.getElementById('paginacionActivos');
        const filasPorPagina = 10;
        const paginasVisibles = 5;
        let paginaActualActivos = 1;
        let datosActivos = [];

        function toggleSpinnerActivos(mostrar) {
            spinnerActivos.style.display = mostrar ? 'block' : 'none';
        }

        function actualizarActivos() {
            toggleSpinnerActivos(true); // Mostrar el spinner al iniciar la actualización
            fetch('src/Models/Activos/consultar_activos.php')
                .then(response => {
                    if (!response.ok) throw new Error('Error al consultar los activos');
                    return response.json();
                })
                .then(data => {
                    datosActivos = data;
                    renderTablaActivos();
                    renderPaginacionActivos();
                })
                .catch(error => console.error('Error:', error))
                .finally(() => toggleSpinnerActivos(false)); // Ocultar el spinner al finalizar
        }
        // Llamada inicial para cargar los datos
        actualizarActivos();

        // Actualizar la tabla cada 10 segundos
        // setInterval(actualizarActivos, 10000);

        function renderTablaActivos() {
            tablaActivos.innerHTML = '';
            const inicio = (paginaActualActivos - 1) * filasPorPagina;
            const fin = inicio + filasPorPagina;
            const activosPagina = datosActivos.slice(inicio, fin);

            if (activosPagina.length === 0) {
                tablaActivos.innerHTML = '<tr><td colspan="4">No hay activos disponibles</td></tr>';
                return;
            }

            activosPagina.forEach(activo => {
                tablaActivos.innerHTML += `
            <tr>
                <td>${activo.Pk_IDActivo}</td>
                <td>${activo.CABMS}-${activo.Progresivo}</td>
                <td>${activo.Descripcion}</td>
                <td>${activo.Estatus}</td>
                <td> 
                ${userRole == 1 || userRole == 3 ? `<a href="/INFORMATICA/src/Models/" target="_blank" class="btn btn-success">Editar</a>` : ""}           
                </td>
            </tr>`;
            });
        }

        function renderPaginacionActivos() {
            const totalPaginas = Math.ceil(datosActivos.length / filasPorPagina);
            paginacionActivos.innerHTML = '';

            const paginaInicio = Math.max(1, paginaActualActivos - Math.floor(paginasVisibles / 2));
            const paginaFin = Math.min(totalPaginas, paginaInicio + paginasVisibles - 1);

            if (paginaActualActivos > 1) {
                paginacionActivos.appendChild(crearItemPagina('Anterior', paginaActualActivos - 1));
            }

            for (let i = paginaInicio; i <= paginaFin; i++) {
                const itemPagina = crearItemPagina(i, i);
                if (i === paginaActualActivos) itemPagina.classList.add('active');
                paginacionActivos.appendChild(itemPagina);
            }

            if (paginaActualActivos < totalPaginas) {
                paginacionActivos.appendChild(crearItemPagina('Siguiente', paginaActualActivos + 1));
            }
        }

        function crearItemPagina(texto, pagina) {
            const itemPagina = document.createElement('li');
            itemPagina.className = 'page-item';
            itemPagina.innerHTML = `<a class="page-link" href="#">${texto}</a>`;
            itemPagina.addEventListener('click', (e) => {
                e.preventDefault();
                paginaActualActivos = pagina;
                renderTablaActivos();
                renderPaginacionActivos();
            });
            return itemPagina;
        }

        // Carga de datos en los selects
        function cargarDatosInicialesActivo() {
            fetch("./src/Models/Personal/obtener_personal.php")
                .then((response) => response.json())
                .then((data) => {
                    llenarSelectActivo(data, "ResguardanteActivo");

                })
                .catch((error) => console.error("Error fetching personal data:", error));
        }

        function llenarSelectActivo(data, selectId, valueKey = "Pk_NumeroEmpleado", textKey = "Nombre") {
            const select = document.getElementById(selectId);
            data.forEach((item) => {
                const option = document.createElement("option");
                option.value = item[valueKey];
                option.textContent = `${item[valueKey]} - ${item[textKey]}`;
                select.appendChild(option);
            });
        }

        // VALIDACIÓN BOOTSTRAP
        (() => {
            "use strict";
            const forms = document.querySelectorAll(".needs-validation");

            Array.from(forms).forEach((form) => {
                form.addEventListener("submit", (event) => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    } else {
                        event.preventDefault(); // Prevenimos el envío por ahora
                        mostrarDatosEnModal(); // Llenamos el modal con los datos del formulario
                        confirmModal.show(); // Mostramos el modal
                    }
                    form.classList.add("was-validated");
                }, false);
            });
        })();

        const confirmModal = new bootstrap.Modal(
            document.getElementById("confirmModalActivos")
        );
        const formDataReview = document.getElementById("formDataReviewActivos");
        
        // Función para mostrar los datos del formulario en el modal
        function mostrarDatosEnModal() {
            formDataReview.innerHTML = ""; // Limpiamos cualquier contenido previo
            const formData = new FormData(activosForm);

            // Iteramos sobre los campos del formulario y los añadimos a la tabla
            formData.forEach((value, key) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                <td><strong>${key}</strong></td>
                <td>${value}</td>
            `;
                formDataReview.appendChild(row);
            });
        }

        document.addEventListener("DOMContentLoaded", () => {
            cargarDatosInicialesActivo();
            const activosForm = document.getElementById("activosForm");

            const confirmSubmitButton = document.getElementById("confirmSubmitActivos");

            // Evento para confirmar el envío y hacer el fetch al backend
            confirmSubmitButton.addEventListener("click", () => {
                const formData = new FormData(activosForm); // Capturamos los datos del formulario

                fetch("ruta/al/backend.php", {
                        method: "POST",
                        body: formData,
                    })
                    .then((response) => {
                        if (response.ok) {
                            return response.json(); // Asumimos que el backend responde con JSON
                        } else {
                            throw new Error("Error en la solicitud");
                        }
                    })
                    .then((data) => {
                        console.log("Respuesta del servidor:", data);
                        confirmModal.hide(); // Ocultamos el modal
                        activosForm.reset(); // Reiniciamos el formulario
                        activosForm.classList.remove("was-validated"); // Limpiamos la validación
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                    });
            });
        });
    </script>