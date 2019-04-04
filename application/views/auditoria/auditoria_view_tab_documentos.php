<div class="card">
    <?php $this->load->view('auditoria/header_view'); ?>
    <div class="card-block">
        <?php if (!empty($this->session->userdata(APP_NAMESPACE))): $auditorias_id = $this->session->userdata(APP_NAMESPACE)[$this->module['id_field']]; ?>
            <div id="accordion" role="tablist" aria-multiselectable="true">
                <div class="card panel panel-default m-b-xs">
                    <div class="card-header panel-heading" role="tab">
                        <h6 class="panel-title m-a-0">
                            <a data-toggle="collapse" data-parent="#accordion" href="#documentacion-inicial">
                                Documentantación inicial
                            </a>
                        </h6>
                    </div>
                    <div id="documentacion-inicial" class="card-block panel-collapse collapse show" role="tabpanel">
                        <div class="list-group">
                            <a href="<?= base_url() . $this->module['controller'] . "/documento/OA"; ?>" class="list-group-item">Orden de Auditoría (OA)</a>
                            <a href="#" class="list-group-item">Acta de inicio de Auditoría (AIA)</a>
                            <a href="#" class="list-group-item">Autirozación de Auditoría No Programada (AANP)</a>
                        </div>
                    </div>
                </div>
                <div class="card panel panel-default m-b-xs">
                    <div class="card-header panel-heading" role="tab">
                        <h6 class="panel-title m-a-0">
                            <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#reprogramaciones">Reprogramaciones</a>
                        </h6>
                    </div>
                    <div id="reprogramaciones" class="card-block panel-collapse collapse in" role="tabpanel">
                        <ul>
                            <li>Orden de Auditoría (OA)</li>
                            <li>Acta de inicio de Auditoría (AIA)</li>
                            <li>Autirozación de Auditoría No Programada (AANP)</li>
                        </ul>
                    </div>
                </div>
                <div class="card panel panel-default m-b-xs">
                    <div class="card-header panel-heading" role="tab">
                        <h6 class="panel-title m-a-0">
                            <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#ampliaciones">Ampliaciones</a>
                        </h6>
                    </div>
                    <div id="ampliaciones" class="card-block panel-collapse collapse in" role="tabpanel">
                        <ul>
                            <li>Orden de Auditoría (OA)</li>
                            <li>Acta de inicio de Auditoría (AIA)</li>
                            <li>Autirozación de Auditoría No Programada (AANP)</li>
                        </ul>
                    </div>
                </div>
                <div class="card panel panel-default m-b-xs">
                    <div class="card-header panel-heading" role="tab">
                        <h6 class="panel-title m-a-0">
                            <a data-toggle="collapse" data-parent="#accordion" href="#documentacion-final">
                                Documentantación final
                            </a>
                        </h6>
                    </div>
                    <div id="documentacion-inicial" class="card-block panel-collapse collapse in" role="tabpanel">
                        <ul>
                            <li>Oficio de Citatorio (OC)</li>
                            <li>Oficio de Envío de Documentos (OED)</li>
                            <li>Imprimir portada o guarda exterior</li>
                        </ul>
                    </div>
                </div>
                <div class="card panel panel-default m-b-xs">
                    <div class="card-header panel-heading" role="tab">
                        <h6 class="panel-title m-a-0">
                            <a data-toggle="collapse" data-parent="#accordion" href="#oficios-generales">
                                Oficios Generales
                            </a>
                        </h6>
                    </div>
                    <div id="documentacion-inicial" class="card-block panel-collapse collapse in" role="tabpanel">
                        <ul>
                            <li>Oficio de Resolución de Prórroga (ORP)</li>
                            <li>Oficio de Solicitud de Información (OSI)</li>
                            <li>Oficio de Resolución de Ampliación de Plazo (RAP)</li>
                        </ul>
                    </div>
                </div>
                <div class="card panel panel-default m-b-xs">
                    <div class="card-header panel-heading" role="tab">
                        <h6 class="panel-title m-a-0">
                            <a data-toggle="collapse" data-parent="#accordion" href="#actas">
                                Actas
                            </a>
                        </h6>
                    </div>
                    <div id="documentacion-inicial" class="card-block panel-collapse collapse in" role="tabpanel">
                        <ul>
                            <li>Acta de Resultados (ARA/ARR)</li>
                            <li>Acta de Cierre de Entrega de Información (ACEI)</li>
                            <li>Autirozación Administrativa (AA)</li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>