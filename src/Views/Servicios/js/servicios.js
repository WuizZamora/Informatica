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

$(document).ready(function () {
  let isSubmitting = false; // Variable para evitar envíos múltiples

  $("#servicioForm").on("submit", function (event) {
    event.preventDefault();

    console.log("Validando formulario...");
    let isValid = true;
    const invalidFields = [];

    // Recorre todos los campos de entrada del formulario
    $(this)
      .find(":input")
      .each(function () {
        if ($(this).attr("id") === "ServicioSolicitado") {
          // Validate multiple selections
          const selectedOptions = $(this).val();
          if (selectedOptions.length === 0) {
            isValid = false;
            invalidFields.push(this.id); // Guarda el ID del campo inválido
          } else {
            $.each(selectedOptions, function (index, value) {
              if (!value) {
                isValid = false;
                invalidFields.push(this.id); // Guarda el ID del campo inválido
              }
            });
          }
        } else if (!this.checkValidity()) {
          isValid = false;
          invalidFields.push(this.id); // Guarda el ID del campo inválido
        }
      });

    if (!isValid) {
      console.log("Formulario inválido");
      console.log("Campos inválidos:", invalidFields);
      event.stopPropagation(); // Detener la propagación si hay campos inválidos
      $(this).addClass("was-validated"); // Asegúrate de agregar la clase aquí
    } else {
      // Mostrar modal de confirmación si es válido
      $("#confirmModal").modal("show");

      // Llenar el modal con los datos del formulario antes de mostrarlo
      fillFormDataReview();
    }
  });

  // Función para llenar la tabla de revisión en el modal
  function fillFormDataReview() {
    let formDataHtml = ""; // Inicializa una cadena para los datos

    // Recorre cada campo del formulario y crea filas en la tabla
    $("#servicioForm")
      .find(":input:not([type='hidden'])") // Excluye campos ocultos
      .each(function () {
        let label = $("label[for='" + this.id + "']").text() || this.name; // Intenta obtener la etiqueta
        let value = $(this).val(); // Captura el valor del campo

        // Si el campo tiene algún valor, mostrarlo
        if (value) {
          formDataHtml += `
            <tr>
              <td>${label}</td>
              <td>${value}</td>
            </tr>
          `;
        }
      });

    // Colocar los datos en el cuerpo de la tabla
    $("#formDataReview").html(formDataHtml);
  }

  // Registrar el evento del botón de confirmación una vez
  $("#confirmSubmit").on("click", function () {
    if (!isSubmitting) {
      // Solo permite el envío si no está en proceso
      isSubmitting = true; // Marcar como en proceso para evitar duplicaciones
      $("#confirmModal").modal("hide"); // Cierra el modal
      submitForm(); // Llama la función que envía el formulario
    }
  });

  // Función que envía el formulario por AJAX
  function submitForm() {
    console.log("Formulario válido, enviando...");

    const formData = new FormData($("#servicioForm")[0]); // Captura todos los datos del formulario, incluyendo archivos

    // Obtener los valores seleccionados del select múltiple
    const selectedOptions = $("#ServicioSolicitado").val();

    // Si hay opciones seleccionadas, agrégalas a formData
    if (selectedOptions) {
      selectedOptions.forEach(function (option) {
        formData.append("ServicioSolicitado[]", option); // Usa el mismo nombre con [] para almacenar múltiples valores
      });
    }

    $.ajax({
      url: "/INFORMATICA/src/Models/Servicios/guardar_servicio.php", // Cambia a la ruta correcta
      type: "POST",
      data: formData,
      contentType: false, // No establecer el tipo de contenido
      processData: false, // No procesar los datos
      dataType: "json",
      success: function (response) {
        $("#mensaje").text(response.message).show(); // Muestra el mensaje
        // Limpia los mensajes de error de Bootstrap
        const invalidFeedbacks = document.querySelectorAll(
          ".was-validated .invalid-feedback"
        );
        invalidFeedbacks.forEach((feedback) => {
          feedback.style.display = "none"; // Oculta todos los mensajes de error
        });

        // Resetea el formulario
        $("#servicioForm")[0].reset();
        $("#servicioForm").removeClass("was-validated");

        // Oculta todos los formularios dinámicos
        $("#formIncidencia, #formVideos, #formDictaminacion").hide();

        // Oculta el mensaje después de 5 segundos
        setTimeout(() => {
          $("#mensaje").hide();
        }, 5000);

        actualizarServicios(); // Actualiza la lista de servicios

        isSubmitting = false; // Restablecer el estado después del éxito
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error("Error:", textStatus, errorThrown);
        console.error("Respuesta del servidor:", jqXHR.responseText); // Añadir esto para ver la respuesta completa
        $("#mensaje").text("Error al enviar los datos.").show();

        isSubmitting = false; // Restablecer el estado en caso de error
      },
    });
  }
});

// SELECCION DE SERVICIO CAMPOS DINAMICOS
function mostrarFormulario() {
  var tipoServicio = document.getElementById("IDTipoServicio").value;

  // Ocultar todos los formularios y limpiar campos
  const incidendenciaFields = document.querySelectorAll(
    "#formIncidencia input, #formIncidencia textarea, #formIncidencia select, #formIncidencia checkbox"
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
  } else if (tipoServicio === "ENTREGA MATERIAL FÍLMICO") {
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
                <td style="word-break: break-word; white-space: normal;">${
                  servicio.Oficio
                }</td>
                <td>${servicio.FechaAtencion}</td>
                <td>${servicio.TipoServicio}</td>
                <td>
                ${
                  userRole == 1 ||
                  userRole == 2 ||
                  userRole == 3 ||
                  userRole == 4
                    ? `<a href="/INFORMATICA/src/Models/Servicios/generar_PDF.php?IDServicio=${servicio.Pk_IDServicio}" target="_blank" class="btn btn-success">VER</a>`
                    : ""
                }              
                    ${
                      userRole == 1 || userRole == 3
                        ? `<button class="btn btn-primary" onclick="editServicio(${servicio.Pk_IDServicio})">Editar</button>`
                        : ""
                    }
                    ${
                      userRole == 1
                        ? `<button class="btn btn-dark" onclick="deleteServicio(${servicio.Pk_IDServicio})">Eliminar</button>`
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
      <a class="page-link" href="#" onclick="changePage(${
        currentPage - 1
      })">Anterior</a>
    </li>
  `;

  // Botones de páginas
  for (let i = 1; i <= totalPages; i++) {
    pagination.innerHTML += `
      <li class="page-item ${i === currentPage ? "active" : ""}">
        <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
      </li>
    `;
  }

  // Botón de página siguiente
  pagination.innerHTML += `
    <li class="page-item ${currentPage === totalPages ? "disabled" : ""}">
      <a class="page-link" href="#" onclick="changePage(${
        currentPage + 1
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
              <label for="Oficio">Oficio:</label>
              <input type="text" class="form-control" id="Oficio" value="${data.Oficio}">
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

        document
          .getElementById("saveButton")
          .addEventListener("click", function () {
            actualizarServicio(id);
          });

        $("#editModal").modal("show");
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
          <label for="DescripcionVideos">Descripcion de los Videos:</label>
          <textarea class="form-control" id="DescripcionVideos">${data.DescripcionVideo}</textarea>
      </div>
    `;
  } else if (tipoServicio === "TÉCNICO") {
    camposAdicionalesDiv.innerHTML = `
    <hr>
      <div class="form-group">
          <label for="cabms_Tecnico">ID ACTIVO:</label>
          <input type="text" class="form-control" id="cabms_Tecnico" value="${
            data.Fk_IDActivo_Activos
          }">
      </div>
      <div class="form-group">
          <label for="descripcionTecnico_Tecnico">Descripción:</label>
          <textarea class="form-control" id="descripcionTecnico">${
            data.DescripcionTecnico
          }</textarea>
      </div>
      <div class="form-group">
          <label for="evaluacion_Tecnico">Evaluación:</label>
          <select class="form-select" id="evaluacion_Tecnico" name="evaluacion_Tecnico" required>
              <option selected disabled value="">Elige una opción</option>
              <option value="FUNCIONAL" ${
                data.Evaluacion === "FUNCIONAL" ? "selected" : ""
              }>Funcional</option>
              <option value="NO FUNCIONAL" ${
                data.Evaluacion === "NO FUNCIONAL" ? "selected" : ""
              }>Baja</option>
          </select>
      </div>
    `;
  } else if (tipoServicio === "INCIDENCIA") {
    // Suponiendo que data.ServicioSolicitado es una cadena concatenada
    const serviciosSolicitados = data.ServicioSolicitado.split(", ").map(servicio => servicio.trim());

    camposAdicionalesDiv.innerHTML = `
    <hr>
      <div class="form-group">
          <label for="ServicioSolicitado">Servicio Solicitado:</label>
          <select class="form-select text-center" id="ServicioSolicitado" name="ServicioSolicitado[]" size="9" multiple>
              <option value="">Elige una opción</option>
              <option value="GESTIÓN DE EQUIPOS" ${serviciosSolicitados.includes("GESTIÓN DE EQUIPOS") ? "selected" : ""}>GESTIÓN DE EQUIPOS</option>
              <option value="CONECTIVIDAD" ${serviciosSolicitados.includes("CONECTIVIDAD") ? "selected" : ""}>CONECTIVIDAD</option>
              <option value="GESTIÓN DE USUARIOS" ${serviciosSolicitados.includes("GESTIÓN DE USUARIOS") ? "selected" : ""}>GESTIÓN DE USUARIOS</option>
              <option value="CAPACITACIÓN Y ASESORÍA" ${serviciosSolicitados.includes("CAPACITACIÓN Y ASESORÍA") ? "selected" : ""}>CAPACITACIÓN Y ASESORÍA</option>
              <option value="OTROS" ${serviciosSolicitados.includes("OTROS") ? "selected" : ""}>OTROS</option>
          </select>
          <div class="invalid-feedback">
              Ingresa una opción
          </div>
      </div>
      <div class="form-group">
          <label for="descripcionIncidencia_Incidencia">Descripción:</label>
          <textarea class="form-control" id="descripcionIncidencia_Incidencia">${data.DescripcionIncidencia}</textarea>
      </div>
      <div class="form-group">
          <label for="observaciones_Incidencia">Observaciones:</label>
          <textarea class="form-control" id="observaciones_Incidencia">${data.Observaciones}</textarea>
      </div>
    `;
}
}

function actualizarServicio(id) {
  const solicitante = document.getElementById("solicitante").value;
  const entrega = document.getElementById("entrega").value;
  const atiende = document.getElementById("atiende").value;
  const tipoServicio = document.getElementById("tipoServicio").value;
  const oficio = document.getElementById("Oficio").value;
  const fechaSolicitud = document.getElementById("fechaSolicitud").value;
  const cantidadVideos =
    tipoServicio === "ENTREGA MATERIAL FÍLMICO"
      ? document.getElementById("cantidadVideos").value
      : null;
  const periodoInicial_Videos =
    tipoServicio === "ENTREGA MATERIAL FÍLMICO"
      ? document.getElementById("periodoInicial_Videos").value
      : null;
  const periodoFinal_Videos =
    tipoServicio === "ENTREGA MATERIAL FÍLMICO"
      ? document.getElementById("periodoFinal_Videos").value
      : null;
  const periodo_Videos =
    tipoServicio === "ENTREGA MATERIAL FÍLMICO"
      ? document.getElementById("periodo_Videos").value
      : null;
  const equipo_Videos =
    tipoServicio === "ENTREGA MATERIAL FÍLMICO"
      ? document.getElementById("equipo_Videos").value
      : null;
  const cabms_Tecnico =
    tipoServicio === "TÉCNICO"
      ? document.getElementById("cabms_Tecnico").value
      : null;
  const progresivo_Tecnico =
    tipoServicio === "TÉCNICO"
      ? document.getElementById("progresivo_Tecnico").value
      : null;
  const descripcionTecnico =
    tipoServicio === "TÉCNICO"
      ? document.getElementById("descripcionTecnico").value
      : null;
  const evaluacion_Tecnico =
    tipoServicio === "TÉCNICO"
      ? document.getElementById("evaluacion_Tecnico").value
      : null;
  const servicioSolicitado_Incidencia =
    tipoServicio === "INCIDENCIA"
      ? document.getElementById("servicioSolicitado_Incidencia").value
      : null;
  const descripcionIncidencia_Incidencia =
    tipoServicio === "INCIDENCIA"
      ? document.getElementById("descripcionIncidencia").value
      : null;
  const observaciones_Incidencia =
    tipoServicio === "INCIDENCIA"
      ? document.getElementById("observaciones_Incidencia").value
      : null;

  // Crear objeto con los datos para enviar
  const data = {
    id,
    solicitante,
    entrega,
    atiende,
    tipoServicio,
    oficio,
    fechaSolicitud,
    cantidadVideos,
    periodoInicial_Videos,
    periodoFinal_Videos,
    periodo_Videos,
    equipo_Videos,
    cabms_Tecnico,
    progresivo_Tecnico,
    descripcionTecnico,
    evaluacion_Tecnico,
    servicioSolicitado_Incidencia,
    descripcionIncidencia_Incidencia,
    observaciones_Incidencia,
  };

  fetch("/INFORMATICA/src/Models/Servicios/actualizar_servicio.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  })
    .then((response) => response.text()) // Cambiar a text() para inspeccionar el HTML o JSON devuelto.
    .then((text) => {
      console.log(text); // Ver la respuesta cruda del servidor en la consola.
      const data = JSON.parse(text); // Intentar parsear manualmente si es JSON.
      if (data.success) {
        alert("Servicio actualizado exitosamente.");
        $("#editModal").modal("hide");
      } else {
        alert(data.error);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Ocurrió un error al actualizar el servicio.");
    });
}

document.addEventListener("DOMContentLoaded", function () {
  // Obtener todos los empleados para PersonalSolicitante
  fetch("./src/Models/Personal/obtener_personal.php")
    .then((response) => response.json())
    .then((data) => {
      // Llenar el select de PersonalSolicitante
      const selectSolicitante = document.getElementById("PersonalSolicitante");
      data.forEach((persona) => {
        const optionSolicitante = document.createElement("option");
        optionSolicitante.value = persona.Pk_NumeroEmpleado;
        optionSolicitante.textContent = `${persona.Pk_NumeroEmpleado} - ${persona.Nombre}`;
        selectSolicitante.appendChild(optionSolicitante);
      });

      // Llenar el select de PersonalEntrega
      const selectEntrega = document.getElementById("PersonalEntrega");
      data.forEach((persona) => {
        const optionEntrega = document.createElement("option");
        optionEntrega.value = persona.Pk_NumeroEmpleado;
        optionEntrega.textContent = `${persona.Pk_NumeroEmpleado} - ${persona.Nombre}`;
        selectEntrega.appendChild(optionEntrega);
      });
    })
    .catch((error) => console.error("Error fetching personal data:", error));

  // Obtener empleados filtrados para PersonalAtiende
  fetch("./src/Models/Personal/obtener_personal.php?filtrar=true")
    .then((response) => response.json())
    .then((data) => {
      const selectAtiende = document.getElementById("PersonalAtiende");
      data.forEach((persona) => {
        const optionAtiende = document.createElement("option");
        optionAtiende.value = persona.Pk_NumeroEmpleado;
        optionAtiende.textContent = `${persona.Pk_NumeroEmpleado} - ${persona.Nombre}`;
        selectAtiende.appendChild(optionAtiende);
      });
    })
    .catch((error) => console.error("Error fetching atiende data:", error));

  // Obtener activos para CABMSDictaminacion
  fetch("./src/Models/Activos/obtener_activos.php")
    .then((response) => response.json())
    .then((data) => {
      const selectCABMSDictaminacion =
        document.getElementById("CABMSDictaminacion");
      data.forEach((activos) => {
        const optionCABMSDictaminacion = document.createElement("option");
        optionCABMSDictaminacion.value = activos.CABMS;
        optionCABMSDictaminacion.textContent = activos.Descripcion;
        optionCABMSDictaminacion.title = activos.Descripcion;
        selectCABMSDictaminacion.appendChild(optionCABMSDictaminacion);
      });

      // Agregar evento change para filtrar progresivos
      selectCABMSDictaminacion.addEventListener("change", (event) => {
        const selectedCABMS = event.target.value;
        fetchProgresivos(selectedCABMS); // Llamar a la función para obtener progresivos
      });
    })
    .catch((error) => console.error("Error fetching activos data:", error));

  // Función para obtener progresivos filtrados
  function fetchProgresivos(cabms) {
    fetch(`./src/Models/Activos/obtener_progresivo.php?cabms=${cabms}`)
      .then((response) => response.json())
      .then((data) => {
        const selectProgresivo = document.getElementById(
          "ProgresivoDictaminacion"
        );
        selectProgresivo.innerHTML = ""; // Limpiar opciones anteriores

        // Agregar opción por defecto
        const defaultOption = document.createElement("option");
        defaultOption.value = "";
        defaultOption.textContent = "Selecciona un progresivo";
        defaultOption.selected = true;
        defaultOption.disabled = true;
        selectProgresivo.appendChild(defaultOption);

        // Agregar las opciones de los progresivos
        data.forEach((activos) => {
          const optionProgresivo = document.createElement("option");
          optionProgresivo.value = activos.Progresivo;
          optionProgresivo.textContent = activos.Progresivo;
          selectProgresivo.appendChild(optionProgresivo);
        });
      })
      .catch((error) =>
        console.error("Error fetching progresivos data:", error)
      );
  }
  // Obtener Progresivo inicialmente (si es necesario)
  fetchProgresivos(); // Si deseas cargar los progresivos inicialmente
});
