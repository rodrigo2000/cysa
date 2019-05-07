function agregar_involucrado($this, suggestion) {
    var html = '<span class="resaltar" id="empleado_' + suggestion.empleados_id + '">' +
            suggestion.empleados_nombre_titulado + ', ' + suggestion.empleados_cargo +
            '<input type="hidden" name="involucrados[]" value="' + suggestion.empleados_id + '">' +
            ' <span type="button" class="autocomplete_empleados_delete label label-danger" title="Eliminar" data-empleados-id="' + suggestion.empleados_id + '">&times;</span>, ' +
            '</span>';
    if ($("span.resaltar", "#seccion_involucrados").length > 0 && $("span.plural", "#seccion_involucrados").length == 0) {
        html = '<span class="plural"> y ' + (suggestion.empleados_genero == 1 ? 'del' : 'de la') + ' </span>' + html;
    }
    $($this).parent('span').parent('span').before(html);
    $($this).val('').focus();
    actualizar_plurales();
    // Agregamos la direccion
    if ($(".direccion_" + suggestion.cc_direcciones_id, ".firmas_involucrados").length == 0) {
        html = '<div class="direccion_' + suggestion.cc_direcciones_id + '">' +
                '<p class="firmas_ua_nombre">' + suggestion.direcciones_nombre + '</p>' +
                '</div>';
        $(".firmas_involucrados").prepend(html);
    }
    html = '<div class="firmas_empleado">' +
            '<div class="firmas_empleado_nombre">' + suggestion.empleados_nombre_titulado_siglas + '</div>' +
            '<div class="firmas_empleado_cargo">' + suggestion.empleados_cargo + '</div>' +
            '<div class="firmas_empleado_enlace">ENLACE DESIGNADO</div>' +
            '</div>';
    // Agregamos el empleado
    $(".direccion_" + suggestion.cc_direcciones_id, ".firmas_involucrados").append(html);
}

function agregar_testigo($this, suggestion) {
    var sinEspecificar = '<b>[SIN ESPECIFICAR]</b>';
    var identificacion = sinEspecificar;
    if (!isEmpty(suggestion.empleados_credencial_elector_delante)) {
        identificacion = ' credencial para votar con clave de elector ' + suggestion.empleados_credencial_elector_delante + ' y número identificador ' + (!isEmpty(suggestion.empleados_credencial_elector_detras) ? suggestion.empleados_credencial_elector_detras : sinEspecificar);
    } else if (!isEmpty(suggestion.empleados_licencia_manejo)) {
        identificacion = " licencia de conducir con folio " + suggestion.empleados_licencia_manejo;
    }
    var html = '<span class="resaltar empleado_' + suggestion.empleados_id + '">el servidor público ' +
            suggestion.empleados_nombre_titulado + ', ' +
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
        html = '<span class="plural"> y </span>' + html;
    }
    $($this).parent('span').parent('span').before(html);
    $($this).val('').focus();
    actualizar_plurales();
    html = '<div class="firmas_empleado">' +
            '<div class="firmas_empleado_nombre">' + suggestion.empleados_nombre_titulado_siglas + '</div>' +
            '<div class="firmas_empleado_cargo">' + suggestion.empleados_cargo + '</div>' +
            '</div>';
    // Agregamos el empleado
    $(".direccion_" + suggestion.cc_direcciones_id, ".firmas_testigos").append(html);
}