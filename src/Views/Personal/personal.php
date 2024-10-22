<div class="container text-center">
    <!-- <h3>ALTA DE PERSONAL</h3>
    <hr> -->
    <h3 class="text-center">DETALLES DEL PERSONAL</h3>
    <hr>

    <!-- Filtro de búsqueda -->
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="input-group mb-3">
                <span class="input-group-text" id="search-icon">
                    <i class="bi bi-search"></i> <!-- Ícono de búsqueda -->
                </span>
                <input
                    type="text"
                    id="filtroPersonal"
                    class="form-control"
                    placeholder="Buscar por numero de empleado o nombre"
                    aria-label="Buscar por numero de empleado o nombre"
                    aria-describedby="search-icon"
                    oninput="filtrarPersonal()" />
            </div>
        </div>
    </div>

    <!-- Tabla de Personal -->
    <div class="table-responsive">
        <table class="table table-striped-columns table-hover text-center">
            <thead class="table-success">
                <tr>
                    <th>NÚMERO DE EMPLEADO</th>
                    <th>NOMBRE</th>
                    <th>RFC</th>
                    <th>Estatus</th>
                    <?php if ($rol != 2) { ?>
                        <th>Acciones</th>
                    <?php } else { ?>
                    <?php } ?>
                </tr>
            </thead>
            <tbody id="tablaPersonal"></tbody>
        </table>
    </div>
    <nav id="navegacionPaginas" aria-label="Page navigation" class="mt-4"></nav>
</div>

<script>
    const userRole = <?php echo json_encode($rol); ?>; // Pasar el rol como variable JavaScript
    let paginaActual = 1;
    const registrosPorPagina = 10; // Número de registros por página
    let datosPersonal = []; // Almacena los datos de personal

    // Función para obtener los datos de personal
    function obtenerPersonal() {
        fetch('./src/Models/Personal/consultar_personal.php')
            .then(respuesta => {
                if (!respuesta.ok) {
                    throw new Error('Error en la consulta de personal: ' + respuesta.statusText);
                }
                return respuesta.json(); // Convertir la respuesta a JSON
            })
            .then(datos => {
                datosPersonal = datos; // Guardar todos los datos
                mostrarPagina(paginaActual); // Mostrar la página actual
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('tablaPersonal').innerHTML =
                    '<tr><td colspan="3" class="text-danger">Error al cargar los datos de personal.</td></tr>';
            });
    }

    // Función para filtrar los datos de personal
    function filtrarPersonal() {
        const filtro = document.getElementById('filtroPersonal').value.toLowerCase();
        const datosFiltrados = datosPersonal.filter(persona =>
            persona.Pk_NumeroEmpleado.toString().toLowerCase().includes(filtro) ||
            persona.Nombre.toLowerCase().includes(filtro)
        );
        mostrarPaginaConDatos(datosFiltrados);
    }

    // Función para mostrar la lista de personal en la página actual
    function mostrarPagina(pagina) {
        const tablaBody = document.getElementById('tablaPersonal');
        tablaBody.innerHTML = ''; // Limpiar contenido anterior

        // Calcular el inicio y fin de los datos para la página actual
        const inicio = (pagina - 1) * registrosPorPagina;
        const fin = inicio + registrosPorPagina;
        const datosPaginados = datosPersonal.slice(inicio, fin);

        // Rellenar las filas con los datos
        datosPaginados.forEach(persona => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${persona.Pk_NumeroEmpleado}</td>
                <td>${persona.Nombre}</td>
                <td>${persona.RFC}</td>
                <td>${persona.Estatus}</td>
                <td> 
                ${userRole == 1 || userRole == 3  ? `<a href="/INFORMATICA/src/Models/" target="_blank" class="btn btn-success">Editar</a>` : ""}           
                </td>
            `;
            tablaBody.appendChild(fila);
        });

        generarNavegacion();
    }

    // Función para mostrar datos filtrados
    function mostrarPaginaConDatos(datos) {
        const tablaBody = document.getElementById('tablaPersonal');
        tablaBody.innerHTML = ''; // Limpiar contenido anterior

        // Calcular el total de páginas con los datos filtrados
        const totalPaginas = Math.ceil(datos.length / registrosPorPagina);
        if (totalPaginas === 0) {
            tablaBody.innerHTML = '<tr><td colspan="5" class="text-danger">No se encontraron resultados.</td></tr>';
            return;
        }

        const inicio = (paginaActual - 1) * registrosPorPagina;
        const fin = inicio + registrosPorPagina;
        const datosPaginados = datos.slice(inicio, fin);

        // Rellenar las filas con los datos filtrados
        datosPaginados.forEach(persona => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${persona.Pk_NumeroEmpleado}</td>
                <td>${persona.Nombre}</td>
                <td>${persona.RFC}</td>
                <td>${persona.Estatus}</td>
                <td> 
                ${userRole == 1 || userRole == 3  ? `<a href="/INFORMATICA/src/Models/" target="_blank" class="btn btn-success">Editar</a>` : ""}           
                </td>
            `;
            tablaBody.appendChild(fila);
        });

        generarNavegacionConDatos(datos.length);
    }

    // Función para generar la navegación de páginas
    function generarNavegacion() {
        const navegacionDiv = document.getElementById('navegacionPaginas');
        navegacionDiv.innerHTML = ''; // Limpiar contenido anterior

        const totalPaginas = Math.ceil(datosPersonal.length / registrosPorPagina);
        const lista = document.createElement('ul');
        lista.className = 'pagination justify-content-center';

        if (paginaActual > 1) {
            lista.appendChild(crearElementoPaginacion('Anterior', paginaActual - 1));
        }

        const rangoVisible = 3;
        let inicioPagina = Math.max(1, paginaActual - rangoVisible);
        let finPagina = Math.min(totalPaginas, paginaActual + rangoVisible);

        for (let i = inicioPagina; i <= finPagina; i++) {
            lista.appendChild(crearElementoPaginacion(i, i, i === paginaActual));
        }

        if (finPagina < totalPaginas) {
            lista.appendChild(crearElementoInactivo('...'));
        }

        if (paginaActual < totalPaginas) {
            lista.appendChild(crearElementoPaginacion('Siguiente', paginaActual + 1));
        }

        navegacionDiv.appendChild(lista);
    }

    // Función para generar la navegación con datos filtrados
    function generarNavegacionConDatos(totalDatos) {
        const navegacionDiv = document.getElementById('navegacionPaginas');
        navegacionDiv.innerHTML = ''; // Limpiar contenido anterior

        const totalPaginas = Math.ceil(totalDatos / registrosPorPagina);
        const lista = document.createElement('ul');
        lista.className = 'pagination justify-content-center';

        if (paginaActual > 1) {
            lista.appendChild(crearElementoPaginacion('Anterior', paginaActual - 1));
        }

        const rangoVisible = 3;
        let inicioPagina = Math.max(1, paginaActual - rangoVisible);
        let finPagina = Math.min(totalPaginas, paginaActual + rangoVisible);

        for (let i = inicioPagina; i <= finPagina; i++) {
            lista.appendChild(crearElementoPaginacion(i, i, i === paginaActual));
        }

        if (finPagina < totalPaginas) {
            lista.appendChild(crearElementoInactivo('...'));
        }

        if (paginaActual < totalPaginas) {
            lista.appendChild(crearElementoPaginacion('Siguiente', paginaActual + 1));
        }

        navegacionDiv.appendChild(lista);
    }

    // Crear un elemento de paginación activo
    function crearElementoPaginacion(texto, pagina, activo = false) {
        const elemento = document.createElement('li');
        elemento.className = `page-item ${activo ? 'active' : ''}`;
        elemento.innerHTML = `<a class="page-link" href="#" onclick="cambiarPagina(${pagina})">${texto}</a>`;
        return elemento;
    }

    // Crear un elemento inactivo
    function crearElementoInactivo(texto) {
        const elemento = document.createElement('li');
        elemento.className = 'page-item disabled';
        elemento.innerHTML = `<span class="page-link">${texto}</span>`;
        return elemento;
    }

    // Cambiar de página
    function cambiarPagina(pagina) {
        paginaActual = pagina;
        filtrarPersonal(); // Aplicar el filtro al cambiar de página
    }

    // Cargar los datos de personal al iniciar
    obtenerPersonal();
</script>