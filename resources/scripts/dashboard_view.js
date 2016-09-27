var t = new Date();
var a = t.getDate();
var l = t.getMonth();
var n = t.getFullYear();

var start = moment();
var end = moment();

function cb(start, end) {
    $("#rango_inicio").val(start.format('YYYY-MM-DD HH:mm'));
    $("#rango_final").val(end.format('YYYY-MM-DD HH:mm'));
    $('#rango').val(start.format('MMMM D, YYYY h:mm A') + ' - ' + end.format('MMMM D, YYYY h:mm A'));
}

$(document).ready(function () {
    $(".fullcalendar").fullCalendar({
        editable: true,
        contentHeight: window.height - 300,
        lang: 'es',
//        customButtons: {
//            myCustomButton: {
//                text: 'custom!',
//                click: function () {
//                    alert('clicked the custom button!');
//                }
//            }
//        },
        header: {
            left: "title",
            center: "month,agendaWeek,agendaDay",
            right: "today prev,next"
        },
        buttonIcons: {
            prev: " fa fa-caret-left",
            next: " fa fa-caret-right"
        },
        firstDay: 1, // Primer día lo ponemos a lunes
        weekends: true,
        weekNumbers: true,
        hiddenDays: [6, 0], // 0=Domingo, 6=Sábado
        droppable: true,
        axisFormat: "h:mm",
        columnFormat: {
            month: "dddd",
            week: "dddd\nD/MMMM",
            day: "dddd, DD de dddd M/d",
            agendaDay: "dddd, DD [de] MMMM"
        },
        allDaySlot: false,
        drop: function () {
            var a = $(this).data("eventObject"),
                    l = $.extend({}, a);
            l.start = t, $(".fullcalendar").fullCalendar("renderEvent", l, !0), $("#drop-remove").is(":checked") && $(this).remove()
        },
        defaultDate: moment().format("YYYY-MM-DD"),
        viewRender: function () {
            $(".fc-button-group").addClass("btn-group"), $(".fc-button").addClass("btn")
        },
        events: base_url + "Dashboard/my_feed",
        eventClick: function (event, element) {
            eliminarElemento();
        },
        dayClick: function (date, jsEvent, view) {
            mostrarModal(date, jsEvent, view);
        }
    });

    $("span.fc-icon", ".fullcalendar").removeClass("fc-icon");

    $("input.drp").daterangepicker({
        locale: {
            format: 'LL h:mm A',
            separator: " al ",
            applyLabel: "Aplicar",
            cancelLabel: "Cancelar",
            fromLabel: "De",
            toLabel: "Hasta",
            customRangeLabel: "Personalizado",
            weekLabel: "Sem",
            daysOfWeek: moment.weekdaysMin(), //["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
            monthNames: moment.months(), // ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            firstDay: 1
        },
//            ranges: {
//                'Hoy': [moment(), moment()],
//                'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
//                'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
//                'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
//                'Este mes': [moment().startOf('month'), moment().endOf('month')],
//                'Mes anterior': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
//            },
        //startDate: end,
        showWeekNumbers: true,
        timePicker: true,
        timePickerIncrement: 15,
        drops: "up"
    }).on('apply.daterangepicker', function(ev, picker) {
        cb(picker.startDate, picker.endDate);
    });
}, cb);

cb(start, end);

function mostrarModal(date, jsEvent, view) {
    $(".bd-example-modal").modal("show");
}