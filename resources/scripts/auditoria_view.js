$(document).ready(function () {
    $(".collapse").collapse({
        toggle: true
    });

    if ($('.component-daterangepicker').length) {
        console.log("hay almenos uno")
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
    }

    var hash = window.location.hash;
    if (!isEmpty(hash)) {
        $('#tab-menu-auditoria a[href="' + hash + '"]').tab('show');
    }
});