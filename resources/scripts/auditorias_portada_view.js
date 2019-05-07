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

    $(":checkbox").labelauty({
        icon: false,
        same_width: true,
        minimum_width: "50px"
    });

}).on('click', "a#btnAgregarTomo", function () {
    var siguienteTomo = $("div.input-group", "div#tomos").length + 1;
    var html = '<div class="input-group">' +
            '<span class="input-group-addon">Tomo ' + siguienteTomo + '</span>' +
            '<input type="number" name="numero_fojas[]" class="form-control text-xs-center" size="4" value="0">' +
            '<span class="input-group-btn">' +
            '<a href="#" class="btn btn-danger btnEliminarTomo"><i class="fa fa-remove"></i></a>' +
            '</span>' +
            '</div>';
    $("div#tomos").append(html);
    return false;
}).on("click", "a.btnEliminarTomo", function () {
    var totalTomos = $("div.input-group", "div#tomos").length;
    if (totalTomos > 1) {
        $(this).parents("div.input-group").remove();
        $("span.input-group-addon", "div#tomos").each(function (index, element) {
            $(element).html("Tomo " + (index + 1));
        });
    } else {
        alert("Al menos debe existir un tomo en el expediente.");
    }
    return false;
}).on('click', "#anchorEnviarRevision", function () {
    var idAuditoria = $(this).attr("idAuditoria");
    if (confirm('¿Esta seguro de enviar el expediente a revisión?\n\nNOTA: Una vez realizada esta acción, ya no se podrá actualizar el expediente, a menos que Resguardante rechace su expediente.')) {
        $.post('ajax_enviar_a_revision.php', {idAuditoria: idAuditoria}, function (json) {
            if (json.success) {
                $("#anchorModal, #anchorEnviarRevision").hide();
                alert("El expediente se ha enviado a revisión");
            }
        }, "json");
    }
});