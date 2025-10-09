<div class="container">
    <h3 class="text-center">Formato de No Adeudo</h3>
    <hr>
    <form id="formatoNoAdeudo" class="row g-3 needs-validation text-center" autocomplete="off" enctype="multipart/form-data" novalidate>

        <div class="col-md-6">
            <label class="form-label" for="PersonalSolicitante">Personal solicitante</label>
            <select class="form-select text-center" name="PersonalSolicitante" id="PersonalSolicitante" required>
                <option disabled selected value="" class="text-center">Selecciona un empleado</option>
            </select>
            <div class="invalid-feedback">
                Selecciona un empleado válido
            </div>
        </div>

        <div class="col-md-6">
            <label for="FechaSeparacion" class="form-label">Separación del cargo</label>
            <input class="form-control text-center" type="date" id="FechaSeparacion" name="FechaSeparacion" required>
        </div>

        <div class="col-md-4">
            <label for="Oficio" class="form-label">Oficio</label>
            <input class="form-control text-center" type="text" id="Oficio" name="Oficio" maxlength="250" required>
        </div>
        <div class="col-md-4">
            <label for="Constancia" class="form-label">Constancia #</label>
            <input class="form-control text-center"
                type="text"
                id="Constancia"
                name="Constancia"
                required
                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
        </div>
         <div class="col-md-4">
            <label for="OficioTI" class="form-label">Oficio JUDTI</label>
            <input class="form-control text-center"
                type="text"
                id="OficioTI"
                name="OficioTI"
                required
                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="confirmSubmit">Guardar</button>
        </div>
    </form>
</div>

<!-- Tabla de constancias -->
<div class="container mt-4">
    <h3 class="text-center">Constancias registradas</h3>
    <hr>
    <table class="table table-bordered text-center" id="tablaConstancias">
        <thead>
            <tr>
                <th>Constancia #</th>
                <th>Empleado</th>
                <th>Fecha Separación</th>
                <th>Oficio</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

<script>
    // VALIDACIÓN BOOTSTRAP
    (() => {
        "use strict";

        // Obtener todos los formularios a los que queremos aplicar estilos de validación de Bootstrap
        const forms = document.querySelectorAll(".needs-validation");

        // Iterar sobre ellos y prevenir el envío si hay campos inválidos
        Array.from(forms).forEach((form) => {
            form.addEventListener(
                "submit",
                (event) => {
                    if (!form.checkValidity()) {
                        event.preventDefault(); // Prevenir el envío del formulario
                        event.stopPropagation(); // Detener la propagación del evento
                    }

                    // Agregar la clase 'was-validated' al formulario
                    form.classList.add("was-validated");
                },
                false
            );
        });
    })();

    document.addEventListener("DOMContentLoaded", function() {
        //OFICIO AUTOMÁTICO
        const year = new Date().getFullYear();
        const oficioBase = "INVEACDMX/DG/DAF/CRMASG/XXXX/";
        document.getElementById("Oficio").value = oficioBase + year;

        // Carga de datos de personal en los selects
        function cargarDatosIniciales() {
            fetch("./src/Models/Personal/obtener_personal.php")
                .then((response) => response.json())
                .then((data) => {
                    llenarSelect(data, "PersonalSolicitante");
                })
                .catch((error) => console.error("Error fetching personal data:", error));
        }

        function llenarSelect(
            data,
            selectId,
            valueKey = "Pk_NumeroEmpleado",
            textKey = "NombreCompleto"
        ) {
            const select = document.getElementById(selectId);
            data.forEach((item) => {
                const option = document.createElement("option");
                option.value = item[valueKey];
                option.textContent = `${item[textKey]}-${item[valueKey]}`;
                select.appendChild(option);
            });
        }

        cargarDatosIniciales();
        cargarConstancias();
    });

    // ENVÍO DEL FORMULARIO POR FETCH
    const form = document.getElementById("formatoNoAdeudo");
    form.addEventListener("submit", function(event) {
        event.preventDefault();
        event.stopPropagation();

        if (form.checkValidity()) {
            const formData = new FormData(form);

            fetch("/INFORMATICA/src/Models/Personal/guardar_constancia.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    Swal.fire({
                        title: "¡Éxito!",
                        text: "Datos de la constancia guardados correctamente.",
                        icon: "success",
                        timer: 3000, // Duración en milisegundos (3 segundos)
                        showConfirmButton: false, // No mostrar botón de aceptar
                    });
                    form.reset();
                    form.classList.remove("was-validated");
                    // Reiniciar el valor del oficio con el año actual
                    const year = new Date().getFullYear();
                    const oficioBase = "INVEACDMX/DG/DAF/CRMASG/XXXX/";
                    document.getElementById("Oficio").value = oficioBase + year;
                    cargarConstancias();
                })
                .catch(error => {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: error, // Aquí se pasa el mensaje del error
                    });
                });
        }

        form.classList.add("was-validated");
    });

    // Función para cargar y mostrar las constancias en la tabla
    function cargarConstancias() {
        fetch("/INFORMATICA/src/Models/Personal/obtener_constancias.php")
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector("#tablaConstancias tbody");
                tbody.innerHTML = "";
                data.forEach(row => {
                    const tr = document.createElement("tr");    
                    tr.innerHTML = `
                    <td><a href="/INFORMATICA/src/Models/Personal/formato_no_adeudo.php?IDConstancia=${row.Pk_IDConstancia}" target="_blank">${row.Pk_IDConstancia || ""}</a></td>
                    <td>${row.Nombre || ""}</td>
                    <td>${row.FechaSeparacion || ""}</td>
                    <td>${row.Oficio || ""}</td>
                `;
                    tbody.appendChild(tr);
                });
            });
    }
</script>