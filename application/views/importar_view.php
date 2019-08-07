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
                            Catálogo de Tipos de documentos <span class="label label-danger">Cuidado!</span>
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
            </div>
            <div class="form-group row">
                <div class="offset-sm-2 col-sm-10">
                    <button type="button" id="btn-importar" class="btn btn-primary">Importar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#btn-importar").click(function () {
            let d = $("#frm-importar").serializeArray();
            var urls = [];
            $.each(d, function (index, element) {
                urls.push(element.value);
            });
            ajaxRequest(urls);
//            $.each(d, function (index, element) {
//                $("#" + element.value)
//                        .html('<i class="fa fa-spinner fa-pulse fa-fw"></i>')
//                        .removeClass();
//                let data = {'catalogo[]': element.value};
//                $.post(base_url + controller + "/iniciar_importacion", data, function (json) {
//                    if (json.success) {
//                        $("#" + json.id)
////                                .html('<i class="fa fa-check" title="' + json.message + '"></i>')
//                                .html('<i class="material-icons" aria-hidden="true" title="' + json.message + '">check</i>')
//                                .addClass("label label-success");
//                    }
//                }, "json");
//            });
        });
    });

    function ajaxRequest(urls) {
        if (urls.length > 0) {
            var element = urls.shift();
            var url = base_url + controller + "/iniciar_importacion";
            $("#" + element)
                    .html('<i class="fa fa-spinner fa-pulse fa-fw"></i>')
                    .removeClass();
            let data = {'catalogo[]': element};
            $.post(url, data, function (json) {
                if (json.success) {
                    $("#" + json.id)
                            .html('<i class="material-icons" aria-hidden="true" title="' + json.message + '">check</i>')
                            .addClass("label label-success");
                }
                ajaxRequest(urls);
            }, "json");
        }
    }
</script>