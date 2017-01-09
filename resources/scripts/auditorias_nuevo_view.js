$(document).ready(function () {

    $("select#direccion").on("change", function () {
        idDireccion = parseInt(this.value, 10);
        if (idDireccion > 0) {
            $("select#subdireccion").html('<option>Cargando subdirecciones...</option>').attr("disabled", true);
            $.post(base_url + 'Auditorias/ajax_get_subdirecciones', {idDireccion: idDireccion}, function (json) {
                html = '<option value="0" selected="selected">SELECCIONAR</option>';
                if (json.success > 0) {
                    $.each(json.data, function (index, element) {
                        html += '<option value="' + json.data[index].clv_subdir + '">' + json.data[index].denSubdireccion + '</option>';
                    });
                } else {
                    html = '<option value="NULL">' + json.message + '</option>'
                }
                $("select#subdireccion").html(html).attr("disabled", false);
                $("select#departamento").html('').attr("disabled", true);
            }, "json");
        } else {
            $("select#subdireccion").html('').attr("disabled", true);
            $("select#departamento").html('').attr("disabled", true);
        }
    });


    $("select#subdireccion").on("change", function () {
        idDireccion = $("select#direccion").val();
        idSubdireccion = parseInt(this.value);
        if (idSubdireccion > 0) {
            $("select#departamento").html('<option>Cargando departamentos...</option>').attr("disabled", true);
            $.post(base_url + 'Auditorias/ajax_get_departamentos', {idDireccion: idDireccion, idSubdireccion: idSubdireccion}, function (json) {
                html = '<option value="0" selected="selected">SELECCIONAR</option>';
                if (json.success > 0) {
                    $.each(json.data, function (index, element) {
                        html += '<option value="' + json.data[index].clv_dir + '">' + json.data[index].denDepartamento + '</option>';
                    });
                } else {
                    html = '<option value="NULL">' + json.message + '</option>'
                }
                $("select#departamento").html(html).attr("disabled", false);
            }, "json");
        } else {
            $("select#departamento").html('').attr("disabled", true);
        }
    });

    $("#myForm").validate({
        wrapper: 'div',
        errorElement: "span",
        errorPlacement: function (error, element) {
            error.addClass("label label-danger");
            if (element.parent('div[class|=has]').length == 0)
                element.wrap('<div class="has-danger"></div>');
            else
                element.parent('div[class|=has]').removeClass().addClass("has-danger")
            element.parents("div[class^=col-]").append(error)
        },
        success: function (label) {
            idLabel = $(label).attr("id");
            $("#" + idLabel).remove();
            aux = idLabel.split("-");
            nameElement = aux[0];
            if ($("[name=" + nameElement + "]").hasClass("select2")) {
                $("#s2id_" + nameElement).addClass("has-success")
            } else {
                $("[name=" + nameElement + "]").parent("div.has-danger").removeClass().addClass("has-success");
            }
        },
        rules: {
            area: {required: true, min: 1},
            tipo: {required: true, min: 1},
            consecutivo: "required",
            //anio: {required: true, min: 1},
            //segundoPeriodo: "required",
            //asignar: "required",
            fipa: "required",
            //firp: "required",
            ffpa: "required",
            //ffrp: "required",
            direccion: {required: true, min: 1},
            subdireccion: {required: true, min: 1},
            departamento: {required: true, min: 1},
            rubro: "required",
            objetivo: "required",
            auditor_lider: {required: true, min: 1},
        },
        messages: {
            area: "Olvidó selecionar el área",
            tipo: "Olvidó seleccionar el tipo de auditoría",
            consecutivo: "Requerido",
            anio: "Olvidó seleccionar el año de la auditoría",
            segundoPeriodo: "Requerido",
            asignar: "Requerido",
            fipa: "Se requiere la fecha de inicio programada",
            firp: "Se requiere la fecha de inicio real programada",
            ffpa: "Se requeire la fecha final programada",
            ffrp: "Se requiere la fecha final real programada",
            direccion: "Olvidó seleccionar la dirección a Auditar",
            subdireccion: "Olvidó seleccionar la subdirección a Auditar",
            departamento: "Olvidó seleccionar el departamento a Auditar",
            rubro: "Olvidó especificar el rubro de la auditoría",
            objetivo: "Olvidó especificar el objetivo de la auditoría",
            auditor_lider: "Olvidó seleccionar al Auditor Líder"
        }
    });

});