<div class="row mb-1">
    <div class="d-none d-sm-block col-sm-3 col-md-4"></div>
    <div class="col-xs-6 col-sm-3 col-md-2">
        <p class="text-xs-center">
            <select id="anio" class="form-control">
            </select>
        </p>
    </div>
    <div class="col-xs-6 col-sm-3 col-md-2">
        <p class="text-xs-center">
            <select id="status" class="form-control">
                <option value="0">Todas las auditorías</option>
                <option value="1">Auditorias abiertas</option>
                <option value="2">Auditorias cerradas</option>
            </select>
        </p>
    </div>
    <div class="d-none d-sm-block col-sm-3 col-md-4"></div>
</div>
<div class="row">
    <div class="col-sm-4">
        <div class="card text-center">
            <div class="card-block">
                <h4 class="card-title">Control de Auditoría</h4>
                <p class="card-text">Auditorías actualmente en proceso</p>
                <a href="#" class="btn btn-primary btn-descargar" reporte="control-auditorias">Descargar</a>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center">
            <div class="card-block">
                <h4 class="card-title">Programa Anual de Auditorías (PAA)</h4>
                <p class="card-text">Muestras todas las auditorías del año seleccionado.</p>
                <a href="#" class="btn btn-primary btn-descargar" reporte="paa">Descargar</a>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center">
            <div class="card-block">
                <h4 class="card-title">Fechas de Autorización</h4>
                <p class="card-text">Muestra las fechas de autorización del Jefe, Subdirector y Director de todas las etaas de la auditoría</p>
                <a href="#" class="btn btn-primary btn-descargar" reporte="fechas-autorizacion">Descargar</a>
            </div>
        </div>
    </div>
</div>
<!--<div class="row">
    <div class="col-sm-4">
        <div class="card text-center">
            <div class="card-block">
                <h4 class="card-title">Auditorías Finalizadas</h4>
                <p class="card-text">Muestra las auditorías finalidas por año.</p>
                <a href="#" class="btn btn-primary">Descargar</a>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center">
            <div class="card-block">
                <h4 class="card-title">Programa de actividades</h4>
                <p class="card-text">DETALLADO</p>
                <a href="#" class="btn btn-primary">Descargar</a>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center">
            <div class="card-block">
                <h4 class="card-title">Programa de actividades</h4>
                <p class="card-text"><input type="checkbox">Todos los CC</p>
                <a href="#" class="btn btn-primary">Descargar</a>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-4">
        <div class="card text-center">
            <div class="card-block">
                <h4 class="card-title">Relación de Auditorías realizadas por período</h4>
                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <a href="#" class="btn btn-primary">Descargar</a>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center">
            <div class="card-block">
                <h4 class="card-title">Reporte Lectura de Actas por Período</h4>
                <p class="card-text"><input type="checkbox">Todos los CC</p>
                <a href="#" class="btn btn-primary">Descargar</a>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center">
            <div class="card-block">
                <h4 class="card-title">Reporte LGT Art. 70<br>Fracc. XXIV</h4>
                <p class="card-text"><input type="checkbox">Todos los CC</p>
                <a href="#" class="btn btn-primary">Descargar</a>
            </div>
        </div>
    </div>
</div>-->
<style>
    .text-center { text-align: center; }
    /*    .card {height: 180px;}*/
</style>
<!-- moments.js -->
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/min/moment.min.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/locale/es.js" type="text/javascript"></script>
<!-- DateRangePicker -->
<link href="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker_2.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker_2.js" type="text/javascript"></script>
<!-- Personalizado -->
<script src="<?= base_url(); ?>resources/scripts/reportes_view.js" type="text/javascript"></script>