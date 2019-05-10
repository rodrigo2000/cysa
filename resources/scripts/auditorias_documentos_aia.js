$(document).ready(function () {
    actualizar_plurales();
    $("input#chkAsistencia").change(function () {
        if ($("input#chkAsistencia").prop("checked")) {
            $("#noAsistencia").fadeOut('slow', function () {
                $("#siAsistencia").fadeIn('slow');
            });
        } else {
            $("#siAsistencia").fadeOut('slow', function () {
                $("#noAsistencia").fadeIn('slow');
            });
        }
    });
});

function agregar_testigo($this, suggestion, tipo_asistencia, documentos_tipos_id) {
    var identificacion = sinEspecificar;
    if (!isEmpty(suggestion.empleados_credencial_elector_delante)) {
        identificacion = ' credencial para votar con clave de elector ' + suggestion.empleados_credencial_elector_delante + ' y número identificador ' + (!isEmpty(suggestion.empleados_credencial_elector_detras) ? suggestion.empleados_credencial_elector_detras : sinEspecificar);
    } else if (!isEmpty(suggestion.empleados_licencia_manejo)) {
        identificacion = " licencia de conducir con folio " + suggestion.empleados_licencia_manejo;
    }
    var html = '<span class="resaltar empleado_' + suggestion.empleados_id + '">el servidor público ' +
            suggestion.empleados_nombre_titulado + ', ' + suggestion.empleados_cargo +
            ', quien manifiesta ser de nacionalidad mexicana y con domicilio particular en ' +
            suggestion.empleados_domicilio +
            ' de la localidad de ' +
            (!isEmpty(suggestion.empleados_localidad) ? suggestion.empleados_localidad.capitalize() : sinEspecificar) +
            ' se identifica con ' + identificacion +
            ', la cual contiene su nombre y fotografía que concuerda con sus rasgos fisonómicos y en la que se aprecia su firma, que reconoce como suya por ser la misma que utiliza para validar todos sus actos tanto públicos como privados' +
            '<input type="hidden" name="testigos[]" value="' + suggestion.empleados_id + '">' +
            '<span type="button" class="autocomplete_empleados_delete label label-danger" title="Eliminar" data-empleados-id="' + suggestion.empleados_id + '">&times;</span>' +
            '</span>';
    if ($("span.resaltar", "#seccion_testigos").length > 0 && $("span.plural", "#seccion_testigos").length == 0) {
        html = '<span class="plural conjuncion"> y </span>' + html;
    }

    if (parseInt(documentos_tipos_id) == 29) {
        var html2 = '<span class="plural conjuncion"> y </span>' +
                '<span class="resaltar empleado_' + suggestion.empleados_id + '">' +
                suggestion.empleados_nombre_titulado + ', ' + suggestion.empleados_cargo +
                '</span>';
        $("span.conjuncion", "p#seccion_testigos_2").html(', ');
        $("span.resaltar:last", "p#seccion_testigos_2").after(html2);
    }

    $($this).parent('span').parent('span').before(html);
    $($this).val('').focus();
    actualizar_plurales();
    html = '<div class="firmas_empleado">' +
            '<div class="firmas_empleado_nombre">' + suggestion.empleados_nombre_titulado_siglas + '</div>' +
            '<div class="firmas_empleado_cargo">' + suggestion.empleados_cargo + '</div>' +
            '</div>';
    // Agregamos el empleado
    $(".firmas_testigos", ".firmas").append(html);
}