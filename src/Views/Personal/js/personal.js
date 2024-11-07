document.addEventListener("DOMContentLoaded", function () {
    obtenerPersonal();
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
                            mostrarDatosEnModalPersonal(); // Llenamos el modal con los datos del formulario
                            confirmModalPersonal.show(); // Mostramos el modal
                        }
                        form.classList.add("was-validated");
                    },
                    false
                );
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

        cargarDatosIniciales();
        const personalForm = document.getElementById("personalForm");
        const confirmSubmitButton = document.getElementById(
            "confirmSubmitPersonal"
        );
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
                if (data.success) {
                    Swal.fire({
                        title: "¡Éxito!",
                        text: "Datos del personal guardados exitosamente.",
                        icon: "success",
                        timer: 3000,
                        showConfirmButton: false,
                    });
                    resetForm(); 
                    obtenerPersonal(); 
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: data.message, // Muestra el mensaje de error específico
                    });
                }
            })
            .catch((error) => {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Error al enviar los datos", 
                });
            })
            .finally(() => {
                isSubmitting = false; 
            });
            
        });
    }
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
function llenarSelect(
    data,
    selectId,
    valueKey = "Pk_IDPlaza",
    textKey = "Puesto"
) {
    const select = document.getElementById(selectId);

    // Limpiar opciones previas
    select.innerHTML =
        '<option disabled selected value="" class="text-center">Selecciona una plaza</option>';

    // Añadir opciones actualizadas
    data.forEach((item) => {
        const option = document.createElement("option");
        option.value = item[valueKey];
        option.textContent = item[textKey];
        select.appendChild(option);
    });
}

let paginaActual = 1;
const registrosPorPagina = 10; // Número de registros por página
let datosPersonal = []; // Almacena los datos de personal

// Función para obtener los datos de personal
function obtenerPersonal() {
    fetch("./src/Models/Personal/consultar_personal.php")
        .then((respuesta) => {
            if (!respuesta.ok) {
                throw new Error(
                    "Error en la consulta de personal: " + respuesta.statusText
                );
            }
            return respuesta.json(); // Convertir la respuesta a JSON
        })
        .then((datos) => {
            datosPersonal = datos; // Guardar todos los datos
            mostrarPagina(paginaActual); // Mostrar la página actual
        })
        .catch((error) => {
            console.error("Error:", error);
            document.getElementById("tablaPersonal").innerHTML =
                '<tr><td colspan="3" class="text-danger">Error al cargar los datos de personal.</td></tr>';
        });
}

// Función para filtrar los datos de personal
function filtrarPersonal() {
    const filtro = document.getElementById("filtroPersonal").value.toLowerCase();
    const datosFiltrados = datosPersonal.filter(
        (persona) =>
            persona.Pk_NumeroEmpleado.toString().toLowerCase().includes(filtro) ||
            persona.NombreCompleto.toLowerCase().includes(filtro)
    );
    mostrarPaginaConDatos(datosFiltrados);
}

// Función para mostrar la lista de personal en la página actual
function mostrarPagina(pagina) {
    const tablaBody = document.getElementById("tablaPersonal");
    tablaBody.innerHTML = ""; // Limpiar contenido anterior

    // Calcular el inicio y fin de los datos para la página actual
    const inicio = (pagina - 1) * registrosPorPagina;
    const fin = inicio + registrosPorPagina;
    const datosPaginados = datosPersonal.slice(inicio, fin);

    // Rellenar las filas con los datos
    datosPaginados.forEach((persona) => {
        const fila = document.createElement("tr");
        fila.innerHTML = `
            <td>${persona.Pk_NumeroEmpleado}</td>
            <td>${persona.NombreCompleto}</td>
            <td>${persona.RFC}</td>
            <td> 
            ${userRole == 1 || userRole == 3
                ? `<button class="btn btn-primary" onclick="editPersonal(${persona.Pk_NumeroEmpleado})">Editar</button>`
                : ""
            }           
            </td>
        `;
        tablaBody.appendChild(fila);
    });

    generarNavegacion();
}

// Función para mostrar datos filtrados
function mostrarPaginaConDatos(datos) {
    const tablaBody = document.getElementById("tablaPersonal");
    tablaBody.innerHTML = ""; // Limpiar contenido anterior

    // Calcular el total de páginas con los datos filtrados
    const totalPaginas = Math.ceil(datos.length / registrosPorPagina);
    if (totalPaginas === 0) {
        tablaBody.innerHTML =
            '<tr><td colspan="5" class="text-danger">No se encontraron resultados.</td></tr>';
        return;
    }

    const inicio = (paginaActual - 1) * registrosPorPagina;
    const fin = inicio + registrosPorPagina;
    const datosPaginados = datos.slice(inicio, fin);

    // Rellenar las filas con los datos filtrados
    datosPaginados.forEach((persona) => {
        const fila = document.createElement("tr");
        fila.innerHTML = `
            <td>${persona.Pk_NumeroEmpleado}</td>
            <td>${persona.NombreCompleto}</td>
            <td>${persona.RFC}</td>
            <td> 
            ${userRole == 1 || userRole == 3
                ? `<button class="btn btn-primary" onclick="editPersonal(${persona.Pk_NumeroEmpleado})">Editar</button>`
                : ""
            }           
            </td>
        `;
        tablaBody.appendChild(fila);
    });

    generarNavegacionConDatos(datos.length);
}

// Función para generar la navegación de páginas
function generarNavegacion() {
    const navegacionDiv = document.getElementById("navegacionPaginas");
    navegacionDiv.innerHTML = ""; // Limpiar contenido anterior

    const totalPaginas = Math.ceil(datosPersonal.length / registrosPorPagina);
    const lista = document.createElement("ul");
    lista.className = "pagination justify-content-center";

    if (paginaActual > 1) {
        lista.appendChild(crearElementoPaginacion("Anterior", paginaActual - 1));
    }

    const rangoVisible = 3;
    let inicioPagina = Math.max(1, paginaActual - rangoVisible);
    let finPagina = Math.min(totalPaginas, paginaActual + rangoVisible);

    for (let i = inicioPagina; i <= finPagina; i++) {
        lista.appendChild(crearElementoPaginacion(i, i, i === paginaActual));
    }

    if (finPagina < totalPaginas) {
        lista.appendChild(crearElementoInactivo("..."));
    }

    if (paginaActual < totalPaginas) {
        lista.appendChild(crearElementoPaginacion("Siguiente", paginaActual + 1));
    }

    navegacionDiv.appendChild(lista);
}

// Función para generar la navegación con datos filtrados
function generarNavegacionConDatos(totalDatos) {
    const navegacionDiv = document.getElementById("navegacionPaginas");
    navegacionDiv.innerHTML = ""; // Limpiar contenido anterior

    const totalPaginas = Math.ceil(totalDatos / registrosPorPagina);
    const lista = document.createElement("ul");
    lista.className = "pagination justify-content-center";

    if (paginaActual > 1) {
        lista.appendChild(crearElementoPaginacion("Anterior", paginaActual - 1));
    }

    const rangoVisible = 3;
    let inicioPagina = Math.max(1, paginaActual - rangoVisible);
    let finPagina = Math.min(totalPaginas, paginaActual + rangoVisible);

    for (let i = inicioPagina; i <= finPagina; i++) {
        lista.appendChild(crearElementoPaginacion(i, i, i === paginaActual));
    }

    if (finPagina < totalPaginas) {
        lista.appendChild(crearElementoInactivo("..."));
    }

    if (paginaActual < totalPaginas) {
        lista.appendChild(crearElementoPaginacion("Siguiente", paginaActual + 1));
    }

    navegacionDiv.appendChild(lista);
}

// Crear un elemento de paginación activo
function crearElementoPaginacion(texto, pagina, activo = false) {
    const elemento = document.createElement("li");
    elemento.className = `page-item ${activo ? "active" : ""}`;
    elemento.innerHTML = `<a class="page-link" href="#" onclick="cambiarPagina(${pagina})">${texto}</a>`;
    return elemento;
}

// Crear un elemento inactivo
function crearElementoInactivo(texto) {
    const elemento = document.createElement("li");
    elemento.className = "page-item disabled";
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
    fetch(
        `/INFORMATICA/src/Models/Personal/obtener_personal_detalles.php?NumeroEmpleado=${id}`
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
                // Construir el contenido del modal con el campo Plaza como select
                let modalContent = `
                    <strong># ${data.Pk_NumeroEmpleado}</strong>
                    <div class="form-group">
                        <label for="PrimerApellidoUpdate">Primer apellido:</label>
                        <input class="form-control text-center" id="PrimerApellidoUpdate" name="PrimerApellidoUpdate" value="${data.PrimerApellido}">
                    </div>
                    <div class="form-group">
                        <label for="SegundoApellidoUpdate">Segundo apellido:</label>
                        <input class="form-control text-center" id="SegundoApellidoUpdate" name="SegundoApellidoUpdate" value="${data.SegundoApellido}">
                    </div>
                    <div class="form-group">
                        <label for="NombreUpdate">Nombre:</label>
                        <input class="form-control text-center" id="NombreUpdate" name="NombreUpdate" value="${data.Nombres}">
                    </div>
                    <div class="form-group">
                        <label for="RFCUpdate">RFC:</label>
                        <input class="form-control text-center" id="RFCUpdate" name="RFCUpdate" value="${data.RFC
                    }">
                    </div>
                    <div class="form-group">
                        <label for="PlazaUpdate">Plaza:</label>
                        <select class="form-select text-center" id="PlazaUpdate" name="PlazaUpdate">
                            <option disabled selected value="" class="text-center">Selecciona una plaza</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="FechaInicialUpdate">Fecha inicial:</label>
                        <input type="date" class="form-control text-center" id="FechaInicialUpdate" name="FechaInicialUpdate" value="${data.FechaInicial
                    }">
                    </div>
                    <div class="form-group">
                        <label for="EstatusUpdate">Estatus:</label>
                        <select class="form-select text-center" id="EstatusUpdate" name="EstatusUpdate">
                            <option disabled value="">Selecciona el estatus del personal</option>
                            <option value="1" ${data.Estatus == 1 ? "selected" : ""
                    }>Vigente</option>
                            <option value="0" ${data.Estatus == 0 ? "selected" : ""
                    }>No Vigente</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="UsuarioUpdate">Usuario:</label>
                        <input type="text" class="form-control text-center" id="UsuarioUpdate" name="UsuarioUpdate" value="${data.Usuario}">
                    </div>
                    <div class="form-group">
                        <label for="PassUpdate">Pass:</label>
                        <input type="text" class="form-control text-center" id="PassUpdate" name="PassUpdate" value="${data.Pass}">
                    </div>
                `;

                // Mostrar contenido en el modal
                document.getElementById("modalBodyPersonal").innerHTML = modalContent;

                let myModalPersonal = new bootstrap.Modal(
                    document.getElementById("editModalPersonal")
                );
                myModalPersonal.show();

                // Llenar el select con las opciones de plazas
                fetch("./src/Models/Personal/obtener_plaza.php?todas=true") // Agregar el parámetro
                    .then((response) => response.json())
                    .then((plazas) => {
                        llenarSelect(plazas, "PlazaUpdate");
                        // Seleccionar la plaza actual del empleado después de llenar el select
                        document.getElementById("PlazaUpdate").value =
                            data.Fk_IDPlaza_Plaza; // Asegúrate que este valor coincide con los valores en el select
                    })
                    .catch((error) =>
                        console.error("Error fetching plazas data:", error)
                    );

                document.getElementById("saveButtonPersonal").onclick = function () {
                    // Obtener los valores de los campos
                    const numeroEmpleado = data.Pk_NumeroEmpleado;
                    const primerApellido = document.getElementById("PrimerApellidoUpdate").value;
                    const segundoApellido = document.getElementById("SegundoApellidoUpdate").value;
                    const nombre = document.getElementById("NombreUpdate").value;
                    const rfc = document.getElementById("RFCUpdate").value;
                    const plaza = document.getElementById("PlazaUpdate").value;
                    const fechaInicial =
                        document.getElementById("FechaInicialUpdate").value;
                    const estatusUpdate = document.getElementById("EstatusUpdate").value;
                    const usuarioUpdate = document.getElementById("UsuarioUpdate").value;
                    const passUpdate = document.getElementById("PassUpdate").value;

                    // Inicializar un objeto para almacenar los datos del servicio
                    const datosPersonal = {
                        numeroEmpleado,
                        primerApellido, 
                        segundoApellido,
                        nombre,
                        rfc,
                        plaza,
                        fechaInicial,
                        estatusUpdate,
                        usuarioUpdate,
                        passUpdate
                    };

                    // Enviar los datos al backend
                    fetch("/INFORMATICA/src/Models/Personal/actualizar_personal.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify(datosPersonal), // Convierte los datos a JSON
                    })
                        .then((response) => response.json()) // Analiza la respuesta directamente como JSON
                        .then((result) => {
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
