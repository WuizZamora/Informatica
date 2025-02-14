<?php
// Pasar el rol como variable JavaScript
echo "<script>const userRole = " . json_encode($rol) . ";</script>";

if ($rol == 1 || $rol == 3) { ?>

    <div class="container">
        <div class="row justify-content-center m-3">
            <div class="col-md-6 text-center">
                <button type="button" class="btn btn-info" onclick="window.location.href='index.php?page=serviciosInformePasados'">VIDEOS 2022-2023</button>
            </div>
        </div>
    </div>

    <div class="container text-center">
        <h2>Consulta de Servicios por Fecha</h2>
        <form id="fechaForm">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fechaInicio" class="form-label">Fecha de Inicio:</label>
                        <input type="date" id="fechaInicio" class="form-control text-center" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fechaFin" class="form-label">Fecha de Fin:</label>
                        <input type="date" id="fechaFin" class="form-control text-center" required>
                    </div>
                </div>
            </div>
        </form>
        <div id="resultado" class="mt-4"></div>
    </div>

    <!-- Modal Bootstrap -->
    <div class="modal fade" id="equipoModal" tabindex="-1" aria-labelledby="equipoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="equipoModalLabel">Información del Equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body container" id="modalBodyEquipo" style="max-height:80rem; overflow-y: auto;">
                    <!-- Aquí se mostrará la información del equipo -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


<?php } else { ?>
    <div class="container text-center">
        <h2>No tienes permiso para acceder a esta sección.</h2>
    </div>
<?php } ?>
<script>
    const fechaInicioInput = document.getElementById('fechaInicio');
    const fechaFinInput = document.getElementById('fechaFin');

    // Función para obtener la fecha en formato 'YYYY-MM-DD'
    const formatFecha = (fecha) => {
        const year = fecha.getFullYear();
        const month = String(fecha.getMonth() + 1).padStart(2, '0'); // Mes empieza en 0
        const day = String(fecha.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    // Establecer valores predeterminados en los inputs de fecha
    const hoy = new Date();
    const inicioAnio = new Date(hoy.getFullYear(), 0, 1); // 1 de enero del año en curso
    fechaInicioInput.value = formatFecha(inicioAnio);
    fechaFinInput.value = formatFecha(hoy);

    const fetchData = async () => {
        const fechaInicio = fechaInicioInput.value;
        const fechaFin = fechaFinInput.value;

        if (!fechaInicio || !fechaFin) {
            return; // No hacer nada si alguna fecha no está seleccionada
        }

        try {
            const response = await fetch('./src/Models/Servicios/consultar_servicios_periodo.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    fecha_inicio: fechaInicio,
                    fecha_fin: fechaFin
                }),
            });

            if (!response.ok) {
                throw new Error('Error en la respuesta de la red');
            }

            const data = await response.json();
            mostrarResultados(data);
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('resultado').innerHTML = '<div class="alert alert-danger">Ocurrió un error al realizar la consulta.</div>';
        }
    };

    // Llamar a fetchData al cargar la página para mostrar resultados iniciales
    document.addEventListener('DOMContentLoaded', fetchData);

    fechaInicioInput.addEventListener('change', fetchData);
    fechaFinInput.addEventListener('change', fetchData);

    // Agrupar los videos por categoría
    function agruparPorCategoria(videos) {
        const categorias = {};

        videos.forEach(item => {
            const {
                Categoria,
                TotalVideosPorCategoria,
                TotalSolicitudesPorCategoria,
                Equipo,
                TotalSolicitudes,
                CantidadVideosPorEquipo
            } = item;

            if (!categorias[Categoria]) {
                categorias[Categoria] = {
                    TotalVideosPorCategoria,
                    TotalSolicitudesPorCategoria,
                    equipos: [],
                };
            }

            categorias[Categoria].equipos.push({
                Equipo,
                TotalSolicitudes,
                CantidadVideosPorEquipo,
            });
        });

        return categorias;
    }

    function mostrarResultados(data) {
        const resultadoDiv = document.getElementById('resultado');
        resultadoDiv.innerHTML = ''; // Limpiar resultados anteriores

        const {
            servicios,
            reporteActivos,
            detallesIncidencias,
            videos
        } = data;

        if (servicios.length === 0 && reporteActivos.length === 0 && incidencias.length === 0 && videos.length === 0 && detallesIncidencias.length === 0) {
            resultadoDiv.innerHTML = '<div class="alert alert-info">No se encontraron resultados.</div>';
            return;
        }

        const totalSolicitudesPorPeriodo = servicios[0]?.TotalSolicitudesPorPeriodo || 0;

        let html = `
            <h3>Servicios solicitados</h3><hr>
            <table class="table table-striped-columns table-hover">
                <thead class="table-secondary">
                    <tr><th>Tipo de Servicio</th><th>Número de solicitudes</th></tr>
                </thead>
            <tbody>`;
        servicios.forEach(item => {
            html += `<tr>
            <td>${item.TipoServicio}</td>
            <td>${item.TotalPorTipo}</td>
            </tr>`;
        });
        html += '</tbody></table>';

        html += `
        <div class="row justify-content-center">
            <div class="col-md-3">
                <div class="alert alert-success">Total de Solicitudes por Período: ${totalSolicitudesPorPeriodo}</div>        
            </div>
        </div>`;

        // Sección de Activos
        const totalActivosFuncionales = reporteActivos.filter(item => item.Estado === 'FUNCIONAL').length;
        const totalActivosNoFuncionales = reporteActivos.filter(item => item.Estado === 'NO FUNCIONAL').length;
        const totalActivos = reporteActivos.length;

        html += `
            <h3>Reporte de Activos</h3><hr>
            <table class="table table-striped-columns table-hover">
                <thead class="table-secondary">
                    <tr>
                    <th>#</th>
                    <th>Número de inventario</th><th>Activo</th>
                    <th>Estado</th>
                    </tr>
                </thead>
                <tbody>`;
        reporteActivos.forEach((item, index) => {
            const progresivo = item.Progresivo.toString().padStart(6, '0');
            html += `<tr>
            <td>${index + 1}</td> <!-- Contador -->
            <td>${item.CABMS}-${progresivo}</td>
            <td>${item.NombreActivo}</td>
            <td>${item.Estado}</td>
        </tr>`;
        });
        html += '</tbody></table>';

        html += `
            <div class="row justify-content-center">
                <div class="alert alert-success col-md-4">Activos revisados: ${totalActivos}</div>
                <div class="alert alert-success col-md-4">Activos dictaminados como  No Funcionales: ${totalActivosNoFuncionales}</div>
                <div class="alert alert-success col-md-4">Activos Funcionales: ${totalActivosFuncionales}</div>
            </div>`;

        const incidenciaDetalles = detallesIncidencias.length;
        const totalSolicitudes = detallesIncidencias.length > 0 ? detallesIncidencias[0].TotalSolicitudes || 0 : 0;
        const totalGeneralIncidencias = detallesIncidencias.length > 0 ? detallesIncidencias[0].TotalGeneral || 0 : 0;

        html += `
        <h3>Reporte de Incidencias</h3><hr>
        <table class="table table-striped-columns table-hover">
            <thead class="table-secondary">
                <tr>
                <th>#</th>
                <th>Tipo de servicio solicitado</th><th>Total</th>
                </tr>
            </thead>
            <tbody>`;
        detallesIncidencias.forEach((item, index) => {
            html += `<tr>
                <td>${index + 1}</td> <!-- Contador -->
                <td>${item.Servicio}</td>
                <td>${item.Total}</td>
            </tr>`;
        });
        html += '</tbody></table>';
        html += `  
        <div class="row justify-content-center">
            <div class="alert alert-success col-md-3">Total de Solicitudes de Incidencias: ${totalSolicitudes}</div>
            <div class="alert alert-success col-md-3">Total de Servicios solicitados: ${totalGeneralIncidencias}</div>
        </div>`;

        // Sección de Videos - Agrupación por Categoría
        html += '<h3>Reporte de Videos</h3><hr>';
        const categorias = agruparPorCategoria(videos);

        Object.entries(categorias).forEach(([categoria, datos]) => {
            html += `
        <h4>${categoria}</h4>
        <div class="row justify-content-center">
            <div class="alert alert-info col-md-3">Total de Videos por ${categoria}: ${datos.TotalVideosPorCategoria}</div>
            <div class="alert alert-info col-md-3">Total de Solicitudes por ${categoria}: ${datos.TotalSolicitudesPorCategoria}</div>
        </div>
        <table class="table table-striped-columns table-hover">
            <thead class="table-secondary">
                <tr><th>#</th><th>Equipo</th><th>Total de Solicitudes</th><th>Cantidad de Videos</th></tr>
            </thead>
            <tbody>`;
            datos.equipos.forEach((equipo, index) => {
                html += `
                    <tr>
                    <td>${index + 1}</td> <!-- Contador -->
                        <td>
                            <a href="#" class="link-equipo" data-equipo="${equipo.Equipo}">
                                ${equipo.Equipo}
                            </a>
                        </td>
                        <td>${equipo.TotalSolicitudes}</td>
                        <td>${equipo.CantidadVideosPorEquipo}</td>
                    </tr>`;
            });
            html += '</tbody></table>';
        });

        const totalVideosPeriodo = videos[0]?.TotalVideosPeriodo || 0;
        html += `
        <div class="row justify-content-center">
            <div class="alert alert-success col-md-3">Total de Videos en el período: ${totalVideosPeriodo}</div>
        </div>`;

        // Renderizar el resultado final
        resultadoDiv.innerHTML = html;
    }

    //modal
    document.addEventListener('click', async (event) => {
        const linkEquipo = event.target.closest('.link-equipo');
        if (!linkEquipo) return; // Ignorar si no es un clic en un link-equipo

        event.preventDefault(); // Evitar el comportamiento de recargar la página

        const equipo = linkEquipo.dataset.equipo;
        const fechaInicio = fechaInicioInput.value; // Asegúrate de tener estos inputs definidos
        const fechaFin = fechaFinInput.value;

        try {
            const response = await fetch('./src/Models/Servicios/consultar_detalle_equipo.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    equipo,
                    fecha_inicio: fechaInicio,
                    fecha_fin: fechaFin
                }),
            });

            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }

            const data = await response.json();
            mostrarDetallesEquipo(data);
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('modalBodyEquipo').innerHTML = `
            <div class="alert alert-danger">Ocurrió un error al obtener los detalles del equipo.</div>`;
        }

        // Abrir el modal después del fetch
        const modal = new bootstrap.Modal(document.getElementById('equipoModal')); // Usar el ID correcto del modal
        modal.show();
    });

    function mostrarDetallesEquipo(data) {
        let html = `
        <h4 class="text-center">Detalles del Equipo: ${data[0]['Equipo']}</h4>`;

        // Verificar si hay datos
        if (data.length === 0) {
            html += `<p>No se encontraron detalles para el equipo.</p>`;
        } else {
            html += `
            <table class="table table-striped table-hover table-responsive text-center">
                <thead class="table-warning">
                    <tr>
                        <th>Solicitante</th>
                        <th>Cantidad de videos</th>
                        <th>Fecha de Solicitud</th>
                        <th>Periodo Inicial</th>
                        <th>Periodo Final</th>
                        <th>Mes</th>
                        <th>Periodo</th>
                        <th>Folio</th>
                        <th>Soporte</th>
                    </tr>
                </thead>
                <tbody>`;

            data.forEach(detalle => {
                html += `
                <tr>
                    <td>${detalle.Solicitante}</td>
                    <td>${detalle.CantidadVideos}</td>
                    <td>${detalle.FechaSolicitud}</td>
                    <td>${detalle.PeriodoInicial}</td>
                    <td>${detalle.PeriodoFinal}</td>
                    <td>${detalle.Mes}</td>
                    <td>${detalle.Periodo}</td>
                    <td>${detalle.Folio}</td>
                     <td>
          ${detalle.Observaciones
        ? `<a href="/INFORMATICA/src/Models/Servicios/${detalle.Observaciones}" target="_blank">
                  <i class="bi bi-file-earmark-text text-primary" style="font-size: 1.5rem;"></i>
                </a>`
        : `<i class="bi bi-file-earmark-text text-muted" style="font-size: 1.5rem; opacity: 0.5;" title="Sin información"></i>`
      }
        </td>
                </tr>`;
            });

            html += `
                </tbody>
            </table>`;
        }

        document.getElementById('modalBodyEquipo').innerHTML = html; // Asegúrate de usar el ID correcto
    }
</script>