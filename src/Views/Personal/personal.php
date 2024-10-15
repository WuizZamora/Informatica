    <div class="container">
        <h1 class="text-center">Personal</h1>
        <p class="text-center">Contenido relacionado con el personal...</p>

        <!-- Tabla de Personal -->
        <div class="table-responsive">
            <table class="table table-striped-columns table-hover text-center">
                <thead class="table-success">
                    <tr>
                        <th>NÚMERO DE EMPLEADO</th>
                        <th>NOMBRE</th>
                        <th>RFC</th>
                        <th>Estatus</th>
                    </tr>
                </thead>
                <tbody id="tablaPersonal"></tbody>
            </table>
        </div>
        <nav id="navegacionPaginas" aria-label="Page navigation" class="mt-4"></nav>
    </div>

    <script>
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
                `;
                tablaBody.appendChild(fila);
            });

            generarNavegacion();
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
            mostrarPagina(paginaActual);
        }

        // Cargar los datos de personal al iniciar
        obtenerPersonal();
    </script>