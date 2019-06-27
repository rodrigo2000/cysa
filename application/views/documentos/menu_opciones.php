<?php if (!$is_finalizada): ?>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Nuevo</button>
        <div class="dropdown-menu" style="height: 300px; overflow: auto;">
            <?php foreach ($direcciones as $d): ?>
                <a class="dropdown-item" href="<?= base_url() . $this->module['controller'] . "/documento/" . $this->uri->segment(3) . "/nuevo/" . $d['direcciones_id']; ?>"><?= $d['direcciones_nombre_cc']; ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php if (!$documento_autorizado): ?>
        <button type="button" class="btn btn-primary boton_guardar m-l-2"><?= $etiquetaBoton; ?></button>
        <?php if ($this->{$this->module['controller'] . "_model"}->tengo_permiso(PERMISOS_AUTORIZAR_DOCUMENTO)): ?>
            <a id="btn-autorizar" href="<?= $this->module['autorizar_url'] . (isset($documento['documentos_id']) ? '/' . $documento['documentos_id'] : ''); ?>" class="actualizar_id btn btn-default btn-warning m-l-2 <?= $hidden; ?>">Autorizar</a>
        <?php endif; ?>
    <?php else: ?>
        <?php if ($this->{$this->module['controller'] . "_model"}->tengo_permiso(PERMISOS_DESAUTORIZAR_DOCUMENTO)): ?>
            <a id="btn-autorizar" href="<?= $this->module['desautorizar_url'] . (isset($documento['documentos_id']) ? '/' . $documento['documentos_id'] : ''); ?>" class="actualizar_id btn btn-default btn-danger m-l-2">Desautorizar</a>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
<div id="btn-vista-impresion" class="btn-group actualizar_id <?= $hidden; ?>">
    <a href="<?= base_url() . $this->module['controller'] . "/" . ($documento_autorizado ? 'imprimir' : 'descargar') . (isset($documento['documentos_id']) ? '/' . $documento['documentos_id'] : ''); ?>" class="actualizar_id btn btn-info m-l-2" target="_blank">Imprimir</a>
    <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <div class="dropdown-menu">
        <a class="dropdown-item actualizar_id" href="<?= base_url() . $this->module['controller'] . "/word" . (isset($documento['documentos_id']) ? '/' . $documento['documentos_id'] : ''); ?>" target="_blank">Word</a>
        <a class="dropdown-item actualizar_id" href="<?= base_url() . $this->module['controller'] . "/pdf" . (isset($documento['documentos_id']) ? '/' . $documento['documentos_id'] : ''); ?>"  target="_blank">PDF</a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item actualizar_id" href="<?= base_url() . $this->module['controller'] . "/html" . (isset($documento['documentos_id']) ? '/' . $documento['documentos_id'] : ''); ?>" target="_blank">HTML</a>
    </div>
</div>
<?php if (!$is_finalizada && count($documentos) > 1): ?>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-default dropdown-toggle m-l-2" data-toggle="dropdown">Oficios</button>
        <div class="dropdown-menu">
            <?php foreach ($documentos as $d): ?>
                <?php $direccion = $this->SAC_model->get_direccion($d['valores'][ORD_ENT_ID_DIR_AUDIT]); ?>
                <a style="margin-right:20px;" class="dropdown-item" href="<?= base_url() . $this->module['controller'] . "/documento/" . $this->uri->segment(3) . "/" . $d['documentos_id']; ?>"><?= ($documento['documentos_id'] == $d['documentos_id'] ? '<i class="fa fa-check"></i> ' : '<i style="padding-left:16px;"></i> ') . $direccion['direcciones_nombre']; ?> <span class="badge badge-primary badge-pill bg-red pull-right"><?= $d['documentos_id']; ?></span></a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
<a id="btn-regresar" class="btn btn-default m-l-2" href="<?= base_url() . $this->uri->segment(1) . "/" . $auditoria['auditorias_id']; ?>#tab-documentos">Regresar</a>
<a id="btn-eliminar" class="btn btn-danger m-l-2 actualizar_id <?= $hidden; ?>" href="<?= base_url() . "Documentos/eliminar" . (isset($documento['documentos_id']) ? '/' . $documento['documentos_id'] : ''); ?>">Eliminar</a>
