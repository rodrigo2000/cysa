$(document).ready(function () {
    $("select#subdireccion").on("change", function () {
        idSubdireccion = parseInt(this.value);
        if (idSubdireccion > 0) {
            $("select#area").html('<option>Cargando subdirecciones...</option>').attr("disabled", true);
            $.post(base_url + 'Productos/ajax_get_areas', {idSubdireccion: idSubdireccion}, function (json) {
                html = '<option value="0" selected="selected">SELECCIONAR</option>';
                if (json.success > 0) {
                    $.each(json.data, function (index, element) {
                        html += '<option value="' + json.data[index].clv_depto + '">' + json.data[index].denDepartamento + '</option>';
                    });
                } else {
                    html = '<option value="NULL">' + json.message + '</option>'
                }
                $("select#area").html(html).attr("disabled", false);
            }, "json");
        } else {
            $("select#area").html('').attr("disabled", true);
        }
    });

    $("input:radio[name=tipo_inclumplimiento]").on("click", function () {
        idMotivo = this.value;
        if (idMotivo != "") {
            $("select#idMotivoIncumplimiento").html('<option>Cargando motivos...</option>').attr("disabled", true);
            $.post(base_url + 'Productos/ajax_get_motivos_incumplimiento', {idMotivoIncumplimiento: idMotivo}, function (json) {
                html = '<option value="0" selected="selected">SELECCIONAR</option>';
                if (json.success > 0) {
                    $.each(json.data, function (index, element) {
                        html += '<option value="' + json.data[index].id_motivos + '">' + json.data[index].motivos + '</option>';
                    });
                } else {
                    html = '<option value="NULL">' + json.message + '</option>'
                }
                $("select#idMotivoIncumplimiento").html(html).attr("disabled", false);
            }, "json");
        } else {
            $("select#idMotivoIncumplimiento").html('').attr("disabled", true);
        }
    });

    $("select#idDocumento, select#idAuditoria").multiSelect();

    $("input:checked#auditorias_relacionadas").on("change", function () {
        if (this.checked) {
            $("fieldset#auditorias_container").show();
        } else {
            $("fieldset#auditorias_container").hide();
        }
    }).labelauty({
        minimum_width: "155px",
        class: "labelauty btn-block"
    });
});