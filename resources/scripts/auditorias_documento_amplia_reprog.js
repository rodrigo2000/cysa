$(document).on('change', 'input[type=number]', function () {
    var data = {
        fecha: $("#fecha_final_original").val(),
        dias: $(this).val(),
        solo_habiles: true
    };
    var url = base_url + 'CYSA/agregar_dias';
    $.post(url, data, function (json) {
        var msj = "Error al calcular la fecha";
        var value = '';
        if (json.success) {
            msj = mysqlDate2OnlyDate(json.nueva_fecha);
            $("#9").val(json.nueva_fecha);
        }
        $("#actualizar9").html(msj);
    }, "json");
});