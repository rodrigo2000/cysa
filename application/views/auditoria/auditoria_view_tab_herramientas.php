<div id="rango-seleccionado" class="text-xs-center">
    <p class="lead">
        Selección del
        <strong><span id="calendario-fecha-inicio">ESPECIFIQUE FECHA INICIO</span></strong>
        al
        <strong><span id="calendario-fecha-fin">ESPECIFIQUE FECHA FIN</span></strong>
        (<span id="dias-naturales">0</span> naturales y <span id="dias-habiles">0</span> hábiles)
        <br>
        <label>
            <input type="checkbox" id="chk-incluir-primer-dia" value="1" checked="checked"> Incluir primer día
        </label>
    </p>
</div>
<div id="calendar" style="min-height: 486px;"></div>
<!-- Bootstrap Year Calendar -->
<link href="<?= APP_CYSA_URL; ?>resources/plugins/bootstrap-year-calendar/bootstrap-year-calendar.min.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_CYSA_URL; ?>resources/plugins/bootstrap-year-calendar/bootstrap-year-calendar.js" type="text/javascript"></script>
<script src="<?= APP_CYSA_URL; ?>resources/plugins/bootstrap-year-calendar/bootstrap-year-calendar.es.js" type="text/javascript"></script>

<!-- Moment Bussiness --><!-- Previamente se cargó momentjs -->
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/momentjs-business.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment-holiday/moment-holiday.js" type="text/javascript"></script>

<!-- Personalizado -->
<script src="<?= APP_CYSA_URL; ?>resources/scripts/auditorias_view_tab_herramientas.js" type="text/javascript"></script>
<link href="<?= APP_CYSA_URL; ?>resources/styles/auditorias_view_tab_calendario.css" rel="stylesheet" type="text/css"/>