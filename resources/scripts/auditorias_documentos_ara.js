function agregar_involucrado($this, suggestion) {
    if ($("#direccion_" + suggestion.direcciones_id).length == 0) {
        html = '<span class="resaltar" id="direcciones' + suggestion.direcciones_id + '">' + suggestion.nombre_completo_direccion + ', </span>';
        $("#seccion_involucrados").append(html);
    }
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
    if ($(".direccion_" + suggestion.cc_direcciones_id, ".firmas_involucrados").length == 0) {
        // Agregamos la direccion
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
    var html = '<span class="resaltar empleado_' + suggestion.empleados_id + '">' +
            (suggestion.empleados_genero == GENERO_MASCULINO ? ' el ' : ' la ') +
            suggestion.empleados_nombre_titulado + ', ' + suggestion.empleados_cargo +
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