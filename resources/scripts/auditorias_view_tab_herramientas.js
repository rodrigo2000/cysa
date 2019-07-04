var DT_hoy = new Date(2019, 6, 1);
var DT_hoyTime = DT_hoy.getTime();
var hoy = {
    id: 0,
    color: 'none',
    titulo: 'Auditoria',
    descripcion: 'hoy',
    etapa: 0,
    startDate: DT_hoy,
    endDate: DT_hoy,
};
var timeDiasFestivos = [];
var timeDiasInhabiles = [];
var timeDiasAuditoria = [];

$(document).ready(function () {
    let anioActual = new Date().getFullYear();

    $('#calendar').calendar({
        language: 'es',
        style: 'background',
        enableContextMenu: true,
        allowOverlap: true,
        enableRangeSelection: true,
        maxDate: new Date(anioActual + 1, 11, 31),
        minDate: new Date(anioActual - 1, 0, 1),
        disabledWeekDays: [6, 0],
        selectRange: function (e) {
            var a = moment(e.startDate);
            var b = moment(e.endDate);
            var diasNaturales = b.diff(a, 'days');
            var diasHabiles = 0;
            var x, d;
            while (a < b) {
                x = a.format('x');
                d = a.format('d');
                if (d != 0 && d != 6 && !$.inArray(x, timeDiasInhabiles) !== -1) {
                    diasHabiles++;
                }
                a.add(1, 'd');
            }
            if ($("#chk-incluir-primer-dia").prop("checked")) {
                diasHabiles++;
                diasNaturales++;
            }
            $("#calendario-fecha-inicio", "#rango-seleccionado").html(a.format("LLLL"));
            $("#calendario-fecha-fin", "#rango-seleccionado").html(b.format("LLLL"));
            $("#dias-naturales", "#rango-seleccionado").html(diasNaturales);
            $("#dias-habiles", "#rango-seleccionado").html(diasHabiles);

        },
        mouseOnDay: function (e) {
            if (e.events.length > 0) {
                var content = '';

                for (var i in e.events) {
                    content += '<div class="event-tooltip-content">'
                            + (typeof e.events[i].titulo !== "undefined" ? '<div class="event-titulo" ' + (typeof e.events[i] !== 'undefined' ? 'style="color:' + e.events[i].color + '"' : '') + '>' + e.events[i].titulo + '</div>' : '')
                            + '<div class="event-descripcion">' + e.events[i].descripcion + '</div>'
                            + '</div>';
                }

                $(e.element).popover({
                    trigger: 'manual',
                    container: 'body',
                    html: true,
                    content: content,
                    template: '<div class="popover"><div class="popover-arrow"></div><div class="popover-content"></div></div>'
                });

                $(e.element).popover('show');
            }
        },
        mouseOutDay: function (e) {
            if (e.events.length > 0) {
                $(e.element).popover('hide');
            }
        },
        customDayRenderer: function (element, date) {
            if (date.getTime() == DT_hoyTime) {
                $(element).css('border', '2px solid blue').css('border-radius', '15px');
            }
            if ($.inArray(date.getTime(), timeDiasFestivos) !== -1) {
                $(element).addClass("dia-festivo");
            }
            if ($.inArray(date.getTime(), timeDiasInhabiles) !== -1) {
                $(element).parent().addClass("inhabil");
            }
            if ($.inArray(date.getTime(), timeDiasAuditoria) !== -1) {
                $(element).parent().addClass("dia-auditoria");
            }
            if ($.inArray(date.getDay(), [6, 0]) !== -1) {
                $(element).parent().addClass("weekend");
            }
        }
    });

    var url = base_url + controller + "/get_fecha_para_calendario";
    var data = {

    };
    $.post(url, data, function (json) {
        if (json.success) {
            var inhabiles = [];
            var festivos = [];
            var data = [];
            $.each(json.inhabiles, function (index, element) {
                var a = element.dias_inhabiles_fecha.split("-");
                var DT_fecha = new Date(a[0], parseInt(a[1]) - 1, a[2]);
                inhabiles.push(DT_fecha);
                timeDiasInhabiles.push(DT_fecha.getTime());
                var b = {
                    id: 0,
                    titulo: 'Día inhábil',
                    descripcion: element.dias_inhabiles_descripcion,
                    startDate: DT_fecha,
                    endDate: DT_fecha
                }
                data.push(b);
            });
            $.each(json.festivos, function (index, element) {
                var a = element.fecha.split("-");
                var DT_fecha = new Date(a[0], parseInt(a[1]) - 1, a[2]);
                festivos.push(DT_fecha);
                timeDiasFestivos.push(DT_fecha.getTime());
                let key = element.evento;
                var evento = {};
                evento[key] = (parseInt(a[1]) - 1) + "/" + a[2];
                moment.modifyHolidays.add(evento);
                var b = {
                    id: 0,
                    titulo: element.titulo,
                    descripcion: element.descripcion,
                    startDate: DT_fecha,
                    endDate: DT_fecha
                }
                data.push(b);
            });
            $.each(json.fechas, function (index, arreglo) {
                $.each(arreglo, function (indice, valor) {
                    if (valor != null && indice !== "auditorias_fechas_etapa" && indice !== "auditorias_fechas_auditorias_id") {
                        var a = new String(valor).split("-");
                        var DT_fecha = new Date(a[0], parseInt(a[1]) - 1, a[2]);
                        var b = {
                            id: 0,
                            descripcion: indice,
                            etapa: arreglo['auditorias_fechas_etapa'],
                            startDate: DT_fecha,
                            endDate: DT_fecha
                        }
                        data.push(b);
                        timeDiasAuditoria.push(DT_fecha.getTime());
                    }
                });
            });
            data.push(hoy);
            //$("#calendar").data("calendar").setDisabledDays(inhabiles);
            $("#calendar").data("calendar").setDataSource(data);
        }
    }, "json");
});
