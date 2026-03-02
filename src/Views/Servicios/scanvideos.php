<?php
$data = $data ?? [];

$total = $data['total'] ?? 0;
$lista = $data['lista'] ?? [];
$videosCortos = $data['videos_cortos'] ?? 0;

?>

<div id="loader" class="text-center my-4 d-none">
    <div class="spinner-border text-primary" role="status"></div>
    <p class="mt-2 fw-bold">Escaneando videos, por favor espera…</p>
</div>

<div class="container mt-4">

    <div class="row g-3 mb-3">

        <!-- TIPO -->
        <div class="col-md-4">
            <label class="form-label">Tipo</label>
            <select class="form-select" id="tipo" onchange="cargarEquipos()">
                <option value="">Seleccione</option>
                <option value="ALCALDIAS">Alcaldías</option>
                <option value="TRANSPORTE">Transporte</option>
                <option value="AMBITO">Ámbito Central</option>
            </select>
        </div>

        <!-- EQUIPO -->
        <div class="col-md-4">
            <label class="form-label">Equipo</label>
            <select class="form-select" id="equipo">
                <option value="">Seleccione tipo primero</option>
            </select>
        </div>

        <!-- MES -->
        <div class="col-md-4">
            <label class="form-label">Mes</label>
            <select class="form-select" id="mes">
                <option value="">Seleccione</option>
                <option value="01 ENERO">Enero</option>
                <option value="02 FEBRERO">Febrero</option>
                <option value="03 MARZO">Marzo</option>
                <option value="04 ABRIL">Abril</option>
                <option value="05 MAYO">Mayo</option>
                <option value="06 JUNIO">Junio</option>
                <option value="07 JULIO">Julio</option>
                <option value="08 AGOSTO">Agosto</option>
                <option value="09 SEPTIEMBRE">Septiembre</option>
                <option value="10 OCTUBRE">Octubre</option>
                <option value="11 NOVIEMBRE">Noviembre</option>
                <option value="12 DICIEMBRE">Diciembre</option>
            </select>
        </div>

        <!-- QUINCENA -->
        <div class="col-md-4">
            <label class="form-label">Quincena</label>
            <select class="form-select" id="quincena">
                <option value="">Seleccione</option>
                <option value="1">1ra Quincena</option>
                <option value="2">2da Quincena</option>
            </select>
        </div>
    </div>

    <div class="text-center">
        <button class="btn btn-primary" onclick="escaniar()">Escanear videos</button>
    </div>
</div>


<?php if (!empty($data['lista'])): ?>
<details class="mt-3">
    <summary class="fw-bold">
        📂 Ver lista de videos (<?= count($data['lista']) ?>)
    </summary>

    <ul class="list-group list-group-flush mt-2">
        <?php foreach ($data['lista'] as $video): 
        $segundos = (int) round($video['duracion']);
        $minutos = floor($segundos / 60);
        $resto   = $segundos % 60;
        $tiempoFormateado = sprintf('%02d:%02d', $minutos, $resto);
        ?>
            <li class="list-group-item small d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    🎬<?= $video['ruta_unc'] ?> 
                </div>

                <?php if ($video['es_corto']): ?>
                    <span class="badge bg-danger">
                        <?= $video['duracion'] ?> s
                    </span>
                <?php else: ?>
                    <span class="badge bg-success">
                        <?= $tiempoFormateado ?> m
                    </span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</details>
<?php endif; ?>


<script>
    const equipos = {
        ALCALDIAS: [
            "ALVARO OBREGON","AZCAPOTZALCO","BENITO JUAREZ","COYOACAN","CUAUHTEMOC",
            "GUSTAVO A. MADERO","IZTACALCO","IZTAPALAPA","MAGDALENA CONTRERAS",
            "MIGUEL HIDALGO","MILPA ALTA","TLAHUAC","TLALPAN",
            "VENUSTIANO CARRANZA","XOCHIMILCO"
        ],
        TRANSPORTE: [
            "TRANSPORTE EQUIPO 1","TRANSPORTE EQUIPO 2","TRANSPORTE EQUIPO 3",
            "TRANSPORTE EQUIPO 4","TRANSPORTE EQUIPO 5"
        ],
        AMBITO: [
            "INVEA I","INVEA II","INVEA III",
            "INVEA IV","INVEA V","INVEA VI"
        ]
    };

    function cargarEquipos() {
        const tipo = document.getElementById('tipo').value;
        const select = document.getElementById('equipo');

        select.innerHTML = '<option value="">Seleccione</option>';

        if (!equipos[tipo]) return;

        equipos[tipo].forEach(e => {
            let opt = document.createElement('option');
            opt.value = e;
            opt.textContent = e;
            select.appendChild(opt);
        });
    }

    function escaniar() {
        const tipo = document.getElementById('tipo').value;
        const equipo = document.getElementById('equipo').value;
        const mes = document.getElementById('mes').value;
        const quincena = document.getElementById('quincena').value;

        if (!tipo || !equipo || !mes || !quincena) {
            alert('Selecciona todos los campos');
            return;
        }

        document.getElementById('loader').classList.remove('d-none');

        setTimeout(() => {
            window.location.href =
                `?page=scanvideos`
                + `&tipo=${tipo}`
                + `&equipo=${encodeURIComponent(equipo)}`
                + `&mes=${encodeURIComponent(mes)}`
                + `&quincena=${quincena}`;
        }, 100);
    }
</script>


