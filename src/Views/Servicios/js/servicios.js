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
    event.preventDefault(); // Evitar el envío normal del formulario

    console.log("Validando formulario...");
    let isValid = true;
    const invalidFields = [];

    // Recorre todos los campos de entrada del formulario
    $(this)
      .find(":input")
      .each(function () {
        if (!this.checkValidity()) {
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
    for (const [key, value] of formData.entries()) {
      console.log(`${key}: ${value}`);
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
  document.getElementById("ConfirmacionCampoOficio").value = ""; // Limpiar el valor del campo confirmación Oficio
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
    document.getElementById("ConfirmacionOficio").required = true; // Asegúrate de que este sea el elemento correcto
    incidendenciaFields.forEach((field) => (field.required = true)); // Hacer los campos requeridos
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
    const row = `
  <tr>
      <td>${`<a href="/INFORMATICA/src/Models/Servicios/generar_PDF.php?IDServicio=${servicio.Pk_IDServicio}" target="_blank">${servicio.Pk_IDServicio}</a>`}</td>
      <td>${servicio.Solicitante}</td>
      <td>${servicio.Atiende}</td>
      <td>${servicio.FechaSolicitud}</td>
      <td style="word-break: break-word; white-space: normal;">${servicio.Oficio}</td>
      <td>${servicio.FechaAtencion}</td>
      <td>${servicio.TipoServicio}</td>
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
