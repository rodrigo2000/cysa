<div class="row">
    <div class="col-xs-12 col-sm-6">
        <div class="card">
            <h3 class="card-header bg-info">Datos de la auditoría</h3>
            <div class="card-block row">
                <dl>
                    <dt class="col-sm-3">Área auditada</dt>
                    <dd class="col-sm-9">
                        <?php
                        $aux = array(
                            capitalizar($auditoria['direcciones_nombre']),
                            capitalizar($auditoria['subdirecciones_nombre']),
                            capitalizar($auditoria['departamentos_nombre'])
                        );
                        echo implode('<br>', $aux);
                        ?>
                    </dd>
                    <dt class="col-sm-3">Objetivo</dt>
                    <dd class="col-sm-9"><?= ucfirst($auditoria['auditorias_objetivo']); ?></dd>
                    <dt class="col-sm-3">Tipo</dt>
                    <dd class="col-sm-9"><?= $auditoria['auditorias_tipos_nombre'] . " (" . $auditoria['auditorias_tipos_siglas'] . ")"; ?></dd>
                    <dt class="col-sm-3 text-truncate">Área responsable</dt>
                    <dd class="col-sm-9"><?= $auditoria['auditorias_areas_siglas']; ?></dd>
                    <?php if (!empty($auditoria['auditorias_origen_id'])): $aux = $this->Auditorias_model->get_auditoria($auditoria['auditorias_origen_id']); ?>
                        <?php if (!empty($aux)): ?>
                            <dt class="col-sm-3 bg-warning">Auditoría Origen</dt>
                            <dd class="col-sm-9 bg-warning"><a href="<?= base_url() . $this->module['controller'] . "/" . $aux['auditorias_id']; ?>"><?= $aux['numero_auditoria']; ?></a> <i class="fa fa-star faa-flash animated"></i></dd>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php $aux = $this->Auditoria_model->get_auditoria_de_seguimiento($auditoria['auditorias_id']); ?>
                    <?php if (!empty($aux)): ?>
                        <dt class="col-sm-3 bg-warning">Auditoría de Seguimiento</dt>
                        <dd class="col-sm-9 bg-warning"><a href="<?= base_url() . $this->module['controller'] . "/" . $aux['auditorias_id']; ?>"><?= $aux['numero_auditoria']; ?></a> <i class="fa fa-star faa-flash animated"></i></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6">
        <div class="card">
            <h3 class="card-header bg-info">Observaciones</h3>
            <div class="card-block">
                <?php if ($auditoria['auditorias_is_sin_observaciones'] == 0): // Es sin observaciones? ?>
                    <?php if ($auditoria['auditorias_status_id'] == AUDITORIAS_STATUS_EN_PROCESO): // Esta en proceso ?>
                        <p><input type="checkbox" class="labelautyfy" name="auditorias_is_sin_observaciones" id="auditorias_is_sin_observaciones" data-labelauty="Sin observaciones"></p>
                    <?php endif; ?>
                    <?php if (isset($auditoria['observaciones']) && count($auditoria['observaciones']) > 0): ?>
                        <div id="accordion" role="tablist" aria-multiselectable="true">
                            <div class="card m-b-0">
                                <?php if ($auditoria['observaciones'][0]['observaciones_auditorias_id'] !== $auditoria['auditorias_id']): ?>
                                    <?php $aa = $this->Auditorias_model->get_auditoria($auditoria['observaciones'][0]['observaciones_auditorias_id']); ?>
                                    <div class="card-header bg-warning" role="tab" id="headingOne">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                            Observaciones de la auditoría <?= $aa['numero_auditoria']; ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <div id="collapseOne" class="collapse" role="tabpanel">
                                    <div class="card-block">
                                        <ul class="list-group list-group-flush" id="observaciones">
                                            <?php foreach ($auditoria['observaciones'] as $o): ?>
                                                <?php if ($o['observaciones_titulo'] !== 'SIN OBSERVACIONES'): ?>
                                                    <li id="observacion_<?= $o['observaciones_id']; ?>" class="list-group-item" style="box-shadow: none;">
                                                        <?= $o['observaciones_numero'] . " - " . $o['observaciones_titulo']; ?>
                                                        <span class="badge badge-default badge-pill">14</span>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="lead text-xs-center">SIN OBSERVACIONES</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6">
        <div class="card">
            <h3 class="card-header bg-info">Fechas del proceso</h3>
            <div class="card-block">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <td></td>
                            <th>Inicio</th>
                            <th>Fin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Programadas</th>
                            <td class="text-xs-center"><?= !empty($auditoria['auditorias_fechas_inicio_programado']) ? mysqlDate2OnlyDate($auditoria['auditorias_fechas_inicio_programado']) : SIN_ESPECIFICAR; ?></td>
                            <td class="text-xs-center"><?= !empty($auditoria['auditorias_fechas_fin_programado']) ? mysqlDate2OnlyDate($auditoria['auditorias_fechas_fin_programado']) : SIN_ESPECIFICAR; ?></td>
                        </tr>
                        <tr>
                            <th>Reales</th>
                            <td class="text-xs-center"><?= !empty(mysqlDate2OnlyDate($auditoria['auditorias_fechas_sello_orden_entrada'])) ? mysqlDate2OnlyDate($auditoria['auditorias_fechas_sello_orden_entrada']) : SIN_ESPECIFICAR; ?></td>
                            <?php $fin_real = !empty($auditoria['auditorias_origen_id']) ? $auditoria['auditorias_fechas_vobo_director_etapa_1'] : $auditoria['auditorias_fechas_vobo_director']; ?>
                            <td class="text-xs-center"><?= !empty($fin_real) ? mysqlDate2OnlyDate($fin_real) : SIN_ESPECIFICAR; ?></td>
                        </tr>
                        <?php if ($auditoria['auditorias_fechas_inicio_programado'] != $auditoria['auditorias_fechas_inicio_real']): ?>
                            <tr>
                                <th>Reprogramadas</th>
                                <td class="text-xs-center">INVESTIGAR</td>
                                <td class="text-xs-center">INVESTIGAR</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6">
        <div class="card">
            <h3 class="card-header bg-info">Equipo de auditoría</h3>
            <div class="card-block">
                <dl class="row">
                    <dt class="col-sm-3">Auditor Líder</dt>
                    <dd class="col-sm-9"><?= capitalizar($auditoria['auditor_lider_nombre_completo']); ?></dd>
                    <dt class="col-sm-3">Equipo de auditoría</dt>
                    <dd class="col-sm-9">
                        <?php
                        $aux = "No hay auditores de apoyo";
                        if (!empty($auditoria['auditoria_equipo'])) {
                            $aux = array_column($auditoria['auditoria_equipo'], 'nombre_completo');
                            $aux = array_map('capitalizar', $aux);
                            $aux = implode('<br>', $aux);
                        }
                        echo $aux;
                        ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>