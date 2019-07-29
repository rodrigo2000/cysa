<div class="card1 no-bg">
    <div class="card-header no-bg b-a-0">
        <?php $this->load->view('auditoria/header_view'); ?>
    </div>
    <div class="card-block">
        <?php if (isset($this->session->cysa[$this->module['id_field']])): ?>
            <ul id="tab-menu-auditoria" class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab-informacion" role="tab">Información</a>
                </li>
                <!--                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-documentos" role="tab">Documentos</a>
                                </li>-->
                <li class="nav-item dropdown" id="catalogo_documentos">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Documentos</a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item dropdown-toggle" href="#">Documentación inicial</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= $this->module['documentos_url'] . "/OA"; ?>">Oficio de Orden de Auditoría (OA)</a></li>
                                <li><a class="dropdown-item" href="<?= $this->module['documentos_url'] . "/AIA"; ?>">Acta de Inicio de Auditoría (AIA)</a></li>
                                <li><a class="dropdown-item" href="<?= $this->module['documentos_url'] . "/AANP"; ?>">Autirozación de Auditoría No Programada (AANP)</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="dropdown-item dropdown-toggle" href="#">Ampliaciones y Reprogramaciones</a>
                            <ul class="dropdown-menu">
                                <?php $reprogramaciones = $this->Auditoria_model->get_documentos($auditoria['auditorias_id'], TIPO_DOCUMENTO_REPROGRAMACION); ?>
                                <?php foreach ($reprogramaciones as $r): ?>
                                    <?php if (isset($r['valores']) && !empty($r['valores'])): $folio = $r['valores'][15]; ?>
                                        <li><a class="dropdown-item" href="<?= $this->module['documentos_url'] . "/" . TIPO_DOCUMENTO_REPROGRAMACION . "/" . $r['documentos_id']; ?>">Reprogramación <?= str_pad($folio, 3, "0", STR_PAD_LEFT); ?></a></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <li><a class="dropdown-item" href="<?= $this->module['documentos_url'] . "/REPROG/nuevo"; ?>">Nueva reprogramación</a></li>
                                <?php $ampliaciones = $this->Auditoria_model->get_documentos($auditoria['auditorias_id'], TIPO_DOCUMENTO_AMPLIACION); ?>
                                <?php foreach ($ampliaciones as $a): ?>
                                    <?php if (isset($a['valores']) && !empty($a['valores'])): $folio = $a['valores'][2]; ?>
                                        <li><a class="dropdown-item" href="<?= $this->module['documentos_url'] . "/" . TIPO_DOCUMENTO_AMPLIACION . "/" . $a['documentos_id']; ?>">Ampliación <?= str_pad($folio, 3, "0", STR_PAD_LEFT); ?></a></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <li><a class="dropdown-item" href="<?= $this->module['documentos_url'] . "/AMPLIA/nuevo"; ?>">Nueva ampliación</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="dropdown-item dropdown-toggle" href="#">Documentación final</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= $this->module['documentos_url'] . "/OC"; ?>">Oficio de Citatorio (OC)</a></li>
                                <?php if ($auditoria['auditorias_anio'] < 2018): ?>
                                    <li><a class="dropdown-item" href="<?= $this->module['documentos_url'] . "/OED"; ?>">Oficio de Envío de Documentos (OED)</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?= $this->module['url'] . "/portada"; ?>">Imprimir portada o guarda exterior</a></li>
                                <li><a class="dropdown-item" href="#">Responsabilidad Administrativa (RA)</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="dropdown-item dropdown-toggle" href="#">Oficios generales</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= $this->module['documentos_url'] . "/ORP"; ?>">Oficio de Resolución de Prórroga (ORP)</a></li>
                                <li><a class="dropdown-item" href="<?= $this->module['documentos_url'] . "/OSI"; ?>">Oficio de Solicitud de Información (OSI)</a></li>
                                <li><a class="dropdown-item" href="<?= $this->module['documentos_url'] . "/RAP"; ?>">Oficio de Resolución de Ampliación de Plazo (RAP)</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="dropdown-item dropdown-toggle" href="#">Actas</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= $this->module['documentos_url'] . "/ARA"; ?>">Acta de Resultados de Auditoría (ARA)</a></li>
                                <li><a class="dropdown-item" href="<?= $this->module['documentos_url'] . "/ARR"; ?>">Acta de Resultados de Revisión (ARR)</a></li>
                                <li><a class="dropdown-item" href="<?= $this->module['documentos_url'] . "/ACEI"; ?>">Acta de Cierre de Entrega de Información (ACEI)</a></li>
                                <li><a class="dropdown-item" href="<?= $this->module['documentos_url'] . "/AA"; ?>">Acta Administrativa (AA)</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <!--                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-acta_resultados" role="tab">Acta de Resultados</a>
                                </li>-->
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-observaciones" role="tab">Observaciones</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-control_auditoria" role="tab">Control de auditoría</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-herramientas" role="tab">Calendario</a>
                </li>
                <!--                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-reciclaje" role="tab">Reciclaje</a>
                                </li>-->
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-timeline" role="tab">Línea de tiempo</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="tab-informacion" role="tabpanel">
                    <?php $this->load->view("auditoria/auditoria_view_tab_informacion"); ?>
                </div>
                <div class="tab-pane" id="tab-documentos" role="tabpanel">
                    <?php $this->load->view("auditoria/auditoria_view_tab_documentos"); ?>
                </div>
                <div class="tab-pane" id="tab-acta_resultados" role="tabpanel">
                    ARA
                </div>
                <div class="tab-pane" id="tab-observaciones">
                    <?php $this->load->view("auditoria/auditoria_view_tab_observaciones"); ?>
                </div>
                <div class="tab-pane" id="tab-control_auditoria" role="tabpanel">
                    <?php $this->load->view("auditoria/auditoria_view_tab_control_auditoria"); ?>
                </div>
                <div class="tab-pane" id="tab-herramientas" role="tabpanel">
                    <?php $this->load->view("auditoria/auditoria_view_tab_herramientas"); ?>
                </div>
                <div class="tab-pane" id="tab-reciclaje" role="tabpanel">settings</div>
                <div class="tab-pane" id="tab-timeline" role="tabpanel">
                    <?php $timeline = $this->Timeline_model->get_timeline($auditorias_id); ?>
                    <?php var_dump($timeline);?>
                    <?php //$this->load->view("auditoria/auditoria_view_tab_timeline"); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- Labelauty -->
<link href="<?= APP_SAC_URL; ?>resources/plugins/jquery-labelauty/source/jquery-labelauty.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/jquery-labelauty/source/jquery-labelauty.js" type="text/javascript"></script>
<!-- MultiSelect -->
<link href="<?= APP_SAC_URL; ?>resources/plugins/multiselect/css/multi-select.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/multiselect/js/jquery.multi-select.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/multiselect/js/jquery.quicksearch.js" type="text/javascript"></script>
<!-- MultiDropdown -->
<link href="<?= base_url(); ?>resources/plugins/bootstrap-4-multi-dropdown-navbar/css/bootstrap-4-navbar.min.css" rel="stylesheet" type="text/css"/>
<script src="<?= base_url(); ?>resources/plugins/bootstrap-4-multi-dropdown-navbar/js/bootstrap-4-navbar.min.js" type="text/javascript"></script>
<!-- Personalizados -->
<script src="<?= base_url(); ?>resources/scripts/select_dependientes.js" type="text/javascript"></script>
<link href="<?= base_url(); ?>resources/styles/auditoria_view.css" rel="stylesheet" type="text/css"/>
<script src="<?= base_url(); ?>resources/scripts/auditoria_view.js" type="text/javascript"></script>