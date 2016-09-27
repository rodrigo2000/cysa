var table = null;
var labelClass = {"Finalizado": "success", "Cancelado": "danger", "Cerrado": "warning", "En proceso": "info"};
var statusIcon = {"Finalizado": "check", "Cancelado": "ban", "Cerrado": "lock", "En proceso": "clock-o"};
var statusAuditoria = [null, "Cancelada", "En proceso", "Finalizada", "Reprogramada"];

$(document).ready(function () {
    $("select", "form").on("change", function () {
        table.draw();
    });

    table = $("table.dataTablePersonalizado").DataTable({
//sPaginationType: "full_numbers",
        order: [[0, 'desc']],
        autoWidth: false,
        responsive: true,
        processing: true,
        serverSide: true,
        //stateSave: true,
        ajax: {
            url: base_url + "Auditorias/listadoServerSide",
            type: "POST",
            data: function (d) {
                d.idStatus = $("select#idStatus").val();
                d.idTipo = $("select#idTipo").val();
                d.idArea = $("select#idArea").val();
                d.anio = $("select#anio").val();
                d.clv_dir = $("select#clv_dir").val();
            },
        },
        columns: [
            {
                data: null, name: "idAuditoria",
                mRender: function (data, type, full) {
                    if (data.num == null) {
                        html = '<span class="text-danger">SIN ASIGNAR</span>';
                    } else {
                        html = data.num;
                    }
                    fecha = new Date(1000 * parseInt(data.fecha, 10));
                    f = fecha.toLocaleDateString().split("/");
                    html += '<br>' + mysqlFecha2Fecha(f[2] + "-" + f[1] + "-" + f[0]);
                    return html;
                }
            },
            {data: "rubroAuditoria"},
            {
                data: null, name: "direccion",
                mRender: function (data, type, full) {
                    return data.denDireccion + '<br><span class="text-info">' + data.denSubdireccion + '</span>';
                }
            },
            {
                data: null, name: "fechaFinProgramadaAuditoria",
                mRender: function (data, type, full) {
                    return mysqlFecha2Fecha(data.fechaFinProgramadaAuditoria);
                }
            },
            {
                data: null, name: "fechaFinReal",
                mRender: function (data, type, full) {
                    return mysqlFecha2Fecha(data.fechaFinReal);
                }
            },
            {
                data: null, name: "fechaAprobacion",
                mRender: function (data, type, full) {
                    return mysqlFecha2Fecha(data.fechaAprobacion);
                }
            },
            {
                data: null, name: "statusAuditoria",
                mRender: function (data, type, full) {
                    return statusAuditoria[data.statusAuditoria];
                }
            },
            {
                data: null, searchable: false, class: "actions",
                mRender: function (data, type, full) {
                    html = '<a class="btn btn-xs btn-primary-outline" href="' + base_url + 'Solicitudes/' + data.idAuditoria + '" data-toggle="tooltip" title="Editar" data-placement="left"><i class="fa fa-pencil"></i></a>' +
                            '<a class="btn btn-xs btn-success-outline" href="' + base_url + 'Timeline/' + data.idAuditoria + '" data-toggle="Proceso" title="Editar" data-placement="left"><i class="fa fa-calendar"></i></a>';
                    return html;
                }
            }
        ],
        aoColumnDefs: [
            {
                bSortable: false,
                aTargets: [-1]
            }
        ],
        language: {
            url: base_sac_url + "resources/plugins/datatables/media/Spanish.json"
        },
    }).on("draw.dt", function () {
        $("tbody [data-toggle=tooltip]", "table.dataTablePersonalizado").tooltip();
    });
});