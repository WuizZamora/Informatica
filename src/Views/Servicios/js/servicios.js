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

document.addEventListener("DOMContentLoaded", function () {
  let isSubmitting = false; // Evita envíos múltiples

  const servicioForm = document.getElementById("servicioForm");
  const confirmModal = new bootstrap.Modal(
    document.getElementById("confirmModal")
  );
  const mensaje = document.getElementById("mensaje");

  // Validación y envío del formulario
  servicioForm.addEventListener("submit", function (event) {
    event.preventDefault();
    console.log("Validando formulario...");

    let isValid = true;
    const invalidFields = [];
    const inputs = servicioForm.querySelectorAll("input, select, textarea");

    inputs.forEach((input) => {
      const isHidden = input.offsetParent === null; // Detecta si el campo está oculto

      if (!isHidden) {
        if (input.id === "ServicioSolicitado") {
          const selectedOptions = Array.from(input.selectedOptions).map(
            (option) => option.value
          );
          if (selectedOptions.length === 0) {
            isValid = false;
            invalidFields.push(input.id);
          }
        } else if (!input.checkValidity()) {
          isValid = false;
          invalidFields.push(input.id);
        }
      }
    });

    if (!isValid) {
      console.log("Formulario inválido:", invalidFields);
      servicioForm.classList.add("was-validated");
    } else {
      fillFormDataReview();
      confirmModal.show();
    }
  });

  function fillFormDataReview() {
    const formDataReview = document.getElementById("formDataReview");
    let formDataHtml = "";

    const inputs = servicioForm.querySelectorAll(
      'input:not([type="hidden"]), select, textarea'
    );
    inputs.forEach((input) => {
      const label =
        document.querySelector(`label[for='${input.id}']`)?.textContent ||
        input.name;
      const value = input.value;
      if (value) {
        formDataHtml += `<tr><td>${label}</td><td>${value}</td></tr>`;
      }
    });

    formDataReview.innerHTML = formDataHtml;
  }

  document
    .getElementById("confirmSubmit")
    .addEventListener("click", function () {
      if (!isSubmitting) {
        isSubmitting = true;
        confirmModal.hide();
        submitForm();
      }
    });

  function submitForm() {
    console.log("Formulario válido, enviando...");

    const formData = new FormData(servicioForm);
    const selectedOptions = Array.from(
      document.getElementById("ServicioSolicitado").selectedOptions
    ).map((option) => option.value);

    selectedOptions.forEach((option) =>
      formData.append("ServicioSolicitado[]", option)
    );

    fetch("/INFORMATICA/src/Models/Servicios/guardar_servicio.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        mensaje.textContent = data.message;
        mensaje.style.display = "block";

        resetForm(); // Llamar al reseteo completo
        setTimeout(() => {
          mensaje.style.display = "none";
        }, 5000);

        actualizarServicios();
        isSubmitting = false;
      })
      .catch((error) => {
        console.error("Error:", error);
        mensaje.textContent = "Error al enviar los datos.";
        mensaje.style.display = "block";
        isSubmitting = false;
      });
  }

  // Función para resetear el formulario completamente
  function resetForm() {
    servicioForm.reset(); // Resetea el formulario principal
    servicioForm.classList.remove("was-validated");

    // Ocultar y limpiar todos los formularios dinámicos
    const dynamicForms = ["formIncidencia", "formVideos", "formDictaminacion"];
    dynamicForms.forEach((formId) => {
      const form = document.getElementById(formId);
      form.style.display = "none";
      form.querySelectorAll("input, select, textarea").forEach((field) => {
        field.value = "";
        field.required = false;
      });
    });

    // Resetear selects a su opción por defecto
    document.querySelectorAll("select").forEach((select) => {
      select.selectedIndex = 0;
    });

    // Ocultar campos adicionales como Oficio y Confirmación
    document.getElementById("ConfirmacionCampoOficio").style.display = "none";
    document.getElementById("CampoOficio").style.display = "none";
    document.getElementById("Oficio").value = "";
  }

  // Carga de datos en los selects
  function cargarDatosIniciales() {
    fetch("./src/Models/Personal/obtener_personal.php")
      .then((response) => response.json())
      .then((data) => {
        llenarSelect(data, "PersonalSolicitante");
        llenarSelect(data, "PersonalEntrega");
      })
      .catch((error) => console.error("Error fetching personal data:", error));

    fetch("./src/Models/Personal/obtener_personal.php?filtrar=true")
      .then((response) => response.json())
      .then((data) => {
        llenarSelect(data, "PersonalAtiende");
      })
      .catch((error) => console.error("Error fetching atiende data:", error));

    fetch("./src/Models/Activos/obtener_activos.php")
      .then((response) => response.json())
      .then((data) => {
        const selectCABMS = document.getElementById("CABMSDictaminacion");
        llenarSelect(data, "CABMSDictaminacion", "CABMS", "Descripcion");

        selectCABMS.addEventListener("change", (event) => {
          fetchProgresivos(event.target.value);
        });
      })
      .catch((error) => console.error("Error fetching activos data:", error));
  }

  function llenarSelect(
    data,
    selectId,
    valueKey = "Pk_NumeroEmpleado",
    textKey = "Nombre"
  ) {
    const select = document.getElementById(selectId);
    data.forEach((item) => {
      const option = document.createElement("option");
      option.value = item[valueKey];
      option.textContent = `${item[valueKey]} - ${item[textKey]}`;
      select.appendChild(option);
    });
  }

  function fetchProgresivos(cabms) {
    fetch(`./src/Models/Activos/obtener_progresivo.php?cabms=${cabms}`)
      .then((response) => response.json())
      .then((data) => {
        const selectProgresivo = document.getElementById(
          "ProgresivoDictaminacion"
        );
        selectProgresivo.innerHTML = "";

        const defaultOption = document.createElement("option");
        defaultOption.value = "";
        defaultOption.textContent = "Selecciona un progresivo";
        defaultOption.selected = true;
        defaultOption.disabled = true;
        selectProgresivo.appendChild(defaultOption);

        data.forEach((activos) => {
          const option = document.createElement("option");
          option.value = activos.Progresivo;
          option.textContent = activos.Progresivo;
          selectProgresivo.appendChild(option);
        });
      })
      .catch((error) =>
        console.error("Error fetching progresivos data:", error)
      );
  }

  // Inicializar carga de datos
  cargarDatosIniciales();
});

// SELECCION DE SERVICIO CAMPOS DINAMICOS
function mostrarFormulario() {
  var tipoServicio = document.getElementById("IDTipoServicio").value;

  // Ocultar todos los formularios y limpiar campos
  const incidendenciaFields = document.querySelectorAll(
    "#formIncidencia input, #formIncidencia textarea, #formIncidencia select"
  );
  const videoFields = document.querySelectorAll(
    "#formVideos input, #formVideos textarea, #formVideos select"
  );
  const dictaminacionFields = document.querySelectorAll(
    "#formDictaminacion input, #formDictaminacion textarea, #formDictaminacion select"
  );

  // Ocultar todos los formularios
  document.getElementById("formIncidencia").style.display = "none";
  document.getElementById("formVideos").style.display = "none";
  document.getElementById("formDictaminacion").style.display = "none";

  // Limpiar valores de todos los campos y quitar el atributo "required"
  incidendenciaFields.forEach((field) => {
    field.value = ""; // Limpiar valores
    field.required = false; // Quitar el atributo required
  });

  videoFields.forEach((field) => {
    field.value = "";
    field.required = false;
  });

  dictaminacionFields.forEach((field) => {
    field.value = "";
    field.required = false;
  });

  // Limpiar y ocultar el campo confirmacion del Oficio
  const campoConfirmacionOficio = document.getElementById(
    "ConfirmacionCampoOficio"
  );
  campoConfirmacionOficio.value = ""; // Limpiar el valor del campo confirmación Oficio
  campoConfirmacionOficio.style.display = "none"; // Ocultar el campo confirmación Oficio

  // Limpiar y reiniciar el campo Oficio
  const campoOficio = document.getElementById("CampoOficio");
  const inputOficio = document.getElementById("Oficio");
  inputOficio.value = ""; // Limpiar el valor del campo Oficio
  inputOficio.removeAttribute("readonly"); // Quitar readonly para asegurarse de que sea editable
  inputOficio.removeAttribute("required"); // Reiniciar el campo a no requerido
  campoOficio.style.display = "none"; // Ocultar el campo Oficio

  // Mostrar el formulario correspondiente
  if (tipoServicio === "INCIDENCIA") {
    document.getElementById("formIncidencia").style.display = "block";
    campoConfirmacionOficio.style.display = "block"; // Mostrar el campo confirmacion Oficio
    incidendenciaFields.forEach((field) => (field.required = true)); // Hacer los campos requeridos
    campoConfirmacionOficio.required = true; // Hacer el campo confirmación del oficio requerido

    // Hacer que ServicioSolicitado sea requerido solo si es INCIDENCIA
    const servicioSolicitadoSelect =
      document.getElementById("ServicioSolicitado");
    servicioSolicitadoSelect.required = true; // Hacer que el select múltiple sea requerido
  } else {
    // Asegúrate de quitar el requisito del select cuando no sea INCIDENCIA
    const servicioSolicitadoSelect =
      document.getElementById("ServicioSolicitado");
    servicioSolicitadoSelect.required = false; // Quitar el requisito del select
  }

  // Manejo para otros tipos de servicios
  if (tipoServicio === "ENTREGA MATERIAL FÍLMICO") {
    document.getElementById("formVideos").style.display = "block";
    campoOficio.style.display = "block"; // Mostrar el campo Oficio
    videoFields.forEach((field) => (field.required = true));
    inputOficio.removeAttribute("readonly"); // Permitir la edición del campo Oficio
    inputOficio.setAttribute("required", "required"); // Hacer el campo requerido
  } else if (tipoServicio === "TÉCNICO") {
    document.getElementById("formDictaminacion").style.display = "block";
    campoOficio.style.display = "block"; // Mostrar el campo Oficio
    dictaminacionFields.forEach((field) => (field.required = true));
    inputOficio.removeAttribute("readonly"); // Permitir la edición del campo Oficio
    inputOficio.setAttribute("required", "required"); // Hacer el campo requerido
  }
}

//CONFIRMACION DE EXISTENCIA DE SERVICIO
function manejarOficio(valorSeleccionado) {
  var campoOficio = document.getElementById("CampoOficio");
  var inputOficio = document.getElementById("Oficio");

  if (valorSeleccionado === "SI") {
    campoOficio.style.display = "block"; // Mostrar el campo de oficio
    inputOficio.value = ""; // Limpiar el campo
    inputOficio.setAttribute("required", "required"); // Hacer el campo requerido
    inputOficio.removeAttribute("readonly"); // Quitar la propiedad readonly si estaba antes
  } else {
    campoOficio.style.display = "none"; // Ocultar el campo de oficio
    inputOficio.removeAttribute("required"); // Eliminar el atributo requerido
    inputOficio.value = "S/O"; // VALOR PREDETERMINADO
    inputOficio.setAttribute("readonly", "readonly"); // Agregar la propiedad readonly
  }
}

// FUNCIÓN PARA TENER LA FECHA Y HORA ACTUAL
function actualizarFechaHora() {
  const fechaActual = new Date();

  const year = fechaActual.getFullYear();
  const month = (fechaActual.getMonth() + 1).toString().padStart(2, "0");
  const day = fechaActual.getDate().toString().padStart(2, "0");
  const hours = fechaActual.getHours().toString().padStart(2, "0");
  const minutes = fechaActual.getMinutes().toString().padStart(2, "0");

  const fechaLocal = `${year}-${month}-${day}T${hours}:${minutes}`;

  document.getElementById("FechaAtencion").value = fechaLocal;
}

setInterval(actualizarFechaHora, 1000);

let currentPage = 1;
const recordsPerPage = 10;
let totalPages = 1;

function actualizarServicios() {
  fetch("./src/Models/Servicios/obtener_servicios.php")
    .then((response) => response.json())
    .then((data) => {
      totalPages = Math.ceil(data.length / recordsPerPage);
      renderTable(data, currentPage);
      renderPagination(totalPages);
    })
    .catch((error) => console.error("Error:", error));
}

function renderTable(data, page) {
  const serviciosBody = document.getElementById("serviciosBody");
  serviciosBody.innerHTML = "";
  const start = (page - 1) * recordsPerPage;
  const end = start + recordsPerPage;
  const paginatedData = data.slice(start, end);

  paginatedData.forEach((servicio) => {
    // Aquí creas la fila de la tabla
    const row = `
            <tr>
                <td>${servicio.Pk_IDServicio}</td>
                <td>${servicio.Solicitante}</td>
                <td>${servicio.Atiende}</td>
                <td>${servicio.FechaSolicitud}</td>
                <td style="word-break: break-word; white-space: normal;">${servicio.Oficio
      }</td>
                <td>${servicio.FechaAtencion}</td>
                <td>${servicio.TipoServicio}</td>
                <td>${servicio.EstadoSolicitud}</td>
                <td>
                  ${servicio.SoporteDocumental 
                    ? `<a href="/INFORMATICA/src/Models/Servicios/${servicio.SoporteDocumental}" target="_blank">
                        <i class="bi bi-file-earmark-text text-primary" style="font-size: 1.5rem;"></i>
                      </a>`
                    : `<i class="bi bi-file-earmark-text text-muted" style="font-size: 1.5rem; opacity: 0.5;" title="Sin información"></i>`
                  }
                </td>
                <td>
                ${userRole == 1 ||
        userRole == 2 ||
        userRole == 3 ||
        userRole == 4
        ? `<a href="/INFORMATICA/src/Models/Servicios/generar_PDF.php?IDServicio=${servicio.Pk_IDServicio}" target="_blank" class="btn btn-success">Ver</a>`
        : ""
      }              
                    ${userRole == 1 || userRole == 3
        ? `<button class="btn btn-primary" onclick="editServicio(${servicio.Pk_IDServicio})">Editar</button>`
        : ""
      }
                    ${userRole == 1
        ? `<button class="btn btn-warning" onclick="EstadoSolicitud(${servicio.Pk_IDServicio})">Estado</button>`
        : ""
      }
                </td>
            </tr>
        `;

    serviciosBody.innerHTML += row;
  });
}

function renderPagination(totalPages) {
  const pagination = document.getElementById("pagination");
  pagination.innerHTML = "";

  // Botón de página anterior
  pagination.innerHTML += `
    <li class="page-item ${currentPage === 1 ? "disabled" : ""}">
      <a class="page-link" href="#" onclick="changePage(${currentPage - 1
    })">Anterior</a>
    </li>
  `;

  // Determinar el rango de páginas a mostrar
  const maxVisiblePages = 5; // Número máximo de páginas visibles
  const startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
  const endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

  // Ajustar el rango si estamos cerca de los extremos
  if (endPage - startPage < maxVisiblePages - 1) {
    if (startPage === 1) {
      endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
    } else if (endPage === totalPages) {
      startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
  }

  // Botones de páginas
  for (let i = startPage; i <= endPage; i++) {
    pagination.innerHTML += `
      <li class="page-item ${i === currentPage ? "active" : ""}">
        <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
      </li>
    `;
  }

  // Botón para ir a la primera página si no estamos en ella
  if (startPage > 1) {
    pagination.innerHTML =
      `
      <li class="page-item">
        <a class="page-link" href="#" onclick="changePage(1)">1</a>
      </li>
      <li class="page-item">
        <span class="page-link">...</span>
      </li>
    ` + pagination.innerHTML;
  }

  // Botón para ir a la última página si no estamos en ella
  if (endPage < totalPages) {
    pagination.innerHTML += `
      <li class="page-item">
        <span class="page-link">...</span>
      </li>
      <li class="page-item">
        <a class="page-link" href="#" onclick="changePage(${totalPages})">${totalPages}</a>
      </li>
    `;
  }

  // Botón de página siguiente
  pagination.innerHTML += `
    <li class="page-item ${currentPage === totalPages ? "disabled" : ""}">
      <a class="page-link" href="#" onclick="changePage(${currentPage + 1
    })">Siguiente</a>
    </li>
  `;
}

function changePage(page) {
  if (page < 1 || page > totalPages) return;
  currentPage = page;
  actualizarServicios();
}

// Actualizar la tabla cada 10 segundos
setInterval(actualizarServicios, 10000);

// Llamar la función por primera vez para mostrar los datos iniciales
actualizarServicios();

function editServicio(id) {
  fetch(
    `/INFORMATICA/src/Models/Servicios/obtener_servicio_detalles.php?IDServicio=${id}`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.error) {
        alert(data.error);
      } else {
        // Construir el contenido del modal
        let modalContent = `
        <strong># ${data.Pk_IDServicio}</strong>
          <div class="form-group">
              <label for="solicitante">Solicitante:</label>
              <select class="form-select" id="solicitante" name="solicitante" required>
                <option disabled selected value="">Selecciona un empleado</option>
              </select>
          </div>
          <div class="form-group">
              <label for="entrega">Entrega:</label>
              <select class="form-select" id="entrega" name="entrega" required>
                <option disabled selected value="">Selecciona un empleado</option>
              </select>
          </div>
          <div class="form-group">
              <label for="atiende">Atiende:</label>
              <select class="form-select" id="atiende" name="atiende" required>
                <option disabled selected value="">Selecciona un empleado</option>
              </select>
          </div>
          <div class="form-group">
              <label for="tipoServicio">Tipo de servicio:</label>
              <input type="text" class="form-control text-center" id="tipoServicio" value="${data.TipoServicio}" readonly>
          </div>
          <div class="form-group">
              <label for="OficioUpdate">Oficio:</label>
              <input type="text" class="form-control" id="OficioUpdate" value="${data.Oficio}">
          </div>
          <div class="form-group">
              <label for="fechaSolicitud">Fecha de solicitud:</label>
              <input type="date" class="form-control" id="fechaSolicitud" value="${data.FechaSolicitud}">
          </div>
          <div id="camposAdicionales"></div>
        `;

        // Mostrar contenido en el modal
        document.getElementById("modalBody").innerHTML = modalContent;

        // Llenar los selects de solicitante, entrega y atiende
        llenarSelectPersonal(
          "./src/Models/Personal/obtener_personal.php",
          "solicitante",
          data.Solicitante
        ).then(() => {
          const solicitanteSelect = document.getElementById("solicitante");
          solicitanteSelect.value = data.Solicitante; // Establecer el valor seleccionado aquí
        });

        llenarSelectPersonal(
          "./src/Models/Personal/obtener_personal.php",
          "entrega",
          data.Entrega
        ).then(() => {
          const entregaSelect = document.getElementById("entrega");
          entregaSelect.value = data.Entrega; // Establecer el valor seleccionado aquí
        });

        llenarSelectPersonal(
          "./src/Models/Personal/obtener_personal.php?filtrar=true",
          "atiende",
          data.Atiende
        ).then(() => {
          const atiendeSelect = document.getElementById("atiende");
          atiendeSelect.value = data.Atiende; // Establecer el valor seleccionado aquí
        });

        // Establecer el valor del select de tipo de servicio
        const tipoServicioSelect = document.getElementById("tipoServicio");
        // Llamar a la función para mostrar campos adicionales
        mostrarCamposAdicionales(tipoServicioSelect.value, data);

        tipoServicioSelect.addEventListener("change", function () {
          mostrarCamposAdicionales(this.value, data);
        });
        let myModal = new bootstrap.Modal(document.getElementById("editModal"));
        myModal.show();

        document.getElementById("saveButton").onclick = function () {
          // Obtener los valores de los campos
          const idServicio = data.Pk_IDServicio;
          const solicitante = document.getElementById("solicitante").value;
          const entrega = document.getElementById("entrega").value;
          const atiende = document.getElementById("atiende").value;
          const oficio = document.getElementById("OficioUpdate").value;
          const fechaSolicitud =
            document.getElementById("fechaSolicitud").value;
          const tipoServicio = document.getElementById("tipoServicio").value;

          // Inicializar un objeto para almacenar los datos del servicio
          const datosServicio = {
            idServicio,
            solicitante,
            entrega,
            atiende,
            oficio,
            fechaSolicitud,
            tipoServicio,
          };

          // Agregar datos específicos según el tipo de servicio
          if (tipoServicio === "TÉCNICO") {
            const IDActivo = document.getElementById("cabms_Tecnico").value;
            const DescripcionTecnico = document.getElementById(
              "descripcionTecnico_Tecnico"
            ).value;
            const EvaluacionTecnico =
              document.getElementById("evaluacion_Tecnico").value;

            datosServicio.IDActivo = IDActivo;
            datosServicio.DescripcionTecnico = DescripcionTecnico;
            datosServicio.EvaluacionTecnico = EvaluacionTecnico;
          } else if (tipoServicio === "INCIDENCIA") {
            const selectElement = document.getElementById(
              "ServicioSolicitadoUPDATE"
            );
            const selectedOptionsUPDATE = Array.from(
              selectElement.selectedOptions
            )
              .map((option) => option.value)
              .join(", "); // Unir los valores seleccionados con comas

            const DescripcionIncidencia = document.getElementById(
              "descripcionIncidencia_Incidencia"
            ).value;
            const ObservacionesIncidencia = document.getElementById(
              "observaciones_Incidencia"
            ).value;

            datosServicio.ServicioSolicitado = selectedOptionsUPDATE;
            datosServicio.DescripcionIncidencia = DescripcionIncidencia;
            datosServicio.ObservacionesIncidencia = ObservacionesIncidencia;
          } else if (tipoServicio === "ENTREGA MATERIAL FÍLMICO") {
            const CantidadVideos =
              document.getElementById("cantidadVideos").value;
            const PIVideos = document.getElementById(
              "periodoInicial_Videos"
            ).value;
            const PFVideos = document.getElementById(
              "periodoFinal_Videos"
            ).value;
            const PVideos = document.getElementById("periodo_Videos").value;
            const Equipo = document.getElementById("equipo_Videos").value;
            const DescripcionVideos = document.getElementById(
              "DescripcionVideosUpdate"
            ).value;

            datosServicio.CantidadVideos = CantidadVideos;
            datosServicio.PIVideos = PIVideos;
            datosServicio.PFVideos = PFVideos;
            datosServicio.PVideos = PVideos;
            datosServicio.Equipo = Equipo;
            datosServicio.DescripcionVideos = DescripcionVideos;
          }

          // Enviar los datos al backend
          fetch("/INFORMATICA/src/Models/Servicios/actualizar_servicio.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify(datosServicio), // Convierte los datos a JSON
          })
            .then(async (response) => {
              const text = await response.text(); // Lee la respuesta como texto
              try {
                const result = JSON.parse(text); // Intenta parsear como JSON
                if (result.success) {
                  alert("Datos del servicio guardados exitosamente.");
                  myModal.hide(); // Cierra el modal si estás usando uno
                } else {
                  alert(
                    result.error || "Ocurrió un error al guardar los datos."
                  );
                }
              } catch (error) {
                console.error("Respuesta inválida del servidor:", text); // Muestra el contenido
                alert("Error en la respuesta del servidor.");
              }
            })
            .catch((error) => {
              console.error("Error al guardar el servicio:", error);
              alert("Ocurrió un error al guardar los datos.");
            });
        };
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Ocurrió un error al cargar los datos.");
    });
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
        option.textContent = `${persona.Pk_NumeroEmpleado} - ${persona.Nombre}`;
        select.appendChild(option);
      });

      // Establecer el valor seleccionado después de llenar el select
      if (valorSeleccionado) {
        select.value = valorSeleccionado;
      }
    })
    .catch((error) => console.error(`Error fetching ${selectId} data:`, error));
}

function mostrarCamposAdicionales(tipoServicio, data) {
  const camposAdicionalesDiv = document.getElementById("camposAdicionales");
  camposAdicionalesDiv.innerHTML = ""; // Limpiar campos adicionales

  if (tipoServicio === "ENTREGA MATERIAL FÍLMICO") {
    camposAdicionalesDiv.innerHTML = `
    <hr>
      <div class="form-group">
          <label for="cantidadVideos">Cantidad de Videos:</label>
          <input type="number" class="form-control" id="cantidadVideos" value="${data.CantidadVideos}">
      </div>
      <div class="form-group">
          <label for="periodoInicial_Videos">Periodo Inicial:</label>
          <input type="date" class="form-control" id="periodoInicial_Videos" value="${data.PeriodoInicial}">
      </div>
      <div class="form-group">
          <label for="periodoFinal_Videos">Periodo Final:</label>
          <input type="date" class="form-control" id="periodoFinal_Videos" value="${data.PeriodoFinal}">
      </div>
      <div class="form-group">
          <label for="periodo_Videos">Periodo:</label>
          <input type="text" class="form-control" id="periodo_Videos" value="${data.Periodo}">
      </div>
      <div class="form-group">
          <label for="equipo_Videos">Equipo:</label>
          <input type="text" class="form-control" id="equipo_Videos" value="${data.Equipo}">
      </div>
      <div class="form-group">
          <label for="DescripcionVideosUpdate">Descripcion de los Videos:</label>
          <textarea class="form-control" id="DescripcionVideosUpdate">${data.DescripcionVideo}</textarea>
      </div>
    `;
  } else if (tipoServicio === "TÉCNICO") {
    camposAdicionalesDiv.innerHTML = `
    <hr>
      <div class="form-group">
          <label for="cabms_Tecnico">ID ACTIVO:</label>
          <input type="text" class="form-control" id="cabms_Tecnico" value="${data.Fk_IDActivo_Activos
      }">
      </div>
      <div class="form-group">
          <label for="descripcionTecnico_Tecnico">Descripción:</label>
          <textarea class="form-control" id="descripcionTecnico_Tecnico">${data.DescripcionTecnico
      }</textarea>
      </div>
      <div class="form-group">
          <label for="evaluacion_Tecnico">Evaluación:</label>
          <select class="form-select" id="evaluacion_Tecnico" name="evaluacion_Tecnico" required>
              <option selected disabled value="">Elige una opción</option>
              <option value="FUNCIONAL" ${data.Evaluacion === "FUNCIONAL" ? "selected" : ""
      }>Funcional</option>
              <option value="NO FUNCIONAL" ${data.Evaluacion === "NO FUNCIONAL" ? "selected" : ""
      }>Baja</option>
          </select>
      </div>
    `;
  } else if (tipoServicio === "INCIDENCIA") {
    // Suponiendo que data.ServicioSolicitado es una cadena concatenada
    const serviciosSolicitados = data.ServicioSolicitado.split(", ").map(
      (servicio) => servicio.trim()
    );

    camposAdicionalesDiv.innerHTML = `
    <hr>
      <div class="form-group">
          <label for="ServicioSolicitadoUPDATE">Servicio Solicitado:</label>
          <select class="form-select text-center" id="ServicioSolicitadoUPDATE" name="ServicioSolicitadoUPDATE[]" size="9" multiple>
              <option value="">Elige una opción</option>
              <option value="GESTIÓN DE EQUIPOS" ${serviciosSolicitados.includes("GESTIÓN DE EQUIPOS")
        ? "selected"
        : ""
      }>GESTIÓN DE EQUIPOS</option>
              <option value="CONECTIVIDAD" ${serviciosSolicitados.includes("CONECTIVIDAD") ? "selected" : ""
      }>CONECTIVIDAD</option>
              <option value="GESTIÓN DE USUARIOS" ${serviciosSolicitados.includes("GESTIÓN DE USUARIOS")
        ? "selected"
        : ""
      }>GESTIÓN DE USUARIOS</option>
              <option value="CAPACITACIÓN Y ASESORÍA" ${serviciosSolicitados.includes("CAPACITACIÓN Y ASESORÍA")
        ? "selected"
        : ""
      }>CAPACITACIÓN Y ASESORÍA</option>
              <option value="OTROS" ${serviciosSolicitados.includes("OTROS") ? "selected" : ""
      }>OTROS</option>
          </select>
          <div class="invalid-feedback">
              Ingresa una opción
          </div>
      </div>
      <div class="form-group">
          <label for="descripcionIncidencia_Incidencia">Descripción:</label>
          <textarea class="form-control" id="descripcionIncidencia_Incidencia">${data.DescripcionIncidencia
      }</textarea>
      </div>
      <div class="form-group">
          <label for="observaciones_Incidencia">Observaciones:</label>
          <textarea class="form-control" id="observaciones_Incidencia">${data.Observaciones
      }</textarea>
      </div>
    `;
  }
}

function EstadoSolicitud(id) {
  fetch(
    `/INFORMATICA/src/Models/Servicios/consultar_estado_solicitud.php?IDServicio=${id}`
  )
    .then((response) => {
      if (!response.ok) {
        throw new Error("Error en la solicitud");
      }
      return response.json();
    })
    .then((data) => {
      if (data.error) {
        alert(data.error);
      } else {
        mostrarDatos(data);
      }
    })
    .catch((error) => {
      alert(error.message);
    });

  function mostrarDatos(servicio) {
    const { Pk_IDServicio, EstadoSolicitud, SoporteDocumental } = servicio;
    let ModalContentEstado = `
      <form id="servicioForm" class="needs-validation" enctype="multipart/form-data">
          <div class="row text-center">
              <div class="col-md-4">
                  <label for="idServicioEstado" class="form-label"><strong>ID Servicio:</strong></label>
                  <input type="text" class="form-control" id="idServicioEstado" name="Pk_IDServicio" value="${Pk_IDServicio}" readonly>
              </div>
              <div class="col-md-4">
                  <label for="estadoSolicitud" class="form-label"><strong>Estado de Solicitud:</strong></label>
                  <select class="form-select" id="estadoSolicitud" name="EstadoSolicitud" required>
                      <option selected disabled value="">Elige una opción</option>
                      <option value="COMPLETADO" ${EstadoSolicitud === "COMPLETADO" ? "selected" : ""
      }>COMPLETADO</option>
                      <option value="PENDIENTE" ${EstadoSolicitud === "PENDIENTE" ? "selected" : ""
      }>PENDIENTE</option>
                      <option value="CANCELADO" ${EstadoSolicitud === "CANCELADO" ? "selected" : ""
      }>CANCELADO</option>
                  </select>
                  <div class="invalid-feedback">Por favor, selecciona un estado.</div>
              </div>
              <div class="col-md-4">
                  <label class="form-label"><strong>Soporte Documental:</strong></label>
                  ${SoporteDocumental
        ? `
                      <br><a href="/INFORMATICA/src/Models/Servicios/${SoporteDocumental}" target="_blank" class="btn btn-link">Ver documento</a>
                  `
        : `
                      <input type="file" class="form-control" id="soporteDocumental" name="SoporteDocumental" required>
                      <div class="invalid-feedback">Por favor, sube un documento.</div>
                  `
      }
              </div>
          </div>  
      </form>
    `;
    // Mostrar contenido en el modal
    document.getElementById("resultadoModal").innerHTML = ModalContentEstado;

    let myModalEstado = new bootstrap.Modal(
      document.getElementById("servicioModal")
    );
    myModalEstado.show();
    document.getElementById("saveButtonEstado").onclick = function () {
      // Obtener los valores de los campos
      const idServicio = document.getElementById("idServicioEstado").value;
      const estadoSolicitud = document.getElementById("estadoSolicitud").value;
      // Verificar si el campo 'soporteDocumental' existe antes de acceder a su valor
      const soporteDocumentalInput =
        document.getElementById("soporteDocumental");
      const soporteDocumental = soporteDocumentalInput
        ? soporteDocumentalInput.files[0]
        : null;

      // Inicializar un objeto FormData para almacenar los datos del servicio
      const formData = new FormData();
      formData.append("Pk_IDServicio", idServicio);
      formData.append("EstadoSolicitud", estadoSolicitud);

      // Solo agregar soporteDocumental si existe
      if (soporteDocumental) {
        formData.append("SoporteDocumental", soporteDocumental);
      }
      // Enviar los datos al backend usando fetch
      fetch("/INFORMATICA/src/Models/Servicios/actualizar_estado_solicitud.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Error en la solicitud al servidor");
          }
          return response.json();
        })
        .then((data) => {
          // Manejar la respuesta del servidor
          if (data.error) {
            alert(data.error);
          } else {
            alert("Estado de solicitud actualizado exitosamente");
            // Aquí puedes cerrar el modal o actualizar la vista
            myModalEstado.hide();
          }
        })
        .catch((error) => {
          alert(error.message);
        });
    };
  }
}
