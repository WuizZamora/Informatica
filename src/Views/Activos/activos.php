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
                    <label for="ProgresivoActivo" class="form-label">Progresivo</label>
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
                <div class="col-md-4">
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
    <script>
        function checkLength(element) { //Longitud CABMS
            if (element.value.length > 11) {
                element.value = element.value.slice(0, 11);
            }
        }

        function checkLengthProgresivo(element) { //Longitud Progresivo
            if (element.value.length > 10) {
                element.value = element.value.slice(0, 10);
            }
        }
        const tablaActivos = document.getElementById("tablaActivos");
        const spinnerActivos = document.getElementById("spinnerActivos");
        const paginacionActivos = document.getElementById("paginacionActivos");
        const filasPorPagina = 10;
        const paginasVisibles = 5;
        let paginaActualActivos = 1;
        let datosActivos = [];

        function toggleSpinnerActivos(mostrar) {
            spinnerActivos.style.display = mostrar ? "block" : "none";
        }

        function actualizarActivos() {
            toggleSpinnerActivos(true); // Mostrar el spinner al iniciar la actualización
            fetch("src/Models/Activos/consultar_activos.php")
                .then((response) => {
                    if (!response.ok) throw new Error("Error al consultar los activos");
                    return response.json();
                })
                .then((data) => {
                    datosActivos = data;
                    renderTablaActivos();
                    renderPaginacionActivos();
                })
                .catch((error) => console.error("Error:", error))
                .finally(() => toggleSpinnerActivos(false)); // Ocultar el spinner al finalizar
        }

        const buscarCABMS = document.getElementById("buscarCABMS"); // Input de búsqueda
        let datosFiltrados = []; // Datos filtrados por búsqueda

        // Actualizar la búsqueda cuando el usuario escriba
        buscarCABMS.addEventListener("input", () => {
            const terminoBusqueda = buscarCABMS.value.toLowerCase();
            datosFiltrados = datosActivos.filter((activo) => {
                // Formatear el Progresivo como un número de 6 dígitos
                const progresivoFormateado = String(activo.Progresivo).padStart(6, "0");
                return `${activo.CABMS}-${progresivoFormateado}`
                    .toLowerCase()
                    .includes(terminoBusqueda);
            });
            paginaActualActivos = 1; // Reiniciar a la primera página
            renderTablaActivos();
            renderPaginacionActivos();
        });

        // Modificar renderTablaActivos para usar los datos filtrados si existen
        function renderTablaActivos() {
            tablaActivos.innerHTML = "";
            const inicio = (paginaActualActivos - 1) * filasPorPagina;
            const fin = inicio + filasPorPagina;
            const activosPagina = (
                datosFiltrados.length > 0 ? datosFiltrados : datosActivos
            ).slice(inicio, fin);

            if (activosPagina.length === 0) {
                tablaActivos.innerHTML =
                    '<tr><td colspan="5">No hay activos disponibles</td></tr>';
                return;
            }

            activosPagina.forEach((activo) => {
                // Formatear el Progresivo como un número de 6 dígitos
                const progresivoFormateado = String(activo.Progresivo).padStart(6, "0");

                tablaActivos.innerHTML += `
                <tr>
                    <td>${activo.Pk_IDActivo}</td>
                    <td>${activo.CABMS}-${progresivoFormateado}</td>
                    <td style="word-break: break-word; white-space: normal;">${activo.Descripcion
                    }</td>
                    <td>${activo.Estatus}</td>
                    <td>${activo.NombreResguardante}</td>
                    <td>
                    ${userRole == 1 || userRole == 3
                        ? `<button class="btn btn-primary" onclick="editActivo(${activo.Pk_IDActivo})">Editar</button>`
                        : ""
                    }
                    </td>
                </tr>`;
            });
        }

        function renderPaginacionActivos() {
            const totalPaginas = Math.ceil(datosActivos.length / filasPorPagina);
            paginacionActivos.innerHTML = "";

            const paginaInicio = Math.max(
                1,
                paginaActualActivos - Math.floor(paginasVisibles / 2)
            );
            const paginaFin = Math.min(totalPaginas, paginaInicio + paginasVisibles - 1);

            if (paginaActualActivos > 1) {
                paginacionActivos.appendChild(
                    crearItemPagina("Anterior", paginaActualActivos - 1)
                );
            }

            for (let i = paginaInicio; i <= paginaFin; i++) {
                const itemPagina = crearItemPagina(i, i);
                if (i === paginaActualActivos) itemPagina.classList.add("active");
                paginacionActivos.appendChild(itemPagina);
            }

            if (paginaActualActivos < totalPaginas) {
                paginacionActivos.appendChild(
                    crearItemPagina("Siguiente", paginaActualActivos + 1)
                );
            }
        }

        function crearItemPagina(texto, pagina) {
            const itemPagina = document.createElement("li");
            itemPagina.className = "page-item";
            itemPagina.innerHTML = `<a class="page-link" href="#">${texto}</a>`;
            itemPagina.addEventListener("click", (e) => {
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

        function llenarSelectActivo(
            data,
            selectId,
            valueKey = "Pk_NumeroEmpleado",
            textKey = "Nombre"
        ) {
            const select = document.getElementById(selectId);
            data.forEach((item) => {
                const option = document.createElement("option");
                option.value = item[valueKey];
                option.textContent = `${item[textKey]} - ${item[valueKey]}`;
                select.appendChild(option);
            });
        }

        if (userRole != 2) {
            // VALIDACIÓN BOOTSTRAP
            (() => {
                "use strict";
                const forms = document.querySelectorAll(".needs-validation");

                Array.from(forms).forEach((form) => {
                    form.addEventListener(
                        "submit",
                        (event) => {
                            if (!form.checkValidity()) {
                                event.preventDefault();
                                event.stopPropagation();
                            } else {
                                event.preventDefault(); // Prevenimos el envío por ahora
                                mostrarDatosEnModal(); // Llenamos el modal con los datos del formulario
                                confirmModal.show(); // Mostramos el modal
                            }
                            form.classList.add("was-validated");
                        },
                        false
                    );
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
                    <td style="max-width: 20rem; word-break: break-all;">${value}</td>
                `;
                    formDataReview.appendChild(row);
                });
            }

            function resetForm() {
                const activosForm = document.getElementById("activosForm");
                activosForm.reset(); // Restablece todos los campos del formulario
                activosForm.classList.remove("was-validated"); // Remueve la clase de validación
            }

            function editActivo(id) {
                fetch(
                        `/INFORMATICA/src/Models/Activos/obtener_activo_detalles.php?IDActivo=${id}`
                    )
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.error) {
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: data.error,
                            });
                        } else {
                            // Construir el contenido del modal
                            let modalContent = `
                        <strong># ${data.Pk_IDActivo}</strong>
                        <div class="form-group">
                            <label for="NumeroInventarioUpdate">Número de inventario:</label>
                            <input class="form-control text-center" id="NumeroInventarioUpdate" name="NumeroInventarioUpdate" value="${data.NumeroInventario}">
                        </div>
                        <div class="form-group">
                            <label for="CABMSUpdate">CABMS:</label>
                            <input class="form-control text-center" id="CABMSUpdate" name="CABMSUpdate" value="${data.CABMS}">
                        </div>
                        <div class="form-group">
                            <label for="ProgresivoUpdate">Progresivo:</label>
                            <input type="text" class="form-control text-center" id="ProgresivoUpdate" name="ProgresivoUpdate" value="${data.Progresivo}">
                        </div>
                        <div class="form-group">
                            <label for="DescripcionUpdate">Descripción:</label>
                            <input type="text" class="form-control text-center" id="DescripcionUpdate" name="DescripcionUpdate" value="${data.Descripcion}">
                        </div>
                        <div class="form-group">
                            <label for="ResguardanteUpdate">Resguardante:</label>
                            <select class="form-select text-center" id="ResguardanteUpdate" name="ResguardanteUpdate" required>
                                <option disabled selected value="">Selecciona un empleado</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="EstatusUpdate">Estatus:</label>
                            <select class="form-select text-center" id="EstatusUpdate" name="EstatusActivo" required>
                                <option disabled value="">Selecciona el estatus del activo</option>
                                <option value="ACTIVO">Activo</option>
                                <option value="PROCESO DE BAJA">Proceso de baja</option>
                            </select>
                        </div>
                    `;

                            // Mostrar contenido en el modal
                            document.getElementById("modalBodyActivos").innerHTML = modalContent;

                            // Llenar los selects de resguardante
                            llenarSelectPersonal(
                                "./src/Models/Personal/obtener_personal.php",
                                "ResguardanteUpdate",
                                data.Resguardante
                            ).then(() => {
                                const resguardanteSelect =
                                    document.getElementById("ResguardanteUpdate");
                                resguardanteSelect.value = data.Fk_Resguardante_Personal; // Establecer el valor seleccionado aquí
                            });

                            // Establecer el valor del select de estatus
                            const estatusSelect = document.getElementById("EstatusUpdate");
                            estatusSelect.value = data.Estatus; // Aquí se asigna el valor que viene del backend

                            let myModalActivos = new bootstrap.Modal(
                                document.getElementById("editModalActivos")
                            );
                            myModalActivos.show();

                            document.getElementById("saveButtonActivos").onclick = function() {
                                // Obtener los valores de los campos
                                const idActivo = data.Pk_IDActivo;
                                const numeroInventario = document.getElementById(
                                    "NumeroInventarioUpdate"
                                ).value;
                                const cabms = document.getElementById("CABMSUpdate").value;
                                const progresivo = document.getElementById("ProgresivoUpdate").value;
                                const descripcionActivo =
                                    document.getElementById("DescripcionUpdate").value;
                                const reguardante =
                                    document.getElementById("ResguardanteUpdate").value;
                                const estatusUpdate = document.getElementById("EstatusUpdate").value;

                                // Inicializar un objeto para almacenar los datos del servicio
                                const datosActivo = {
                                    idActivo,
                                    numeroInventario,
                                    cabms,
                                    progresivo,
                                    descripcionActivo,
                                    reguardante,
                                    estatusUpdate,
                                };

                                console.log(datosActivo);
                                // Enviar los datos al backend
                                fetch("/INFORMATICA/src/Models/Activos/actualizar_activo.php", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json",
                                        },
                                        body: JSON.stringify(datosActivo), // Convierte los datos a JSON
                                    })
                                    .then(async (response) => {
                                        const text = await response.text(); // Lee la respuesta como texto
                                        try {
                                            const result = JSON.parse(text); // Intenta parsear como JSON
                                            if (result.success) {
                                                Swal.fire({
                                                    title: "¡Éxito!",
                                                    text: "Datos del activo actualizados exitosamente.",
                                                    icon: "success",
                                                    timer: 3000, // Duración en milisegundos (3 segundos)
                                                    showConfirmButton: false, // No mostrar botón de aceptar
                                                });
                                                myModalActivos.hide(); // Cierra el modal si estás usando uno
                                                actualizarActivos();
                                            } else {
                                                Swal.fire({
                                                    icon: "error",
                                                    title: "Oops...",
                                                    text: result.error, // Aquí se pasa el mensaje del error
                                                });
                                            }
                                        } catch (error) {
                                            console.error("Respuesta inválida del servidor:", text); // Muestra el contenido
                                            alert("Error en la respuesta del servidor.");
                                        }
                                    })
                                    .catch((error) => {
                                        console.error("Error al guardar el activo:", error);
                                        Swal.fire({
                                            icon: "error",
                                            title: "Oops...",
                                            text: "Error al guardar el activo",
                                        });
                                    });
                            };
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Error al cargar los datos",
                        });
                    });
            }
        }


        // Función reutilizable para llenar los selects
        function llenarSelectPersonal(url, selectId, valorSeleccionado) {
            return fetch(url)
                .then((response) => response.json())
                .then((data) => {
                    const select = document.getElementById(selectId);
                    data.forEach((persona) => {
                        const option = document.createElement("option");
                        option.value = persona.Pk_NumeroEmpleado;
                        option.textContent = `${persona.Nombre}- ${persona.Pk_NumeroEmpleado}`;
                        select.appendChild(option);
                    });

                    // Establecer el valor seleccionado después de llenar el select
                    if (valorSeleccionado) {
                        select.value = valorSeleccionado;
                    }
                })
                .catch((error) => console.error(`Error fetching ${selectId} data:`, error));
        }

        document.addEventListener("DOMContentLoaded", () => {
            // Llamada inicial para cargar los datos
            actualizarActivos();
            cargarDatosInicialesActivo();
            const activosForm = document.getElementById("activosForm");
            const confirmSubmitButton = document.getElementById("confirmSubmitActivos");
            let isSubmitting = false;

            confirmSubmitButton.addEventListener("click", () => {
                if (isSubmitting) return; // Evita envíos duplicados
                isSubmitting = true;

                confirmModal.hide();
                const formData = new FormData(activosForm); // Captura los datos del formulario

                fetch("/INFORMATICA/src/Models/Activos/guardar_activo.php", {
                        method: "POST",
                        body: formData,
                    })
                    .then((response) => {
                        // Verifica que la respuesta sea JSON válida
                        if (!response.ok) {
                            throw new Error("Error en la solicitud");
                        }
                        return response.json();
                    })
                    .then((data) => {
                        if (data.success) {
                            Swal.fire({
                                title: "¡Éxito!",
                                text: "Datos del activo guardados exitosamente.",
                                icon: "success",
                                timer: 3000,
                                showConfirmButton: false,
                            });
                            resetForm();
                            actualizarActivos();
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch((error) => {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: error.message, // Muestra el mensaje del error específico
                        });
                    })
                    .finally(() => {
                        isSubmitting = false;
                    });
            });
        });
    </script>