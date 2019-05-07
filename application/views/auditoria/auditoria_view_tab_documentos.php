<div class="card">
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
                            <a href="<?= $this->module['documentos_url'] . "/OA"; ?>" class="list-group-item">Orden de Auditoría (OA)</a>
                            <a href="<?= $this->module['documentos_url'] . "/AIA"; ?>" class="list-group-item">Acta de inicio de Auditoría (AIA)</a>
                            <a href="<?= $this->module['documentos_url'] . "/AANP"; ?>" class="list-group-item">Autirozación de Auditoría No Programada (AANP)</a>
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
                            <li>Reprogramación 1</li>
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
                            <li>Ampliación 1</li>
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
                    <div id="documentacion-final" class="card-block panel-collapse collapse in" role="tabpanel">
                        <div class="list-group">
                            <a href="<?= $this->module['documentos_url'] . "/OC"; ?>" class="list-group-item">Oficio de Citatorio (OC)</a>
                            <a href="<?= $this->module['documentos_url'] . "/OED"; ?>" class="list-group-item">Oficio de Envío de Documentos (OED)</a>
                            <a href="<?= $this->module['url'] . "/portada"; ?>" class="list-group-item">Imprimir portada o guarda exterior</a>
                        </div>
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
                    <div id="oficios-generales" class="card-block panel-collapse collapse in" role="tabpanel">
                        <div class="list-group">
                            <a href="<?= $this->module['documentos_url'] . "/ORP"; ?>" class="list-group-item">Oficio de Resolución de Prórroga (ORP)</a>
                            <a href="<?= $this->module['documentos_url'] . "/OSI"; ?>" class="list-group-item">Oficio de Solicitud de Información (OSI)</a>
                            <a href="<?= $this->module['documentos_url'] . "/RAP"; ?>" class="list-group-item">Oficio de Resolución de Ampliación de Plazo (RAP)</a>
                        </div>
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
                    <div id="actas" class="card-block panel-collapse collapse in" role="tabpanel">
                        <div class="list-group">
                            <a href="<?= $this->module['documentos_url'] . "/ARA"; ?>" class="list-group-item">Acta de Resultados (ARA/ARR)</a>
                            <a href="<?= $this->module['documentos_url'] . "/ACEI"; ?>" class="list-group-item">Acta de Cierre de Entrega de Información (ACEI)</a>
                            <a href="<?= $this->module['documentos_url'] . "/AA"; ?>" class="list-group-item">Autirozación Administrativa (AA)</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>