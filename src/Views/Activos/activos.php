    <style>
        .spinner-activos {
            display: none;
            margin: 20px auto;
        }

        .pagination-activos {
            justify-content: center;
        }
    </style>
    <div class="container">

        <h1 class="text-center">Activos</h1>
        <p class="text-center">Contenido relacionado con los activos...</p>

        <!-- Spinner de carga -->
        <div class="spinner-border text-primary spinner-activos" role="status" id="spinnerActivos">
            <span class="visually-hidden">Cargando...</span>
        </div>

        <!-- Tabla de Activos -->
        <div class="table-responsive">
            <table class="table table-striped-columns table-hover text-center">
                <thead class="table-primary">
                    <tr>
                        <th>ID ACTIVO</th>
                        <th>CABMS</th>
                        <th>Progresivo</th>
                        <th>Descripción</th>
                        <th>Estado de Conservación</th>
                    </tr>
                </thead>
                <tbody id="tablaActivos"></tbody>
            </table>
        </div>

        <!-- Paginación -->
        <nav>
            <ul class="pagination pagination-activos" id="paginacionActivos"></ul>
        </nav>
    </div>


    <script>
        const tablaActivos = document.getElementById('tablaActivos');
        const spinnerActivos = document.getElementById('spinnerActivos');
        const paginacionActivos = document.getElementById('paginacionActivos');
        const filasPorPagina = 10;
        const paginasVisibles = 5;
        let paginaActualActivos = 1;
        let datosActivos = [];

        function toggleSpinnerActivos(mostrar) {
            spinnerActivos.style.display = mostrar ? 'block' : 'none';
        }

        fetch('src/Models/Activos/consultar_activos.php')
            .then(response => {
                if (!response.ok) throw new Error('Error al consultar los activos');
                return response.json();
            })
            .then(data => {
                datosActivos = data;
                renderTablaActivos();
                renderPaginacionActivos();
            })
            .catch(error => console.error('Error:', error))
            .finally(() => toggleSpinnerActivos(false));

        function renderTablaActivos() {
            tablaActivos.innerHTML = '';
            const inicio = (paginaActualActivos - 1) * filasPorPagina;
            const fin = inicio + filasPorPagina;
            const activosPagina = datosActivos.slice(inicio, fin);

            if (activosPagina.length === 0) {
                tablaActivos.innerHTML = '<tr><td colspan="4">No hay activos disponibles</td></tr>';
                return;
            }

            activosPagina.forEach(activo => {
                tablaActivos.innerHTML += `
                    <tr>
                        <td>${activo.Pk_IDActivo}</td>
                        <td>${activo.CABMS}</td>
                        <td>${activo.Progresivo}</td>
                        <td>${activo.Descripcion}</td>
                        <td>${activo.Estatus}</td>
                    </tr>`;
            });
        }

        function renderPaginacionActivos() {
            const totalPaginas = Math.ceil(datosActivos.length / filasPorPagina);
            paginacionActivos.innerHTML = '';

            const paginaInicio = Math.max(1, paginaActualActivos - Math.floor(paginasVisibles / 2));
            const paginaFin = Math.min(totalPaginas, paginaInicio + paginasVisibles - 1);

            if (paginaActualActivos > 1) {
                paginacionActivos.appendChild(crearItemPagina('Anterior', paginaActualActivos - 1));
            }

            for (let i = paginaInicio; i <= paginaFin; i++) {
                const itemPagina = crearItemPagina(i, i);
                if (i === paginaActualActivos) itemPagina.classList.add('active');
                paginacionActivos.appendChild(itemPagina);
            }

            if (paginaActualActivos < totalPaginas) {
                paginacionActivos.appendChild(crearItemPagina('Siguiente', paginaActualActivos + 1));
            }
        }

        function crearItemPagina(texto, pagina) {
            const itemPagina = document.createElement('li');
            itemPagina.className = 'page-item';
            itemPagina.innerHTML = `<a class="page-link" href="#">${texto}</a>`;
            itemPagina.addEventListener('click', (e) => {
                e.preventDefault();
                paginaActualActivos = pagina;
                renderTablaActivos();
                renderPaginacionActivos();
            });
            return itemPagina;
        }

        toggleSpinnerActivos(true);
    </script>