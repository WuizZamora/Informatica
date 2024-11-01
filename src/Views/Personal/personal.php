<div class="container text-center">
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
                <input type="text" class="form-control text-center" id="RFCEmpleado" name="RFCEmpleado" maxlength="15" required>
            </div>
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
            <div class="col-md-4">
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
                    mostrarDatosEnModalPersonal(); // Llenamos el modal con los datos del formulario
                    confirmModalPersonal.show(); // Mostramos el modal
                }
                form.classList.add("was-validated");
            }, false);
        });
    })();

    const confirmModalPersonal = new bootstrap.Modal(
        document.getElementById("confirmModalPersonal")
    );
    const formDataReview = document.getElementById("formDataReviewPersonal");

    // Función para mostrar los datos del formulario en el modal
    function mostrarDatosEnModalPersonal() {
        formDataReview.innerHTML = ""; // Limpiamos cualquier contenido previo
        const formData = new FormData(personalForm);

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
        const personalForm = document.getElementById("personalForm");
        personalForm.reset(); // Restablece todos los campos del formulario
        personalForm.classList.remove("was-validated"); // Remueve la clase de validación
        cargarDatosIniciales();
    }

    document.addEventListener("DOMContentLoaded", function() {
        cargarDatosIniciales();
        const personalForm = document.getElementById("personalForm");
        const confirmSubmitButton = document.getElementById("confirmSubmitPersonal");
        let isSubmitting = false;

        confirmSubmitButton.addEventListener("click", () => {
            if (isSubmitting) return; // Evita envíos duplicados
            isSubmitting = true;

            confirmModalPersonal.hide();
            const formData = new FormData(personalForm); // Captura los datos del formulario

            fetch("/INFORMATICA/src/Models/Personal/guardar_personal.php", {
                    method: "POST",
                    body: formData,
                })
                .then((response) => response.json())
                .then((data) => {
                    // Verifica si la respuesta fue exitosa
                    if (data.success) {
                        Swal.fire({
                            title: "¡Éxito!",
                            text: "Datos del personal guardados exitosamente.",
                            icon: "success",
                            timer: 3000, // Duración en milisegundos (3 segundos)
                            showConfirmButton: false, // No mostrar botón de aceptar
                        });
                        resetForm(); // Resetea el formulario
                        obtenerPersonal(); // Obtiene el personal actualizado
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: data.message, // Aquí se pasa el mensaje del error
                        });
                    }
                })
                .catch((error) => {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Error al enviar los datos", // Aquí se pasa el mensaje del error
                    });
                })
                .finally(() => {
                    isSubmitting = false; // Restablece el estado de envío
                });
        });
    });

    function cargarDatosIniciales() {
        fetch("./src/Models/Personal/obtener_plaza.php")
            .then((response) => response.json())
            .then((data) => {
                llenarSelect(data, "PlazaEmpleado");
            })
            .catch((error) => console.error("Error fetching personal data:", error));
    }

    // Función para llenar el select con los datos
    function llenarSelect(data, selectId, valueKey = "Pk_IDPlaza", textKey = "Puesto") {
        const select = document.getElementById(selectId);

        // Limpiar opciones previas
        select.innerHTML = '<option disabled selected value="" class="text-center">Selecciona una plaza</option>';

        // Añadir opciones actualizadas
        data.forEach((item) => {
            const option = document.createElement("option");
            option.value = item[valueKey];
            option.textContent = item[textKey];
            select.appendChild(option);
        });
    }

    const userRole = <?php echo json_encode($rol); ?>; // Pasar el rol como variable JavaScript
    let paginaActual = 1;
    const registrosPorPagina = 10; // Número de registros por página
    let datosPersonal = []; // Almacena los datos de personal

    // Función para obtener los datos de personal
    function obtenerPersonal() {
        fetch('./src/Models/Personal/consultar_personal.php')
            .then(respuesta => {
                if (!respuesta.ok) {
                    throw new Error('Error en la consulta de personal: ' + respuesta.statusText);
                }
                return respuesta.json(); // Convertir la respuesta a JSON
            })
            .then(datos => {
                datosPersonal = datos; // Guardar todos los datos
                mostrarPagina(paginaActual); // Mostrar la página actual
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('tablaPersonal').innerHTML =
                    '<tr><td colspan="3" class="text-danger">Error al cargar los datos de personal.</td></tr>';
            });
    }

    // Función para filtrar los datos de personal
    function filtrarPersonal() {
        const filtro = document.getElementById('filtroPersonal').value.toLowerCase();
        const datosFiltrados = datosPersonal.filter(persona =>
            persona.Pk_NumeroEmpleado.toString().toLowerCase().includes(filtro) ||
            persona.Nombre.toLowerCase().includes(filtro)
        );
        mostrarPaginaConDatos(datosFiltrados);
    }

    // Función para mostrar la lista de personal en la página actual
    function mostrarPagina(pagina) {
        const tablaBody = document.getElementById('tablaPersonal');
        tablaBody.innerHTML = ''; // Limpiar contenido anterior

        // Calcular el inicio y fin de los datos para la página actual
        const inicio = (pagina - 1) * registrosPorPagina;
        const fin = inicio + registrosPorPagina;
        const datosPaginados = datosPersonal.slice(inicio, fin);

        // Rellenar las filas con los datos
        datosPaginados.forEach(persona => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${persona.Pk_NumeroEmpleado}</td>
                <td>${persona.Nombre}</td>
                <td>${persona.RFC}</td>
                <td> 
                ${userRole == 1 || userRole == 3  ? `<button class="btn btn-primary" onclick="editPersonal(${persona.Pk_NumeroEmpleado})">Editar</button>` : ""}           
                </td>
            `;
            tablaBody.appendChild(fila);
        });

        generarNavegacion();
    }

    // Función para mostrar datos filtrados
    function mostrarPaginaConDatos(datos) {
        const tablaBody = document.getElementById('tablaPersonal');
        tablaBody.innerHTML = ''; // Limpiar contenido anterior

        // Calcular el total de páginas con los datos filtrados
        const totalPaginas = Math.ceil(datos.length / registrosPorPagina);
        if (totalPaginas === 0) {
            tablaBody.innerHTML = '<tr><td colspan="5" class="text-danger">No se encontraron resultados.</td></tr>';
            return;
        }

        const inicio = (paginaActual - 1) * registrosPorPagina;
        const fin = inicio + registrosPorPagina;
        const datosPaginados = datos.slice(inicio, fin);

        // Rellenar las filas con los datos filtrados
        datosPaginados.forEach(persona => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${persona.Pk_NumeroEmpleado}</td>
                <td>${persona.Nombre}</td>
                <td>${persona.RFC}</td>
                <td> 
                ${userRole == 1 || userRole == 3  ? `<button class="btn btn-primary" onclick="editPersonal(${persona.Pk_NumeroEmpleado})">Editar</button>` : ""}           
                </td>
            `;
            tablaBody.appendChild(fila);
        });

        generarNavegacionConDatos(datos.length);
    }

    // Función para generar la navegación de páginas
    function generarNavegacion() {
        const navegacionDiv = document.getElementById('navegacionPaginas');
        navegacionDiv.innerHTML = ''; // Limpiar contenido anterior

        const totalPaginas = Math.ceil(datosPersonal.length / registrosPorPagina);
        const lista = document.createElement('ul');
        lista.className = 'pagination justify-content-center';

        if (paginaActual > 1) {
            lista.appendChild(crearElementoPaginacion('Anterior', paginaActual - 1));
        }

        const rangoVisible = 3;
        let inicioPagina = Math.max(1, paginaActual - rangoVisible);
        let finPagina = Math.min(totalPaginas, paginaActual + rangoVisible);

        for (let i = inicioPagina; i <= finPagina; i++) {
            lista.appendChild(crearElementoPaginacion(i, i, i === paginaActual));
        }

        if (finPagina < totalPaginas) {
            lista.appendChild(crearElementoInactivo('...'));
        }

        if (paginaActual < totalPaginas) {
            lista.appendChild(crearElementoPaginacion('Siguiente', paginaActual + 1));
        }

        navegacionDiv.appendChild(lista);
    }

    // Función para generar la navegación con datos filtrados
    function generarNavegacionConDatos(totalDatos) {
        const navegacionDiv = document.getElementById('navegacionPaginas');
        navegacionDiv.innerHTML = ''; // Limpiar contenido anterior

        const totalPaginas = Math.ceil(totalDatos / registrosPorPagina);
        const lista = document.createElement('ul');
        lista.className = 'pagination justify-content-center';

        if (paginaActual > 1) {
            lista.appendChild(crearElementoPaginacion('Anterior', paginaActual - 1));
        }

        const rangoVisible = 3;
        let inicioPagina = Math.max(1, paginaActual - rangoVisible);
        let finPagina = Math.min(totalPaginas, paginaActual + rangoVisible);

        for (let i = inicioPagina; i <= finPagina; i++) {
            lista.appendChild(crearElementoPaginacion(i, i, i === paginaActual));
        }

        if (finPagina < totalPaginas) {
            lista.appendChild(crearElementoInactivo('...'));
        }

        if (paginaActual < totalPaginas) {
            lista.appendChild(crearElementoPaginacion('Siguiente', paginaActual + 1));
        }

        navegacionDiv.appendChild(lista);
    }

    // Crear un elemento de paginación activo
    function crearElementoPaginacion(texto, pagina, activo = false) {
        const elemento = document.createElement('li');
        elemento.className = `page-item ${activo ? 'active' : ''}`;
        elemento.innerHTML = `<a class="page-link" href="#" onclick="cambiarPagina(${pagina})">${texto}</a>`;
        return elemento;
    }

    // Crear un elemento inactivo
    function crearElementoInactivo(texto) {
        const elemento = document.createElement('li');
        elemento.className = 'page-item disabled';
        elemento.innerHTML = `<span class="page-link">${texto}</span>`;
        return elemento;
    }

    // Cambiar de página
    function cambiarPagina(pagina) {
        paginaActual = pagina;
        filtrarPersonal(); // Aplicar el filtro al cambiar de página
    }

    // Cargar los datos de personal al iniciar
    obtenerPersonal();

    function editPersonal(id) {
        fetch(`/INFORMATICA/src/Models/Personal/obtener_personal_detalles.php?NumeroEmpleado=${id}`)
            .then((response) => response.json())
            .then((data) => {
                if (data.error) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: data.error,
                    });
                } else {
                    // Construir el contenido del modal con el campo Plaza como select
                    let modalContent = `
                        <strong># ${data.Pk_NumeroEmpleado}</strong>
                        <div class="form-group">
                            <label for="NombreUpdate">Nombre:</label>
                            <input class="form-control text-center" id="NombreUpdate" name="NombreUpdate" value="${data.Nombre}">
                        </div>
                        <div class="form-group">
                            <label for="RFCUpdate">RFC:</label>
                            <input class="form-control text-center" id="RFCUpdate" name="RFCUpdate" value="${data.RFC}">
                        </div>
                        <div class="form-group">
                            <label for="PlazaUpdate">Plaza:</label>
                            <select class="form-select text-center" id="PlazaUpdate" name="PlazaUpdate">
                                <option disabled selected value="" class="text-center">Selecciona una plaza</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="FechaInicialUpdate">Fecha inicial:</label>
                            <input type="date" class="form-control text-center" id="FechaInicialUpdate" name="FechaInicialUpdate" value="${data.FechaInicial}">
                        </div>
                        <div class="form-group">
                            <label for="EstatusUpdate">Estatus:</label>
                            <select class="form-select text-center" id="EstatusUpdate" name="EstatusUpdate">
                                <option disabled value="">Selecciona el estatus del personal</option>
                                <option value="1" ${data.Estatus == 1 ? 'selected' : ''}>Vigente</option>
                                <option value="0" ${data.Estatus == 0 ? 'selected' : ''}>No Vigente</option>
                            </select>
                        </div>
                    `;

                    // Mostrar contenido en el modal
                    document.getElementById("modalBodyPersonal").innerHTML = modalContent;

                    let myModalPersonal = new bootstrap.Modal(document.getElementById("editModalPersonal"));
                    myModalPersonal.show();

                    // Llenar el select con las opciones de plazas
                    fetch("./src/Models/Personal/obtener_plaza.php?todas=true") // Agregar el parámetro
                        .then((response) => response.json())
                        .then((plazas) => {
                            llenarSelect(plazas, "PlazaUpdate");
                            // Seleccionar la plaza actual del empleado después de llenar el select
                            document.getElementById("PlazaUpdate").value = data.Fk_IDPlaza_Plaza; // Asegúrate que este valor coincide con los valores en el select
                        })
                        .catch((error) => console.error("Error fetching plazas data:", error));


                    document.getElementById("saveButtonPersonal").onclick = function() {
                        // Obtener los valores de los campos
                        const numeroEmpleado = data.Pk_NumeroEmpleado;
                        const nombre = document.getElementById("NombreUpdate").value;
                        const rfc = document.getElementById("RFCUpdate").value;
                        const plaza = document.getElementById("PlazaUpdate").value;
                        const fechaInicial = document.getElementById("FechaInicialUpdate").value;
                        const estatusUpdate = document.getElementById("EstatusUpdate").value;

                        // Inicializar un objeto para almacenar los datos del servicio
                        const datosPersonal = {
                            numeroEmpleado,
                            nombre,
                            rfc,
                            plaza,
                            fechaInicial,
                            estatusUpdate
                        };

                        // Enviar los datos al backend
                        fetch("/INFORMATICA/src/Models/Personal/actualizar_personal.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                },
                                body: JSON.stringify(datosPersonal), // Convierte los datos a JSON
                            })
                            .then(async (response) => {
                                const text = await response.text();
                                try {
                                    const result = JSON.parse(text);
                                    if (result.success) {
                                        Swal.fire({
                                            title: "¡Éxito!",
                                            text: "Datos del personal actualizados exitosamente.",
                                            icon: "success",
                                            timer: 3000,
                                            showConfirmButton: false,
                                        });
                                        cargarDatosIniciales();
                                        myModalPersonal.hide();
                                        obtenerPersonal();
                                    } else {
                                        Swal.fire({
                                            icon: "error",
                                            title: "Oops...",
                                            text: result.error,
                                        });
                                    }
                                } catch (error) {
                                    console.error("Respuesta inválida del servidor:", text);
                                    alert("Error en la respuesta del servidor.");
                                }
                            })
                            .catch((error) => {
                                console.error("Error al guardar al personal:", error);
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "Error al guardar al personal",
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
</script>