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
  let activosData = []; // Almacena los activos para reutilizar
  let activeBlockCount = 1; // Contador para el número de bloques activos

  // Función para inicializar la carga de activos cuando se muestra el formulario
  function initDictaminacionForm() {
    if (activosData.length === 0) {
      // Solo cargar si no se ha hecho antes
      fetch("./src/Models/Activos/obtener_activos.php")
        .then((response) => response.json())
        .then((data) => {
          activosData = data; // Guardamos los datos globalmente
          addActivoBlock(); // Agregamos un bloque inicial
          updateRequiredFields(); // Actualiza los campos requeridos
        })
        .catch((error) => console.error("Error fetching activos data:", error));
    }
  }

  // Función para agregar un bloque de activo
  function addActivoBlock() {
    const container = document.getElementById("activosContainer");
    if (!container) return; // Verifica que el contenedor exista
    const blockId = activeBlockCount; // Usa el contador para el ID del bloque

    const activoBlock = document.createElement("div");
    activoBlock.className = "row mb-3 activo-block";

    activoBlock.innerHTML = `
      <div class="row justify-content-center">
          <div class="col-md-3">
              Activo ${blockId}
          </div>
      </div>
      <div class="col-md-4">
          <label class="form-label" for="CABMSDictaminacion${blockId}">CABMS</label>
          <select class="form-select text-center cabmsSelect" id="CABMSDictaminacion${blockId}" name="CABMSDictaminacion[]" required>
              <option disabled selected value="">Selecciona un activo</option>
          </select>
          <div class="invalid-feedback">Selecciona un activo válido</div>
      </div>
      <div class="col-md-4">
          <label class="form-label" for="ProgresivoDictaminacion${blockId}">Progresivo</label>
          <select class="form-select text-center progresivoSelect" id="ProgresivoDictaminacion${blockId}" name="ProgresivoDictaminacion[]" required>
              <option disabled selected value="">Selecciona un progresivo</option>
          </select>
          <div class="invalid-feedback">Selecciona un progresivo válido</div>
      </div>
      <div class="col-md-3">
          <label class="form-label" for="EstadoConservacion${blockId}">Estado de conservación</label>
          <select class="form-select text-center estadoSelect" id="EstadoConservacion${blockId}" name="EstadoConservacion[]" required>
              <option disabled selected value="">Elige una opción</option>
              <option value="Funcional">Funcional</option>
              <option value="No funcional">Baja</option>
          </select>
      </div>
      <div class="col-md-1 d-flex align-items-end">
          <button type="button" class="btn btn-danger removeActivoBtn">-</button>
      </div>
    `;

    const cabmsSelect = activoBlock.querySelector(".cabmsSelect");
    fillCABMSSelect(cabmsSelect);

    // Evento para manejar el cambio en el select de CABMS
    cabmsSelect.addEventListener("change", (event) => {
      const progresivoSelect = activoBlock.querySelector(".progresivoSelect");
      fetchProgresivos(event.target.value, progresivoSelect);
    });

    const removeBtn = activoBlock.querySelector(".removeActivoBtn");
    // Evento para manejar la eliminación del bloque de activo
    removeBtn.addEventListener("click", () => {
      // Verificar si es el primer bloque (blockId es 1)
      if (blockId === 1) {
        Swal.fire({
          icon: "warning",
          title: "No puedes eliminar el primer activo.",
        });
      } else {
        container.removeChild(activoBlock);
        activeBlockCount--; // Decrementar el contador de bloques activos
        toggleRemoveButton(container); // Actualizar el estado del botón
        updateRequiredFields(); // Actualiza campos requeridos
      }
    });

    container.appendChild(activoBlock);
    activeBlockCount++; // Incrementar el contador de bloques activos
    toggleRemoveButton(container); // Actualizar el estado del botón
    updateRequiredFields(); // Actualiza campos requeridos
  }

  // Llenar el select de CABMS
  function fillCABMSSelect(select) {
    activosData.forEach((activo) => {
      const option = document.createElement("option");
      option.value = activo.CABMS;
      option.textContent = `${activo.CABMS} - ${activo.Descripcion}`;
      select.appendChild(option);
    });
  }

  // Obtener y llenar progresivos según el CABMS seleccionado
  function fetchProgresivos(cabms, selectProgresivo) {
    fetch(`./src/Models/Activos/obtener_progresivo.php?cabms=${cabms}`)
      .then((response) => response.json())
      .then((data) => {
        selectProgresivo.innerHTML = `
          <option disabled selected value="">Selecciona un progresivo</option>
        `;

        data.forEach((activo) => {
          const option = document.createElement("option");
          option.value = activo.Progresivo;
          option.textContent = activo.Progresivo;
          selectProgresivo.appendChild(option);
        });
      })
      .catch((error) =>
        console.error("Error fetching progresivos data:", error)
      );
  }

  // Función para habilitar o deshabilitar el botón de eliminar
  function toggleRemoveButton(container) {
    const removeButtons = container.querySelectorAll(".removeActivoBtn");
    removeButtons.forEach((button) => {
      button.disabled = activeBlockCount === 1; // Deshabilitar si hay 1 o menos bloques
    });
  }

  // Función para actualizar los campos requeridos
  function updateRequiredFields() {
    const activoBlocks = document.querySelectorAll(".activo-block");
    const requiredFields = [...document.querySelectorAll("[required]")];
    requiredFields.forEach((field) => (field.required = false)); // Quitar requerimiento a todos

    if (activoBlocks.length > 0) {
      requiredFields.forEach((field) => (field.required = true)); // Volver a requerir si hay bloques activos
    }
  }

  fetchServicios();
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

      let value = input.value;

      // Verificamos si es el select múltiple de ServicioSolicitado
      if (input.id === "ServicioSolicitado") {
        const selectedOptions = Array.from(input.selectedOptions)
          .filter((option) => option.value !== "default") // Ignora la opción "Elige una opción"
          .map((option) => option.textContent); // Usa textContent para mostrar el texto

        value = selectedOptions.join(", "); // Une las selecciones por comas
      }

      // Solo agregamos filas si hay un valor válido
      if (value && value.trim() !== "") {
        formDataHtml += `<tr><td>${label}</td><td style="max-width: 20rem; word-break: break-all;">${value}</td></tr>`;
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

        fetchServicios();
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

    // Reiniciar el bloque de activos a uno solo
    const activosContainer = document.getElementById("activosContainer");
    while (activosContainer.firstChild) {
      activosContainer.removeChild(activosContainer.firstChild); // Elimina todos los bloques
    }

    activeBlockCount = 1; // Reinicia el contador a 1
    addActivoBlock(); // Agrega un bloque inicial
  }

  // Carga de datos en los selects
  function cargarDatosIniciales() {
    fetch("./src/Models/Personal/obtener_personal.php")
      .then((response) => response.json())
      .then((data) => {
        llenarSelect(data, "PersonalSolicitante");
      })
      .catch((error) => console.error("Error fetching personal data:", error));

    fetch("./src/Models/Personal/obtener_personal.php?filtrar=true")
      .then((response) => response.json())
      .then((data) => {
        llenarSelect(data, "PersonalAtiende");
      })
      .catch((error) => console.error("Error fetching atiende data:", error));
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
      option.textContent = `${item[textKey]}-${item[valueKey]}`;
      select.appendChild(option);
    });
  }

  // Evento para agregar más bloques de activo
  const addActivoBtn = document.getElementById("addActivoBtn");
  if (addActivoBtn) {
    addActivoBtn.addEventListener("click", addActivoBlock);
  }
  initDictaminacionForm();
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
    "#formVideos input, #formVideos select"
  );
  const dictaminacionFields = document.querySelectorAll(
    "#formDictaminacion input, #formDictaminacion textarea, #formDictaminacion select, #activosContainer select"
  );

  // Ocultar todos los formularios
  document.getElementById("formIncidencia").style.display = "none";
  document.getElementById("formVideos").style.display = "none";
  document.getElementById("formDictaminacion").style.display = "none";

  // Limpiar valores de todos los campos y quitar el atributo "required"
  incidendenciaFields.forEach((field) => {
    field.value = "";
    field.required = false;
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
  const inputConfirmacionOficio = document.getElementById("ConfirmacionOficio"); // <== Asegúrate que este input exista
  inputConfirmacionOficio.value = ""; // Limpiar valor
  inputConfirmacionOficio.required = false; // Asegurarse que no sea requerido por defecto
  campoConfirmacionOficio.style.display = "none"; // Ocultar contenedor

  // Limpiar y reiniciar el campo Oficio
  const campoOficio = document.getElementById("CampoOficio");
  const inputOficio = document.getElementById("Oficio");
  inputOficio.value = "";
  inputOficio.removeAttribute("readonly");
  inputOficio.required = false;
  campoOficio.style.display = "none";

  // Mostrar el formulario correspondiente
  if (tipoServicio === "INCIDENCIA") {
    document.getElementById("formIncidencia").style.display = "block";
    campoConfirmacionOficio.style.display = "block"; // Mostrar campo confirmacion
    inputConfirmacionOficio.required = true; // <== Asegurarse que el input sea requerido
    incidendenciaFields.forEach((field) => (field.required = true));

    const servicioSolicitadoSelect =
      document.getElementById("ServicioSolicitado");
    servicioSolicitadoSelect.required = true; // Hacer el select múltiple requerido
  } else {
    const servicioSolicitadoSelect =
      document.getElementById("ServicioSolicitado");
    servicioSolicitadoSelect.required = false;
  }

  if (tipoServicio === "ENTREGA MATERIAL FÍLMICO") {
    document.getElementById("formVideos").style.display = "block";
    campoConfirmacionOficio.style.display = "block"; // Mostrar campo confirmacion
    inputConfirmacionOficio.required = true; // <== Asegurar que sea requerido
    videoFields.forEach((field) => (field.required = true));
  } else if (tipoServicio === "TÉCNICO") {
    document.getElementById("formDictaminacion").style.display = "block";
    campoOficio.style.display = "block";
    dictaminacionFields.forEach((field) => (field.required = true));
    inputOficio.removeAttribute("readonly");
    inputOficio.setAttribute("required", "required");
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
let allData = []; // Almacenar todos los datos al inicio

function fetchServicios() {
  fetch("./src/Models/Servicios/obtener_servicios.php")
    .then((response) => response.json())
    .then((data) => {
      allData = data; // Guardar los datos originales
      actualizarServicios(); // Renderizar todos los servicios inicialmente
    })
    .catch((error) => console.error("Error:", error));
}

function actualizarServicios() {
  const searchInput = document.getElementById("searchInput");
  if (!searchInput) {
    console.error("El elemento con ID 'searchInput' no se encontró en el DOM.");
    return; // Salir de la función si el elemento no existe
  }

  const searchTerm = searchInput.value.toLowerCase();
  const filteredData = searchTerm
    ? allData.filter(
        (servicio) =>
          servicio.Folio.toLowerCase().includes(searchTerm) ||
          servicio.Oficio.toLowerCase().includes(searchTerm)
      )
    : allData;

  totalPages = Math.ceil(filteredData.length / recordsPerPage);
  renderTable(filteredData, currentPage);
  renderPagination(totalPages);
}

function renderTable(data, page) {
  const serviciosBody = document.getElementById("serviciosBody");
  serviciosBody.innerHTML = "";
  const start = (page - 1) * recordsPerPage;
  const end = start + recordsPerPage;
  const paginatedData = data.slice(start, end);

  paginatedData.forEach((servicio) => {
    const isEntregaMaterial =
      servicio.TipoServicio === "ENTREGA MATERIAL FÍLMICO";
    const isPendiente = servicio.EstadoSolicitud === "PENDIENTE";
    const shouldDisable = (isEntregaMaterial&&isPendiente) ;

    const row = `
      <tr>
        <td>${servicio.Folio}</td>
        <td>${servicio.Solicitante}</td>
        <td>${servicio.FechaSolicitud}</td>
        <td style="word-break: break-word; white-space: normal;">
          ${servicio.Oficio}
        </td>
        <td>${servicio.FechaAtencion}</td>
        <td>${servicio.TipoServicio}</td>
        <td class="${
          servicio.EstadoSolicitud === "CANCELADO"
            ? "text-danger"
            : servicio.EstadoSolicitud === "COMPLETADO"
            ? "text-success"
            : ""
        }">
          ${servicio.EstadoSolicitud}
        </td>
        <td>
          ${
            servicio.SoporteDocumental
              ? `<a href="/INFORMATICA/src/Models/Servicios/${servicio.SoporteDocumental}" target="_blank">
                  <i class="bi bi-file-earmark-text text-primary" style="font-size: 1.5rem;"></i>
                </a>`
              : `<i class="bi bi-file-earmark-text text-muted" style="font-size: 1.5rem; opacity: 0.5;" title="Sin información"></i>`
          }
        </td>
        <td>
          ${
            userRole == 1 || userRole == 2 || userRole == 3 || userRole == 4
              ? `<a href="/INFORMATICA/src/Models/Servicios/generar_PDF.php?IDServicio=${
                  servicio.Pk_IDServicio
                }" 
                  target="_blank" 
                  class="btn btn-success ${shouldDisable ? "disabled" : ""}"
                  tabindex="${shouldDisable ? "-1" : "0"}">
                  Ver
                </a>`
              : ""
          }
          ${
            userRole == 1 || userRole == 3
              ? `<button class="btn btn-primary" onclick="editServicio(${servicio.Pk_IDServicio})">Editar</button>`
              : ""
          }
          ${
            userRole == 1 || userRole == 2 || userRole == 3
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
      <a class="page-link" href="#" onclick="changePage(${
        currentPage - 1
      })">Anterior</a>
    </li>
  `;

  // Determinar el rango de páginas a mostrar
  const maxVisiblePages = 5; // Número máximo de páginas visibles
  let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
  let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

  // Ajustar el rango si estamos cerca de los extremos
  if (endPage - startPage < maxVisiblePages - 1) {
    if (startPage === 1) {
      endPage = Math.min(totalPages, startPage + maxVisiblePages - 1); // Cambiado a "endPage ="
    } else if (endPage === totalPages) {
      startPage = Math.max(1, endPage - maxVisiblePages + 1); // Cambiado a "startPage ="
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
setInterval(fetchServicios, 10000);

function editServicio(id) {
  fetch(
    `/INFORMATICA/src/Models/Servicios/obtener_servicio_detalles.php?IDServicio=${id}`
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
        let modalContent = `
        <strong># ${data[0].Pk_IDServicio}</strong>
          <div class="form-group">
              <label for="solicitante">Solicitante:</label>
              <select class="form-select" id="solicitante" name="solicitante" required>
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
              <input type="text" class="form-control text-center" id="tipoServicio" value="${data[0].TipoServicio}" readonly>
          </div>
          <div class="form-group">
              <label for="OficioUpdate">Oficio:</label>
              <input type="text" class="form-control" id="OficioUpdate" value="${data[0].Oficio}">
          </div>
          <div class="form-group">
              <label for="fechaSolicitud">Fecha de solicitud:</label>
              <input type="date" class="form-control" id="fechaSolicitud" value="${data[0].FechaSolicitud}">
          </div>
          <div id="camposAdicionales"></div>
        `;

        // Mostrar contenido en el modal
        document.getElementById("modalBody").innerHTML = modalContent;

        // Llenar los selects de solicitante, entrega y atiende
        llenarSelectPersonal(
          "./src/Models/Personal/obtener_personal.php",
          "solicitante",
          data[0].Solicitante
        ).then(() => {
          const solicitanteSelect = document.getElementById("solicitante");
          solicitanteSelect.value = data[0].Solicitante; // Establecer el valor seleccionado aquí
        });

        llenarSelectPersonal(
          "./src/Models/Personal/obtener_personal.php?filtrar=true",
          "atiende",
          data[0].Atiende
        ).then(() => {
          const atiendeSelect = document.getElementById("atiende");
          atiendeSelect.value = data[0].Atiende; // Establecer el valor seleccionado aquí
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
          const idServicio = data[0].Pk_IDServicio;
          const solicitante = document.getElementById("solicitante").value;
          const atiende = document.getElementById("atiende").value;
          const oficio = document.getElementById("OficioUpdate").value;
          const fechaSolicitud =
            document.getElementById("fechaSolicitud").value;
          const tipoServicio = document.getElementById("tipoServicio").value;

          // Inicializar un objeto para almacenar los datos del servicio
          const datosServicio = {
            idServicio,
            solicitante,
            atiende,
            oficio,
            fechaSolicitud,
            tipoServicio,
          };

          console.log(datosServicio);

          // Agregar datos específicos según el tipo de servicio
          if (tipoServicio === "TÉCNICO") {
            const DescripcionTecnico = document.getElementById(
              "descripcionTecnico_General"
            ).value;
            // Inicializar un arreglo para los ID Activos y sus evaluaciones
            const activos = [];

            // Recorrer los datos para obtener ID Activos y Evaluaciones
            const idActivosElements =
              document.querySelectorAll("[id^='id_activo_']");
            const evaluacionesElements = document.querySelectorAll(
              "[id^='evaluacion_Tecnico_']"
            );
            const id = document.querySelectorAll("[id^='id_']");
            const cabmsActivo = document.querySelectorAll(
              "[id^='cabms_Tecnico_']"
            );
            const progresivoActivo = document.querySelectorAll(
              "[id^='progresivo_Tecnico_']"
            );

            idActivosElements.forEach((element, index) => {
              const IDActivo = element.value;
              const EvaluacionTecnico = evaluacionesElements[index].value;
              const idPK = id[index].value;
              const cabmsTecnico = cabmsActivo[index].value;
              const progresivoTecnico = progresivoActivo[index].value;

              // Agregar al arreglo de activos
              activos.push({
                IDActivo,
                EvaluacionTecnico,
                idPK,
                cabmsTecnico,
                progresivoTecnico,
              });
            });

            // Asignar el arreglo de activos al objeto de datos del servicio
            datosServicio.activos = activos;
            datosServicio.DescripcionTecnico = DescripcionTecnico;
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

            datosServicio.ServicioSolicitado = selectedOptionsUPDATE;
            datosServicio.DescripcionIncidencia = DescripcionIncidencia;
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
                  Swal.fire({
                    title: "¡Éxito!",
                    text: "Datos del servicio actualizados exitosamente.",
                    icon: "success",
                    timer: 3000, // Duración en milisegundos (3 segundos)
                    showConfirmButton: false, // No mostrar botón de aceptar
                  });
                  myModal.hide(); // Cierra el modal si estás usando uno
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
              console.error("Error al guardar el servicio:", error);
              Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Error al guardar el servicio",
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

// Función reutilizable para llenar los selects
function llenarSelectPersonal(url, selectId, valorSeleccionado) {
  return fetch(url)
    .then((response) => response.json())
    .then((data) => {
      const select = document.getElementById(selectId);
      data.forEach((persona) => {
        const option = document.createElement("option");
        option.value = persona.Pk_NumeroEmpleado;
        option.textContent = `${persona.Nombre}-${persona.Pk_NumeroEmpleado}`;
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
          <input type="number" class="form-control" id="cantidadVideos" value="${data[0].CantidadVideos}">
      </div>
      <div class="form-group">
          <label for="periodoInicial_Videos">Periodo Inicial:</label>
          <input type="date" class="form-control" id="periodoInicial_Videos" value="${data[0].PeriodoInicial}">
      </div>
      <div class="form-group">
          <label for="periodoFinal_Videos">Periodo Final:</label>
          <input type="date" class="form-control" id="periodoFinal_Videos" value="${data[0].PeriodoFinal}">
      </div>
      <div class="form-group">
          <label for="periodo_Videos">Periodo:</label>
          <input type="text" class="form-control" id="periodo_Videos" value="${data[0].Periodo}">
      </div>
      <div class="form-group">
          <label for="equipo_Videos">Equipo:</label>
          <input type="text" class="form-control" id="equipo_Videos" value="${data[0].Equipo}">
      </div>
      <div class="form-group">
          <label for="DescripcionVideosUpdate">Descripcion de los Videos:</label>
          <textarea class="form-control" rows="8" id="DescripcionVideosUpdate">${data[0].DescripcionVideo}</textarea>
      </div>
    `;
  } else if (tipoServicio === "TÉCNICO") {
    camposAdicionalesDiv.innerHTML = "";

    // Mostrar la descripción general una vez
    camposAdicionalesDiv.innerHTML += `
    <div class="form-group">
        <label for="descripcionTecnico_General">Descripción General:</label>
        <textarea class="form-control" rows="4" id="descripcionTecnico_General">${data[0].DescripcionTecnico}</textarea>
    </div>
    `;

    // Iterar sobre los datos para crear campos de entrada para cada activo
    data.forEach((item, index) => {
      camposAdicionalesDiv.innerHTML += `
        <hr>
        <div class="form-group" style="display:none;">
          <label for="id_${index}">ID:</label>
          <input type="text" class="form-control" id="id_${index}" value="${
        item.ID
      }">
        </div>
        <div class="form-group" style="display:none;">
          <label for="id_activo_${index}">ID ACTIVO:</label>
          <input type="text" class="form-control" id="id_activo_${index}" value="${
        item.Fk_IDActivo_Activos
      }">
        </div>
        <div class="form-group">
          <label for="cabms_Tecnico_${index}">CABMS:</label>
          <input type="text" class="form-control" id="cabms_Tecnico_${index}" value="${
        item.CABMS
      }">
        </div>
        <div class="form-group">
          <label for="progresivo_Tecnico_${index}">PROGRESIVO:</label>
          <input type="text" class="form-control" id="progresivo_Tecnico_${index}" value="${
        item.Progresivo
      }">
        </div>
        <div class="form-group">
          <label for="evaluacion_Tecnico_${index}">Evaluación:</label>
          <select class="form-select" id="evaluacion_Tecnico_${index}" name="evaluacion_Tecnico_${index}" required>
            <option selected disabled value="">Elige una opción</option>
            <option value="FUNCIONAL" ${
              item.Evaluacion === "FUNCIONAL" ? "selected" : ""
            }>Funcional</option>
                  <option value="NO FUNCIONAL" ${
                    item.Evaluacion === "NO FUNCIONAL" ? "selected" : ""
                  }>Baja</option>
          </select>
        </div>
        `;
    });
  } else if (tipoServicio === "INCIDENCIA") {
    // Suponiendo que data.ServicioSolicitado es una cadena concatenada
    const serviciosSolicitados = data[0].ServicioSolicitado.split(", ").map(
      (servicio) => servicio.trim()
    );

    camposAdicionalesDiv.innerHTML = `
    <hr>
      <div class="form-group">
          <label for="ServicioSolicitadoUPDATE">Servicio Solicitado:</label>
          <select class="form-select text-center" id="ServicioSolicitadoUPDATE" name="ServicioSolicitadoUPDATE[]" size="6" multiple>
              <option value="">Elige una opción</option>
            <option value="GESTIÓN DE EQUIPOS" ${
                serviciosSolicitados.includes("GESTIÓN DE EQUIPOS") ? "selected" : ""
            }>GESTIÓN DE EQUIPOS</option>
            <option value="CARPETA COMPARTIDA" ${
                serviciosSolicitados.includes("CARPETA COMPARTIDA") ? "selected" : ""
            }>CARPETA COMPARTIDA</option>
            <option value="CREACIÓN DE USUARIO" ${
                serviciosSolicitados.includes("CREACIÓN DE USUARIO") ? "selected" : ""
            }>CREACIÓN DE USUARIO</option>
            <option value="CORREO ELECTRÓNICO INSTITUCIONAL" ${
                serviciosSolicitados.includes("CORREO ELECTRÓNICO INSTITUCIONAL") ? "selected" : ""
            }>CORREO ELECTRÓNICO INSTITUCIONAL</option>
            <option value="SOPORTE TÉCNICO" ${
                serviciosSolicitados.includes("SOPORTE TÉCNICO") ? "selected" : ""
            }>SOPORTE TÉCNICO</option>
            <option value="ENTREGA DE INSUMOS NUEVOS PARA EQUIPO DE COMPUTO" ${
                serviciosSolicitados.includes("ENTREGA DE INSUMOS NUEVOS PARA EQUIPO DE COMPUTO") ? "selected" : ""
            }>ENTREGA DE INSUMOS NUEVOS PARA EQUIPO DE COMPUTO</option>
            <option value="CONECTIVIDAD" ${
                serviciosSolicitados.includes("CONECTIVIDAD") ? "selected" : ""
            }>CONECTIVIDAD</option>
            <option value="GESTIÓN DE USUARIOS" ${
                serviciosSolicitados.includes("GESTIÓN DE USUARIOS") ? "selected" : ""
            }>GESTIÓN DE USUARIOS</option>
            <option value="CAPACITACIÓN Y ASESORÍA" ${
                serviciosSolicitados.includes("CAPACITACIÓN Y ASESORÍA") ? "selected" : ""
            }>CAPACITACIÓN Y ASESORÍA</option>
            <option value="OTROS" ${
                serviciosSolicitados.includes("OTROS") ? "selected" : ""
            }>OTROS</option>
          </select>
          <div class="invalid-feedback">
              Ingresa una opción
          </div>
      </div>
      <div class="form-group">
          <label for="descripcionIncidencia_Incidencia">Descripción:</label>
          <textarea class="form-control" rows="8" id="descripcionIncidencia_Incidencia">${
            data[0].DescripcionIncidencia
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
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: data.error,
        });
      } else {
        mostrarDatos(data);
      }
    })
    .catch((error) => {
      alert(error.message);
    });

  function mostrarDatos(servicio) {
    const { Pk_IDServicio, EstadoSolicitud, SoporteDocumental, Observaciones } = servicio;
    let ModalContentEstado = `
    <div class="row justify-content-center text-center">
    <div class="col-md-8">
    <form id="servicioForm" class="needs-validation" enctype="multipart/form-data">
      <label for="idServicioEstado" class="form-label" style="display:none;">ID Servicio:</label>
      <input type="text" class="form-control text-center" style="display:none;" id="idServicioEstado" name="Pk_IDServicio" value="${Pk_IDServicio}" readonly>
      
      <label for="estadoSolicitud" class="form-label">Estado de Solicitud:</label>
      <select class="form-select text-center" id="estadoSolicitud" name="EstadoSolicitud" required>
      <option selected disabled value="">Elige una opción</option>
      <option value="PENDIENTE" disabled ${
        EstadoSolicitud === "PENDIENTE" ? "selected" : ""
      }>PENDIENTE</option>
      <option value="COMPLETADO" ${
        EstadoSolicitud === "COMPLETADO" ? "selected" : ""
      }>COMPLETADO</option>
      <option value="CANCELADO" ${
        EstadoSolicitud === "CANCELADO" ? "selected" : ""
      }>CANCELADO</option>
      </select>

      <label for="Observaciones" class="form-label">Observaciones:</label>
      <textarea class="form-control text-center" id="Observaciones" name="Observaciones" maxlength="400" rows="8">${Observaciones !== null ? Observaciones : ""}</textarea>

      <label class="form-label">Soporte Documental:</label>
        ${
          SoporteDocumental
            ? `<br><a href="/INFORMATICA/src/Models/Servicios/${SoporteDocumental}" target="_blank" class="btn btn-link">Ver documento</a>`
            : `<input type="file" class="form-control" id="soporteDocumental" name="SoporteDocumental" required>`
        }
        </form>
        ${
          userRole == 1 || userRole == 3
            ? `<button type="button" class="btn btn-danger btn-sm" onclick="BorrarSoporteDocumental(${Pk_IDServicio});">BORRAR SOPORTE</button>`
            : ""
        }  
        </div>
        </div>
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
      const observaciones = document.getElementById("Observaciones").value;
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
      formData.append("Observaciones", observaciones);

      // Solo agregar soporteDocumental si existe
      if (soporteDocumental) {
        formData.append("SoporteDocumental", soporteDocumental);
      }

        // Mostrar en consola el contenido de FormData
  console.log("Contenido de FormData:");
  for (let [key, value] of formData.entries()) {
    console.log(`${key}:`, value);
  }
      // Enviar los datos al backend usando fetch
      fetch(
        "/INFORMATICA/src/Models/Servicios/actualizar_estado_solicitud.php",
        {
          method: "POST",
          body: formData,
        }
      )
        .then((response) => {
          if (!response.ok) {
            throw new Error("Error en la solicitud al servidor");
          }
          return response.json();
        })
        .then((data) => {
          // Manejar la respuesta del servidor
          if (data.error) {
            Swal.fire({
              icon: "error",
              title: "Oops...",
              text: data.error,
            });
          } else {
            Swal.fire({
              title: "¡Éxito!",
              text: "Estado de solicitud actualizado exitosamente",
              icon: "success",
              timer: 3000, // Duración en milisegundos (3 segundos)
              showConfirmButton: false, // No mostrar botón de aceptar
            });
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

function BorrarSoporteDocumental(idServicio) {
  const url = "/INFORMATICA/src/Models/Servicios/borrar_soporte.php";
  const myModalEstado = bootstrap.Modal.getInstance(
    document.getElementById("servicioModal")
  ); // Obtiene la instancia del modal

  fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ idServicio }), // Enviando el idServicio en el cuerpo de la solicitud
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Error en la solicitud: " + response.statusText);
      }
      return response.text(); // Cambia a .text() para ver el contenido de la respuesta
    })
    .then((text) => {
      // Cierra el modal después de la eliminación exitosa
      myModalEstado.hide();

      Swal.fire({
        title: "¡Éxito!",
        text: "Soporte documental eliminado exitosamente.",
        icon: "success",
        timer: 3000, // Duración en milisegundos (3 segundos)
        showConfirmButton: false, // No mostrar botón de aceptar
      });
    })
    .catch((error) => {
      console.error("Hubo un problema con la solicitud:", error);
    });
}
