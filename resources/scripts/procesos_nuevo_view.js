$(document).ready(function () {
    $("#procesos_tipo_auditoria").button('toggle');

    $("form#procesos_form").on('submit', function (event) {
	var tiposAuditorias = [];
	$("label.active", "#tiposAuditorias").each(function (index, element) {
	    tiposAuditorias.push($.trim($(element).text()));
	});
	$("#procesos_tipo_auditoria").val(tiposAuditorias.join(","));
    });

    $("input:visible:first", "form#procesos_form").focus();
});