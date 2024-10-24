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
                Equipo,
                TotalSolicitudes,
                CantidadVideosPorEquipo
            } = item;

            if (!categorias[Categoria]) {
                categorias[Categoria] = {
                    TotalVideosPorCategoria,
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
            incidencias,
            videos
        } = data;

        if (servicios.length === 0 && reporteActivos.length === 0 && incidencias.length === 0 && videos.length === 0) {
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
        <div class="alert alert-success col-md-4">Activos revisados: ${totalActivos}</div>
        <div class="alert alert-success col-md-4">Activos dictaminados como  No Funcionales: ${totalActivosNoFuncionales}</div>
        <div class="alert alert-success col-md-4">Activos Funcionales: ${totalActivosFuncionales}</div>
    </div>`;

        // Sección de Incidencias
        const totalIncidencias = incidencias[0]?.TotalIncidencias || 0;

        html += `
    <h3>Reporte de Incidencias</h3><hr>
    <table class="table table-striped-columns table-hover">
        <thead class="table-secondary">
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
    </div>`;

        // Sección de Videos - Agrupación por Categoría
        html += '<h3>Reporte de Videos</h3><hr>';
        const categorias = agruparPorCategoria(videos);

        Object.entries(categorias).forEach(([categoria, datos]) => {
            html += `
        <h4>${categoria}</h4>
        <div class="row justify-content-center">
            <div class="alert alert-info col-md-3">Total de Videos en la Categoría: ${datos.TotalVideosPorCategoria}</div>
        </div>
        <table class="table table-striped-columns table-hover">
            <thead class="table-secondary">
                <tr><th>Equipo</th><th>Total de Solicitudes</th><th>Cantidad de Videos</th></tr>
            </thead>
            <tbody>`;
            datos.equipos.forEach(equipo => {
                html += `
            <tr>
                <td>${equipo.Equipo}</td>
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
</script>