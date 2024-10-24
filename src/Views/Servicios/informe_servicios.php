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
            const response = await fetch('./src/Models/Servicios/consultar_servicios_fechas.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ fecha_inicio: fechaInicio, fecha_fin: fechaFin }),
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

    function mostrarResultados(data) {
    const resultadoDiv = document.getElementById('resultado');
    resultadoDiv.innerHTML = ''; // Limpiar resultados anteriores

    const { servicios, reporteActivos, incidencias, videos } = data;

    if (servicios.length === 0 && reporteActivos.length === 0 && incidencias.length === 0 && videos.length === 0) {
        resultadoDiv.innerHTML = '<div class="alert alert-info">No se encontraron resultados.</div>';
        return;
    }

    const totalSolicitudesPorPeriodo = servicios[0]?.TotalSolicitudesPorPeriodo || 0;

    let html = `
    <h3>Servicios atendidos</h3><hr>
    <table class="table">
        <thead>
            <tr><th>Tipo de Servicio</th><th>Total de solicitudes por Tipo</th></tr>
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
    <table class="table">
        <thead>
            <tr><th>Número de inventario</th><th>Activo</th><th>Estado</th></tr>
        </thead>
        <tbody>`;
    reporteActivos.forEach(item => {
        const progresivo = item.Progresivo.toString().padStart(6, '0');
        html += `<tr>
        <td>${item.CABMS}-${progresivo}</td>
        <td>${item.NombreActivo}</td>
        <td>${item.Estado}</td>
    </tr>`;
    });
    html += '</tbody></table>';

    html += `
    <div class="row justify-content-center">
        <div class="alert alert-success col-md-4">Total de Activos revisados: ${totalActivos}</div>
        <div class="alert alert-success col-md-4">Total de Activos Funcionales: ${totalActivosFuncionales}</div>
        <div class="alert alert-success col-md-4">Total de Activos No Funcionales: ${totalActivosNoFuncionales}</div>
    </div>`;

    // Sección de Incidencias
    const totalIncidencias = incidencias[0]?.TotalIncidencias || 0;

    html += `
    <h3>Reporte de Incidencias</h3><hr>
    <table class="table">
        <thead>
            <tr><th>Servicios Solicitados</th></tr>
        </thead>
        <tbody>`;
    incidencias.forEach(item => {
        html += `<tr>
        <td>${item.ServicioSolicitado}</td>
    </tr>`;
    });
    html += '</tbody></table>';

    html += `  
    <div class="row justify-content-center">
        <div class="alert alert-success col-md-3">Total de Incidencias: ${totalIncidencias}</div>
    </div>
    `;

    // Sección de Videos
    const totalVideosPeriodo = videos[0]?.TotalVideosPeriodo || 0;

    html += `
    <h3>Reporte de Videos</h3><hr>
    <table class="table">
        <thead>
            <tr><th>Total de Solicitudes</th><th>Equipo</th><th>Cantidad de Videos</th></tr>
        </thead>
        <tbody>`;
    videos.forEach(item => {
        html += `<tr>
        <td>${item.TotalSolicitudes}</td>
        <td>${item.Equipo}</td>
        <td>${item.CantidadVideos}</td>
    </tr>`;
    });
    html += '</tbody></table>';

    html += `
    <div class="row justify-content-center">
        <div class="alert alert-success col-md-3">Total de Videos en el período: ${totalVideosPeriodo}</div>
    </div>
    `;

    // Renderizar el resultado final
    resultadoDiv.innerHTML = html;
}
</script>
