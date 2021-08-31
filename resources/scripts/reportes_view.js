$(document).ready(function () {
    var anio_actual = moment().year();
    for (var i = anio_actual; i > 2010; i--) {
        $("#anio").append('<option value="' + i + '" ' + (i == anio_actual ? 'selected="selected"' : '') + '>' + i + '</option>');
    }

    $(".btn-descargar").on('click', document, function () {
        let a = this;
        let url = base_url + controller + '/reporte';
        let reporte = $(this).attr("reporte");
        let data = {
            reporte: reporte,
            anio: $("select#anio").val(),
            auditorias_status_id: $("select#status").val(),
        };
        if (!isEmpty(reporte)) {
            $(a).addClass('disabled').html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
            $.post(url, data, function (json) {
                if (json.success && !isEmpty(json.archivo)) {
                    window.location = base_url + 'archivos/' + json.archivo;
                } else {
                    alert("Error al generar el archivo.");
                }
            }, "json").fail(function (e) {
                alert_html_error(e.responseText);
            }).always(function () {
                $(a).removeClass('disabled').html('Descargar');
            });
        }
        return false;
    });
    $(".btn-primary").each(function () {
        let reporte = $(this).attr("reporte");
        if (isEmpty(reporte)) {
            $(this).addClass("disabled");
        }
    });
}).on("click", ".btn.descargar", function () {
    var anio = $("#anio").val();
    var mes = $("#mes").val();
    var fraccion = $(this).attr("data-fraccion");

    var data = {
        anio: anio,
        mes: mes,
        fraccion: fraccion
    }
    let btn = this;
    $(btn).addClass("disabled").html(ICON_SPINNER);
    $.post(base_url + controller + "/descargar", data, function (json) {
        if (json.success) {
            window.location.href = json.archivo;
            $(btn).removeClass("disabled").html('Descargar');
        }
    }, "json");
});

function get_meses_del_anio(anio) {
    var meses = moment.months();
    if (anio == moment().year() && moment().month() < 11) {
        meses.splice(moment().month() + 1, 12);
    }
    return meses;
}

function is_json(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}