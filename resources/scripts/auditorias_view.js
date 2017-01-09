var table = null;
var labelClass = {"Finalizado": "success", "Cancelado": "danger", "Cerrado": "warning", "En proceso": "info"};
var statusIcon = {"Finalizado": "check", "Cancelado": "ban", "Cerrado": "lock", "En proceso": "clock-o"};
var statusAuditoria = [null, "Cancelada", "En proceso", "Finalizada", "Reprogramada"];
var className = [null, 'text-danger', 'text-info', 'text-success', 'text-warning']

$(document).ready(function () {
    $("button#btnBuscar").on("click", function () {
	table.clear().draw();
    })
    $("select", "form").on("change", function () {
	table.clear().draw();
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
		    var html = "";
		    if (data.num == null) {
			html = '<span class="text-danger">SIN ASIGNAR</span>';
		    } else {
			html = data.num;
		    }
		    var fecha = new Date(1000 * parseInt(data.fecha, 10));
		    var f = fecha.toLocaleDateString().split("/");
		    html += '<br><small>' + mysqlFecha2Fecha(f[2] + "-" + f[1] + "-" + f[0]) + '</small>';
		    return html;
		}
	    },
//            {data: "rubroAuditoria"},
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
		    var idStatus = get_status_auditoria(data);
		    var html = '<div class="' + className[idStatus] + '">' + statusAuditoria[idStatus] + '</div>';
		    return html;
		}
	    },
	    {
		data: null, searchable: false, class: "actions",
		mRender: function (data, type, full) {
		    var html = '<a class="btn btn-xs btn-primary-outline" href="' + base_url + 'Auditorias/modificar/' + data.idAuditoria + '" data-toggle="tooltip" title="Editar" data-placement="left"><i class="fa fa-pencil"></i></a>' +
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

function get_status_auditoria(data) {
    var valor = 0;
    var status = parseInt(data.statusAuditoria, 10);
    switch (status) {
	case 0:
	    valor = 1;
	    break;
	case 1:
	    valor = 2;
	    if (data.fechaInicioProgramadaAuditoria != data.fechaInicioReal) {
		valor = 4;
	    }
	    break;
	case 2:
	case 3:
	case 4:
	    valor = 3;
    }
    return valor;
}