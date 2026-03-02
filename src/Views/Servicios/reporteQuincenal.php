<?php
$meses = [
  1=>'ENERO',2=>'FEBRERO',3=>'MARZO',4=>'ABRIL',5=>'MAYO',6=>'JUNIO',
  7=>'JULIO',8=>'AGOSTO',9=>'SEPTIEMBRE',10=>'OCTUBRE',11=>'NOVIEMBRE',12=>'DICIEMBRE'
];

$anio = $_GET['anio'] ?? date('Y');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center my-3">
        <a href="?page=reporteQuincenal&anio=<?= $anio-1 ?>" class="btn btn-outline-secondary">⬅ <?= $anio-1 ?></a>
        <h3>Reporte Anual por Quincena – <?= $anio ?></h3>
        <a href="?page=reporteQuincenal&anio=<?= $anio+1 ?>" class="btn btn-outline-secondary"><?= $anio+1 ?> ➡</a>
    </div>

    <table class="table table-bordered table-sm text-center table-hover small" id="tablaQuincenal">
        <thead class="thead-quincenal">
            <tr>
                <th rowspan="2">Equipo</th>
                <?php foreach ($meses as $mes): ?>
                    <th colspan="2"><?= $mes ?></th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <?php foreach ($meses as $mes): ?>
                    <th>1ra Q</th>
                    <th>2da Q</th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <!-- JS inserta filas aquí -->
        </tbody>
    </table>
</div>

<script>
const anio = <?= $anio ?>;

fetch(`./src/Models/Servicios/obtener_videos_quincena.php?anio=${anio}`)
  .then(r => r.json())
  .then(datos => {

    const tabla = {};

    datos.forEach(row => {
        if (!tabla[row.Equipo]) tabla[row.Equipo] = {};
        if (!tabla[row.Equipo][row.Mes]) tabla[row.Equipo][row.Mes] = {};
        tabla[row.Equipo][row.Mes][row.Quincena] = row.CantidadVideos;
    });

    const tbody = document.querySelector('#tablaQuincenal tbody');
    tbody.innerHTML = '';

    Object.keys(tabla).forEach(equipo => {
        let tr = `<tr><th class="tabla-equipo">${equipo}</th>`;

        for (let m = 1; m <= 12; m++) {
            tr += `<td>${tabla[equipo]?.[m]?.[1] ?? 0}</td>`;
            tr += `<td>${tabla[equipo]?.[m]?.[2] ?? 0}</td>`;
        }

        tr += '</tr>';
        tbody.insertAdjacentHTML('beforeend', tr);
    });
  });
</script>

<script>
const tabla = document.getElementById('tablaQuincenal');

tabla.addEventListener('mouseover', e => {
    const cell = e.target.closest('td, th');
    if (!cell) return;

    const colIndex = cell.cellIndex;

    tabla.querySelectorAll('tr').forEach(row => {
        if (row.cells[colIndex]) {
            row.cells[colIndex].classList.add('col-hover');
        }
    });
});

tabla.addEventListener('mouseout', () => {
    tabla.querySelectorAll('.col-hover').forEach(c =>
        c.classList.remove('col-hover')
    );
});
</script>