<form id="frmReportes">
    <div class="row mb-1">
        <div class="d-none d-sm-block col-sm-3 col-md-4"></div>
        <div class="col-xs-6 col-sm-3 col-md-2">
            <p class="text-xs-center">
                <select id="anio" name="anio" class="form-control">
                </select>
            </p>
        </div>
        <div class="col-xs-6 col-sm-3 col-md-2">
            <p class="text-xs-center">
                <select id="status" name="status" class="form-control">
                    <option value="0">Todas las auditorías</option>
                    <option value="1">Auditorias abiertas</option>
                    <option value="2">Auditorias cerradas</option>
                </select>
            </p>
        </div>
        <div class="d-none d-sm-block col-sm-3 col-md-4"></div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="card text-center">
                <div class="card-block">
                    <input type="checkbox" name="campos[]" value="numero_auditoria" data-labelauty="Número de auditoría" checked="checked" />
                    <input type="checkbox" name="campos[]" value="direcciones_nombre" data-labelauty="Dirección" title="Nombre de la Unidad Administrativa" checked="checked" />
                    <input type="checkbox" name="campos[]" value="auditorias_rubro" data-labelauty="Rubro" checked="checked" />
                    <input type="checkbox" name="campos[]" value="auditor_lider_nombre_completo" data-labelauty="Auditor líder" checked="checked" title="Nombre completo del auditor líder de la auditoría" />
                    <input type="checkbox" name="campos[]" value="auditorias_status_nombre" data-labelauty="Status Auditoría" />
                    <input type="checkbox" name="campos[]" value="auditorias_fechas_sello_orden_entrada" data-labelauty="Fecha Sello OA" />
                    <input type="checkbox" name="campos[]" value="auditorias_fechas_vobo_jefe" data-labelauty="Fecha VoBo Jefe" />
                    <input type="checkbox" name="campos[]" value="auditorias_fechas_vobo_subdirector" data-labelauty="Fecha VoBo Subdirector" />
                    <input type="checkbox" name="campos[]" value="auditorias_fechas_vobo_director" data-labelauty="Fecha VoBo Director" />
                    <input type="checkbox" name="campos[]" value="auditorias_fechas_lectura" data-labelauty="Fecha Lectura ARA" />
                    <input type="checkbox" name="campos[]" value="auditorias_fechas_recibir_informacion_etapa_1" data-labelauty="Fecha Recepción información" />
                    <input type="checkbox" name="campos[]" value="auditorias_fechas_vobo_jefe_etapa_1" data-labelauty="Fecha VoBo Jefe (Solventación)" />
                    <input type="checkbox" name="campos[]" value="auditorias_fechas_vobo_subdirector_etapa_1" data-labelauty="Fecha VoBo Subdirector (Solventación)" />
                    <input type="checkbox" name="campos[]" value="auditorias_fechas_vobo_director_etapa_1" data-labelauty="Fecha VoBo Director (Solventación)" />
                    <input type="checkbox" name="campos[]" value="auditorias_fechas_lectura_etapa_1" data-labelauty="Fecha Lectura ARR" />
                    <hr>
                    <input type="checkbox" name="campos[]" value="observaciones_total" data-labelauty="Total de observaciones" />
                    <input type="checkbox" name="campos[]" value="observaciones_titulo" data-labelauty="Título de la observación" />
                    <input type="checkbox" name="campos[]" value="observaciones_solventadas" data-labelauty="Observaciones solventadas" />
                    <input type="checkbox" name="campos[]" value="observaciones_no_solventadas" data-labelauty="Observaciones NO solventadas" />
                    <input type="checkbox" name="campos[]" value="recomendaciones_por_observacion" data-labelauty="Recomendaciones por observación" />
                </div>
            </div>
        </div>
    </div>
</form>
<div class="row">
    <p class="text-xs-center"><a href="#" class="btn btn-primary btn-descargar" reporte="custom">Descargar</a></p>
</div>
<!--
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
                <p class="card-text">Muestra las fechas de autorización del Jefe, Subdirector y Director de todas las etapas de la auditoría</p>
                <a href="#" class="btn btn-primary btn-descargar" reporte="fechas-autorizacion">Descargar</a>
            </div>
        </div>
    </div>
</div>
-->
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
    input.labelauty + label {
        display: inline-block !important;
    }
    input.labelauty + label > span.labelauty-unchecked { color: gray;}
    input.labelauty + label > span.labelauty-checked { }
</style>
<!-- moments.js -->
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/min/moment.min.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/locale/es.js" type="text/javascript"></script>
<!-- DateRangePicker -->
<link href="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker_2.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker_2.js" type="text/javascript"></script>
<!-- Labelauty -->
<link href="<?= APP_SAC_URL; ?>resources/plugins/jquery-labelauty/source/jquery-labelauty.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/jquery-labelauty/source/jquery-labelauty.js" type="text/javascript"></script>
<!-- Personalizado -->
<script src="<?= base_url(); ?>resources/scripts/reportes_view.js" type="text/javascript"></script>