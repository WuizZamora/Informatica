<?php
// Pasar el rol como variable JavaScript
echo "<script>const userRole = " . json_encode($rol) . ";</script>";

if ($rol == 1 || $rol == 3) { ?>

    <div class="container text-center">
        <h2>Consulta de Servicios por Fecha 2022-2023</h2>
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
    fechaInicioInput.value = '2022-12-30'; // Asignar el valor

    const fechaFinInput = document.getElementById('fechaFin');
    fechaFinInput.value = '2023-12-30'; // Asignar el valor

    const fetchData = async () => {
        const fechaInicio = fechaInicioInput.value;
        const fechaFin = fechaFinInput.value;

        if (!fechaInicio || !fechaFin) {
            return; // No hacer nada si alguna fecha no está seleccionada
        }

        try {
            const response = await fetch('./src/Models/Servicios/consultar_servicio_periodo_pasado.php', {
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

    function mostrarResultados(data) {
        const resultadoDiv = document.getElementById('resultado');
        resultadoDiv.innerHTML = ''; // Limpiar resultados anteriores

        const {
            servicios
        } = data;

        if (servicios.length === 0) {
            resultadoDiv.innerHTML = '<div class="alert alert-info">No se encontraron resultados.</div>';
            return;
        }

        // Sección de Videos - Agrupación por Categoría
        let html = '<h3>Reporte de Videos</h3><hr>';
        const categorias = agruparPorCategoria(servicios);

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

        const totalVideosPeriodo = servicios[0]?.TotalVideosPeriodo || 0;
        html += `
        <div class="row justify-content-center">
            <div class="alert alert-success col-md-3">Total de Videos en el período: ${totalVideosPeriodo}</div>
        </div>`;

        // Renderizar el resultado final
        resultadoDiv.innerHTML = html;
    }

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

     //modal
     document.addEventListener('click', async (event) => {
        const linkEquipo = event.target.closest('.link-equipo');
        if (!linkEquipo) return; // Ignorar si no es un clic en un link-equipo

        event.preventDefault(); // Evitar el comportamiento de recargar la página

        const equipo = linkEquipo.dataset.equipo;
        const fechaInicio = fechaInicioInput.value; // Asegúrate de tener estos inputs definidos
        const fechaFin = fechaFinInput.value;

        try {
            const response = await fetch('./src/Models/Servicios/consultar_detalle_equipo_pasado.php', {
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
                </tr>`;
            });

            html += `
                </tbody>
            </table>`;
        }

        document.getElementById('modalBodyEquipo').innerHTML = html; // Asegúrate de usar el ID correcto
    }

</script>