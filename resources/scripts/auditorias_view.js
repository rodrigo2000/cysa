var table = null;
var labelClass = {"Finalizado": "success", "Cancelado": "danger", "Cerrado": "warning", "En proceso": "info"};
var statusIcon = {"Finalizado": "check", "Cancelado": "ban", "Cerrado": "lock", "En proceso": "clock-o"};

$(document).ready(function () {
    $("button#btnBuscar").on("click", function () {
        table.clear().draw();
    })
    $("select", "form").on("change", function () {
        table.clear().draw();
    });

    var table = $("table.dataTablePersonalizado").DataTable({
        language: {
            url: base_sac_url + "resources/plugins/datatables/media/Spanish.json"
        },
        columnDefs: [{orderable: false, targets: [-1]}],
        orderMulti: false,
        order: [[0, 'desc']],
        autoWidth: false,
        responsive: true,
        processing: true,
        serverSide: true,
        stateSave: true,
        ajax: {
            url: base_url + controller + "/get_auditorias_ajax",
            type: "POST",
            data: function (d) {
                d.auditorias_status_id = $("select#auditorias_status_id").val();
                d.auditorias_tipo = $("select#auditorias_tipo").val();
                d.auditorias_area = $("select#auditorias_area").val();
                d.auditorias_anio = $("select#auditorias_anio").val();
                d.direcciones_id = $("select#direcciones_id").val();
            },
        },
        columns: [
            {data: 'numero', class: 'align-middle text-xs-center'},
            {data: 'direccion', class: 'align-middle text-xs-center'},
            {data: 'fecha_inicio_programado', name: 'auditorias_fechas_inicio_programado', class: 'align-middle text-xs-center'},
            {data: 'fecha_inicio_real', name: 'auditorias_fechas_inicio_real', class: 'align-middle text-xs-center'},
            {data: 'aprobacion', name: 'auditorias_fechas_vobo_director', class: 'align-middle text-xs-center'},
            {data: 'status', name: 'auditorias_status_id', class: 'align-middle text-xs-center'},
            {data: 'acciones', class: 'align-middle text-xs-center'}
        ],
        stateLoadParams: function (settings, data) {
            $.each(data.search, function (index, element) {
                $("#" + index).val(element);
            });
        },
        stateSaveParams: function (settings, data) {
            $("select", "form#filtros").each(function (index, element) {
                var id = $(element).prop("id");
                data.search[id] = $(element).val();
            });
        },
        initComplete: function (settings, json) {
            $("#tablaAuditorias_filter input").unbind().bind("keypress", function (event) {
                if (event.keyCode == 13) {
                    var search = $(this).val();
                    var dataTable = $('table#tablaAuditorias').dataTable();
                    dataTable.fnFilter(search, null, false, true);
                }
            });
        }
    });
});
