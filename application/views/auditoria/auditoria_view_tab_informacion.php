<div class="row">
    <div class="col-xs-12 col-sm-6">
        <div class="card">
            <h3 class="card-header bg-info">Datos de la auditoría: <strong><?= $auditoria['auditorias_rubro']; ?></strong></h3>
            <div class="card-block">
                <dl class="row">
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
                    <dd class="col-sm-9">
                        <p><?= ucfirst($auditoria['auditorias_objetivo']); ?></p>
                    </dd>
                    <dt class="col-sm-3">Tipo</dt>
                    <dd class="col-sm-9"><?= $auditoria['auditorias_tipos_nombre'] . " (" . $auditoria['auditorias_tipos_siglas'] . ")"; ?></dd>
                    <dt class="col-sm-3 text-truncate">Área responsable</dt>
                    <dd class="col-sm-9"><?= $auditoria['auditorias_areas_siglas']; ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6">
        <div class="card">
            <h3 class="card-header bg-info">Observaciones</h3>
            <div class="card-block">
                <?php if ($auditoria['auditorias_status_id'] == AUDITORIAS_STATUS_EN_PROCESO): // Esta en proceso ?>
                    <p><input type="checkbox" class="labelautyfy" name="auditorias_is_sin_observaciones" id="auditorias_is_sin_observaciones" data-labelauty="Sin observaciones"></p>

                <?php elseif ($auditoria['auditorias_is_sin_observaciones'] == 1): ?>
                    <p class="lead text-xs-center">SIN OBSERVACIONES</p>
                <?php endif; ?>
                <?php if (isset($auditoria['observaciones']) && count($auditoria['observaciones']) > 0): ?>
                    <ul class="list-group">
                        <?php foreach ($auditoria['observaciones'] as $o): ?>
                            <?php if ($o['observaciones_titulo'] !== 'SIN OBSERVACIONES'): ?>
                                <li class="list-group-item"><?= $o['observaciones_titulo']; ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
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
                            <td class="text-xs-center"><?= mysqlDate2OnlyDate($auditoria['auditorias_fechas_inicio_programado']); ?></td>
                            <td class="text-xs-center"><?= mysqlDate2OnlyDate($auditoria['auditorias_fechas_fin_programado']); ?></td>
                        </tr>
                        <tr>
                            <th>Reales</th>
                            <td class="text-xs-center"><?= mysqlDate2OnlyDate($auditoria['auditorias_fechas_inicio_real']); ?></td>
                            <td class="text-xs-center"><?= mysqlDate2OnlyDate($auditoria['auditorias_fechas_fin_real']); ?></td>
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
                        if (!empty($auditoria['auditoria_equipo'])) {
                            $aux = array_column($auditoria['auditoria_equipo'], 'nombre_completo');
                            $aux = array_map('capitalizar', $aux);
                            implode('<br>', $aux);
                        } else {
                            echo "No hay auditores de apoyo";
                        }
                        ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>