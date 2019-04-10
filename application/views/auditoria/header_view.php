<?php
$cysa = $this->session->userdata('cysa');
$mis_auditorias_anio = $this->Auditoria_model->get_anios_para_select();
$anio = intval(date("Y"));
if (!isset($cysa['auditorias_anio']) && empty($cysa['auditorias_anio']) && !empty($mis_auditorias_anio)) {
    if (!empty($mis_auditorias_anio['en_proceso'])) {
        $anio = $mis_auditorias_anio['en_proceso'][0]['auditorias_anio'];
    } elseif (!empty($mis_auditorias_anio['finalizadas'])) {
        $anio = 0 - $mis_auditorias_anio['finalizadas'][0]['auditorias_anio'];
    }
    $this->{$this->module['controller'] . "_model"}->actualizar_session('auditorias_anio', intval($anio));
}
if (isset($cysa['auditorias_anio']) && !empty($cysa['auditorias_anio'])) {
    $anio = $cysa['auditorias_anio'];
}
if (isset($cysa['auditorias_id']) && !empty($cysa['auditorias_id'])) {
    $data['auditoria'] = $this->Auditorias_model->get_auditoria($cysa['auditorias_id']);
}
$auditorias = $this->Auditoria_model->get_mis_auditorias(abs($anio));
$tipo_auditoria_AP = array(1, 2, 3);
$APs = $ICs = array();
foreach ($auditorias as $a) {
    if (in_array($a['auditorias_tipo'], $tipo_auditoria_AP)) {
        array_push($APs, $a);
    } else {
        array_push($ICs, $a);
    }
}
$mis_auditorias_id = array(
    'auditorias_AP' => $APs,
    'auditorias_IC' => $ICs
);
$auditorias_id = isset($this->session->cysa['auditorias_id']) ? $this->session->cysa['auditorias_id'] : NULL;
$anio_select = isset($this->session->cysa['auditorias_anio']) ? $this->session->cysa['auditorias_anio'] : NULL;
?>
<div class="col-xs-6 col-sm-4 col-md-3 pull-right hidden-xs-down">
    <select name="mis_auditorias_id" class="mis_auditorias_id form-control">
        <?php if (empty($auditorias_id)): ?>
            <option value="0">SELECCIONE</option>
        <?php endif; ?>
        <?php foreach ($mis_auditorias_id as $tipos => $aa): ?>
            <?php if ($tipos === 'auditorias_AP' && count($aa) > 0): ?>
                <optgroup label="AUDITORIÁS (AP/AE/SA)">
                    <?php foreach ($aa as $r): ?>
                        <?php if ($r['auditorias_status_id'] > 0): ?>
                            <option value="<?= $r['auditorias_id']; ?>" <?= $auditorias_id == $r['auditorias_id'] ? 'selected="selected"' : ''; ?> title="<?= $r['auditorias_objetivo'] ?>"><?= $r['numero_auditoria']; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </optgroup>
            <?php endif; ?>
            <?php if ($tipos === 'auditorias_IC' && count($aa) > 0): ?>
                <optgroup label="INTERVENCIÓN DE CONTROL">
                    <?php foreach ($aa as $r): ?>
                        <?php if ($r['auditorias_status_id'] > 0): ?>
                            <option value="<?= $r['auditorias_id']; ?>" <?= $auditorias_id == $r['auditorias_id'] ? 'selected="selected"' : ''; ?> title="<?= $r['auditorias_objetivo'] ?>"><?= $r['numero_auditoria']; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </optgroup>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>
</div>
<div class="col-xs-6 col-sm-2 pull-right hidden-xs-down">
    <select name="mis_auditorias_anio" class="mis_auditorias_anio form-control">
        <?php if (isset($mis_auditorias_anio['en_proceso']) && count($mis_auditorias_anio['en_proceso']) > 0): ?>
            <optgroup label="EN PROCESO">
                <?php foreach ($mis_auditorias_anio['en_proceso'] as $r): ?>
                    <option value="<?= $r['auditorias_anio']; ?>" <?= $r['auditorias_anio'] == $anio_select ? 'selected="selected"' : ''; ?>><?= $r['auditorias_anio']; ?></option>
                <?php endforeach; ?>
            </optgroup>
        <?php endif; ?>
        <?php if (isset($mis_auditorias_anio['finalizadas']) && count($mis_auditorias_anio['finalizadas']) > 0): ?>
            <optgroup label="FINALIZADAS">
                <?php foreach ($mis_auditorias_anio['finalizadas'] as $r): ?>
                    <option value="-<?= $r['auditorias_anio']; ?>" <?= gmp_neg($r['auditorias_anio']) == $anio_select ? 'selected="selected"' : ''; ?>><?= $r['auditorias_anio']; ?></option>
                <?php endforeach; ?>
            </optgroup>
        <?php endif; ?>
    </select>
</div>
<h3><?= !empty($auditorias_id) ? $this->module['title_list'] : 'Seleccione auditoría'; ?></h3>
<div class="row hidden-sm-up">
    <div class="col-xs-6 col-sm-2">
        <select name="mis_auditorias_anio" class="mis_auditorias_anio form-control">
            <?php if (isset($mis_auditorias_anio['en_proceso']) && count($mis_auditorias_anio['en_proceso']) > 0): ?>
                <optgroup label="EN PROCESO">
                    <?php foreach ($mis_auditorias_anio['en_proceso'] as $r): ?>
                        <option value="<?= $r['auditorias_anio']; ?>" <?= $r['auditorias_anio'] == $anio_select ? 'selected="selected"' : ''; ?>><?= $r['auditorias_anio']; ?></option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endif; ?>
            <?php if (isset($mis_auditorias_anio['finalizadas']) && count($mis_auditorias_anio['finalizadas']) > 0): ?>
                <optgroup label="FINALIZADAS">
                    <?php foreach ($mis_auditorias_anio['finalizadas'] as $r): ?>
                        <option value="-<?= $r['auditorias_anio']; ?>" <?= gmp_neg($r['auditorias_anio']) == $anio_select ? 'selected="selected"' : ''; ?>><?= $r['auditorias_anio']; ?></option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endif; ?>
        </select>
    </div>
    <div class="col-xs-6 col-sm-4 col-md-3">
        <select name="mis_auditorias_id" class="mis_auditorias_id form-control">
            <option value="0">SELECCIONE</option>
            <?php foreach ($mis_auditorias_id as $r): ?>
                <?php if (isset($r['auditorias_status_id']) && $r['auditorias_status_id'] > 0): ?>
                    <option value="<?= $r['auditorias_id']; ?>" <?= $auditorias_id == $r['auditorias_id'] ? 'selected="selected"' : ''; ?> title="<?= $r['auditorias_objetivo'] ?>"><?= $r['numero_auditoria']; ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<script src="<?= base_url(); ?>resources/scripts/header_view.js" type="text/javascript"></script>