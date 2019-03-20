<div id="iconografia" class="row">
    <div class="col-xs-12 col-sm-6">
	<div class="p-t-2 p-b-2 clearfix">
	    <div class="timeline-icon bg-success text-white">
		<i class="material-icons">check</i>
	    </div>
	    <div class="overflow-hidden">
		<p class="m-b-0"><strong>FINALIZADO</strong></p>
		<p class="m-b-0">Indica que la fecha en que se ejecut&oacute; la tarea se encuentra en el tiempo establecido.</p>
	    </div>
	</div>
	<div class="p-t-2 p-b-2 clearfix">
	    <div class="timeline-icon bg-info text-white">
		<i class="material-icons">check</i>
	    </div>
	    <div class="overflow-hidden">
		<p class="m-b-0"><strong>INFORMATIVO</strong></p>
		<p class="m-b-0">Esta tarea no requiere de captura por parte del usuario, es informativo.</p>
	    </div>
	</div>
	<div class="p-t-2 p-b-2 clearfix">
	    <div class="timeline-icon bg-default text-white">
		<i class="material-icons">more_horiz</i>
	    </div>
	    <div class="overflow-hidden">
		<p class="m-b-0"><strong>PENDIENTE</strong></p>
		<p class="m-b-0">Indica que la tarea se encuentra pendiente por realizar. Este icono se muestra porque la fecha programada de la tarea es posterior al d&iacute;a de hoy.</p>
	    </div>
	</div>
    </div>
    <div class="col-xs-12 col-sm-6">
	<div class="p-t-2 p-b-2 clearfix">
	    <div class="timeline-icon bg-danger text-white">
		<i class="material-icons">close</i>
	    </div>
	    <div class="overflow-hidden">
		<p class="m-b-0"><strong>RETRASADO</strong></p>
		<p class="m-b-0">Indica que la fecha en que se ejecut&oacute; la tarea se encuentra despu&eacute;s del tiempo establecido.</p>
	    </div>
	</div>
	<div class="p-t-2 p-b-2 clearfix">
	    <div class="timeline-icon bg-danger text-white">
		<i class="material-icons">help_outline</i>
	    </div>
	    <div class="overflow-hidden">
		<p class="m-b-0"><strong>SIN FECHA CAPTURADA</strong></p>
		<p class="m-b-0">Indica que no se tiene capturada la fecha en la que se realiz&oacute; la tarea.</p>
	    </div>
	</div>
	<div class="p-t-2 p-b-2 clearfix">
	    <div class="timeline-icon bg-purple-darker">
		<i class="material-icons">flag</i>
	    </div>
	    <div class="overflow-hidden">
		<p class="m-b-0"><strong>INICIO DE AUDITORIA</strong></p>
		<p class="m-b-0">Indica el inicio de la Auditor&iacute;a.</p>
	    </div>
	</div>
	<div class="p-t-2 p-b-2 clearfix">
	    <div class="timeline-icon bg-purple-darker">
		<i class="material-icons">star</i>
	    </div>
	    <div class="overflow-hidden">
		<p class="m-b-0"><strong>FIN DE AUDITORIA</strong></p>
		<p class="m-b-0">Indica la finalizaci&oacute;n de la Auditor&iacute;a.</p>
	    </div>
	</div>
    </div>
</div>
<style>
    #iconografia {
	/*display:flex;*/
    }
    #iconografia .timeline-icon {
	float:left;
	margin-right: 10px;
	width: 40px;
	height: 40px;
	text-align: center;
	border-radius: 50%;
	box-shadow: 0 0 0 2px #fff;
    }
    #iconografia .timeline-icon>i {
	line-height: 40px;
    }
</style>
