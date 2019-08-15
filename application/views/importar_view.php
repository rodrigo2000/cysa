<div class="card">
    <div class="card-header no-bg1 b-a-0">
        <h3><?= $this->module['title_list']; ?></h3>
    </div>
    <div class="card-block">
        <form id="frm-importar">
            <div class="form-group row">
                <legend class="col-form-legend col-sm-2">Importar</legend>
                <div class="col-sm-10 lead">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="catalogo[]" value="documentos_tipos">
                            Catálogo de Tipos de documentos
                            <span id="documentos_tipos"></span>
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="catalogo[]" value="documentos_constantes">
                            Catálogo de Constantes de documentos
                            <span id="documentos_constantes"></span>
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="catalogo[]" value="versiones">
                            Catálogo de Versiones de documentos
                            <span id="versiones"></span>
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="catalogo[]" value="documentos">
                            Catálogo de Documentos
                            <span id="documentos"></span>
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="catalogo[]" value="auditorias">
                            Catálogo de Auditorías
                            <span id="auditorias"></span>
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="catalogo[]" value="observaciones">
                            Catálogo de Observaciones
                            <span id="observaciones"></span>
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="catalogo[]" value="recomendaciones">
                            Catálogo de Recomendaciones
                            <span id="recomendaciones"></span>
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="catalogo[]" value="avances">
                            Catálogo de Avances de Recomendaciones
                            <span id="avances"></span>
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="catalogo[]" value="equipos_trabajo">
                            Catálogo de Equipos de trabajo
                            <span id="equipos_trabajo"></span>
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="catalogo[]" value="involucrados">
                            Catálogo de Involucrados en auditoría
                            <span id="involucrados"></span>
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="catalogo[]" value="asistencias">
                            Catálogo de Asistencias en documentos de auditorías
                            <span id="asistencias"></span>
                        </label>
                    </div>
                </div>
                <div id="error-message"></div>
            </div>
            <div class="form-group row">
                <div class="offset-sm-2 col-sm-10">
                    <button type="button" id="btn-importar" class="btn btn-primary">Importar</button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
<link href="<?= base_url(); ?>resources/styles/importar_view.css" rel="stylesheet" type="text/css"/>
<script src="<?= base_url(); ?>resources/scripts/importar_view.js" type="text/javascript"></script>