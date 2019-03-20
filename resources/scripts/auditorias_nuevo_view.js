$(document).ready(function () {
    $('.component-daterangepicker').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        timePickerIncrement: 5,
        autoApply: false,
        autoUpdateInput: false,
        opens: "center",
        alwaysShowCalendars: true,
        locale: {
            format: 'LL',
            applyLabel: "Aplicar",
            cancelLabel: "Cancelar",
            monthNames: moment.months()
        }
    }).on('apply.daterangepicker', function (ev, picker) {
        var alternativo = $(ev.target).attr("datepicker");
        if (typeof ev.target.val !== "undefined") {
            $(ev.target).val(picker.startDate.format("LL"));
        } else {
            $(ev.target).html(picker.startDate.format("LL"));
        }
        $("#" + alternativo).val(picker.startDate.format("YYYY-MM-DD")).trigger("change");
    });

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

    $("#asignar_consecutivo").on('change', function () {
        get_consecutivo();
    });
    $("#auditorias_segundo_periodo").on('change', function () {
        get_consecutivo();
    });
});

function get_consecutivo() {
    var is_checked = $("#asignar_consecutivo").prop("checked");
    var is_segundo_periodo = $("#auditorias_segundo_periodo:checked").length;
    if (is_checked) {
        $.get(base_url + controller + '/get_proximo_numero_auditoria/' + is_segundo_periodo, function (json) {
            $("#auditorias_numero, #numero_auditoria").val(json.consecutivo);
        }, "json");
    } else {
        $("#auditorias_numero, #numero_auditoria").val('');
    }
    $("#numero_auditoria").prop("disabled", is_checked);
}