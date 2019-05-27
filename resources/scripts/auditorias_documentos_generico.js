$(document).ready(function () {
    $("*[default-value]", "#frmOficios").each(function (index, element) {
        if (this.nodeName === 'INPUT' || this.nodeName === 'SELECT' || this.nodeName === 'TEXTAREA') {
            if ($.trim($(this).val()) === "") {
                $(this).val($(this).attr("default-value"))
            }
        } else if ($.trim($(this).text()) === "") {
            $(this).html($(this).attr("default-value"));
        }
    }).on('blur', function () {
        if (this.nodeName === 'INPUT' || this.nodeName === 'SELECT' || this.nodeName === 'TEXTAREA') {
            if ($.trim($(this).val()) === "") {
                $(this).val($(this).attr("default-value"))
            }
        } else if ($.trim($(this).text()) === "") {
            $(this).html($(this).attr("default-value"));
        }
    }).on('keypress', function (event) {
        var aceptar_enter = $(this).attr("aceptar-enter");
        if (event.which == 13 && aceptar_enter != 1) {
            event.preventDefault();
            return false;
        }
    });
    $(".xeditable", "div#oficio-hoja:not(.autorizado)").editable({
        url: base_url + "Oficios/actualizar_campo_de_oficio",
        datepicker: {
            language: 'es',
        },
        display: false,
        success: function (response, newValue) {
            if (typeof response !== "undefined") {
                if (response.state === "success") {
                    $(this).html(response.nuevo_valor_por_mostrar);
                    $("input[name=" + response.nombre_campo + "]").val(response.nuevo_valor_guardado);
                    if (this.nodeName === 'INPUT' || this.nodeName === 'SELECT' || this.nodeName === 'TEXTAREA') {

                    } else {
                        $(this).attr('data-value', response.nuevo_valor_guardado);
                    }
                } else {
                    return response.msg;
                }
            } else {
                return "Error de JavaScript: UNDEFINED";
            }
        },
        error: function (response, newValue) {
            if (response.status === 500) {
                return 'Service unavailable. Please try later.';
            } else {
                return response.responseText;
            }
        }
    });
    $("input.autoresize").on('input', function () {
        this.style.width = this.value.length + "ch";
    }).trigger('input');
    $("input, select, textarea", ".autorizado").prop("readonly", true).prop("disabled", true);
    $("span.editable", "#oficio-hoja.autorizado").prop('contenteditable', false);
    $('#headers_id.ddslick').ddslick({
        width: '100%',
        imagePosition: "left",
        selectText: "Selecciona la imagen del encabezado",
        background: "none",
        onSelected: function (data) {
            $("img.dd-selected-image", "#headers_id").css("margin", "auto");
        }
    });
    // HEADERS
    $("#headers_id").css("margin", "auto").css("margin-bottom", "1em");
    $(".dd-selected", "#headers_id").css("display", "flex");
    $("img", "#headers_id").css("margin", "auto");
    $("a.dd-option", "#headers_id").css("display", "grid");
    $("ul,div", "#headers_id").css("background-color", "yellow");
    // FOOTERS
    $('#footers_id.ddslick').ddslick({
        width: '100%',
        imagePosition: "left",
        selectText: "Selecciona la imagen del pie de página",
        background: "none",
        onSelected: function (data) {
            $("img.dd-selected-image", "#footers_id").css("margin", "auto");
        }
    });
    $("#footers_id").css("margin", "auto").css("margin-bottom", "1em");
    $(".dd-selected", "#footers_id").css("display", "flex");
    $("img", "#footers_id").css("margin", "auto");
    $("a.dd-option", "#footers_id").css("display", "grid");
    $("ul,div", "#footers_id").css("background-color", "yellow");
    $("button.boton_guardar").on('click', function () {
        get_form_data(true);
    });
    actualizar_plurales();
    if (typeof Bloodhound !== 'undefined') {
        var empleados = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: base_sac_url + 'Usuarios/get_empleados_typeahead/%QUERY',
                wildcard: '%QUERY',
                filter: function (response) {
                    return response.data;
                }
            }
        });
        $('.autocomplete').typeahead({
            highlight: true,
            hint: true,
            minLength: 1
        }, {
            name: 'buscar',
            display: 'value',
            source: empleados,
//        async: true,
//        limit: 100
        }).bind('typeahead:select', function (ev, suggestion) {
            let url = base_url + "Asistencias/agregar_asistencia";
            let documentos_id = $("#documentos_id").val();
            let data = {
                documentos_id: documentos_id,
                empleados_id: suggestion.empleados_id,
                asistencias_tipo: $(this).attr("data-asistencias-tipo")
            }
            let $this = this;
            $.post(url, data, function (json) {
                var a = parseInt($($this).attr("data-asistencias-tipo"));
                var dt = parseInt($("#documentos_tipos_id").val());
                if (json.success) {
                    if (a > 2) {
                        agregar_involucrado($this, suggestion, a, dt);
                    } else if (a == 2) {
                        agregar_testigo($this, suggestion, a, dt);
                    }
                } else {
                    alert("No se puedo agregar al empleado. " + json.message);
                }
            }, "json");
        });
    }
    if ($('.component-daterangepicker').length > 0) {
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
    $(document).on('click', ".btn_agregar", function () {
        $(this).addClass('hidden-xs-up');
        var tipo = $(this).attr("data-tipo");
        var asistencias_tipo = $(this).attr("data-asistencias-tipo");
        $("#autocomplete_" + tipo)
                .css('display', 'inline-table')
                .css('display', '-webkit-inline-box')
                .removeClass('hidden-xs-up');
        $("input.tt-input", "#autocomplete_" + tipo).attr("data-asistencias-tipo", asistencias_tipo);
    }).on('click', '.autocomplete_empleados_delete', function () {
        let url = base_url + "Asistencias/eliminar_asistencia";
        let empleados_id = $(this).attr("data-empleados-id");
        let data = {
            documentos_id: $("#documentos_id").val(),
            empleados_id: empleados_id
        };
        let $this = this;
        $.post(url, data, function (json) {
            if (json.success) {
                $($this).parent('span.resaltar').remove();
                $(".empleado_" + empleados_id).remove();
                $(".firmas_involucrados > div").each(function (index, element) {
                    if ($('div', element).length == 0) {
                        $(element).remove();
                    }
                });
                actualizar_plurales();
            } else {
                alert("No se puedo eliminar el empleado. " + json.message);
            }
        }, "json");
    }).on('click', 'button.ocultar', function () {
        var span = $(this).parent('span').parent('span');
        span.parent('span').next('a.btn_agregar').removeClass('hidden-xs-up');
        span.addClass('hidden-xs-up');
    }).on('click', '.opciones', function (event) {
        event.preventDefault();
        var opciones = $(this).attr("data-opciones").split('|');
        var html = $(this).html();
        var next = null;
        for (var i = 0; i < opciones.length; i++) {
            if (opciones[i] == html) {
                next = i + 1;
            }
        }
        var n = next % opciones.length;
        while (isEmpty(opciones[n])) {
            n++;
        }
        $(this).html(opciones[n]).trigger('change', [n]);
        event.stopPropagation();
    });
});
function get_form_data(async = false) {
    var data = $("#frmOficios").serializeObject();
    data.headers_id = $(".dd-selected-value", "#headers_id").attr('value');
    data.footers_id = $(".dd-selected-value", "#footers_id").attr('value');
    var oficio = $("#oficio-hoja").clone(true);
    $("button,.btn,.autocomplete_empleados_delete,.hidden-print,.watermark", oficio).remove();
    $('.resaltar, .editable, .bg-white>span', oficio).each(function (index, element) {
        let txt = $(element).text();
        $(element).replaceWith(txt);
    });
    $(".dd-selected-image", oficio).each(function (index, element) {
        let obj = $(element).css('margin-bottom', '1em').removeClass();
        let div = $(element).parents('div.dd-container');
        div.parent('td').addClass('text-xs-center');
        div.replaceWith(obj);
    });
    data.constantes = $.extend({}, data.constantes);
    $(".editable, span[name^=constantes]", "#frmOficios").each(function (index, element) {
        var id = $(element).prop('id');
        var valor = $(element).html();
        var name = $(element).attr('name');
        if (isEmpty(name)) {
            data.constantes[id] = valor;
        } else {
            if (name.indexOf("[") > 0) {
                name = name.substring(0, name.indexOf("["));
                if (isEmpty(data[name])) {
                    data[name] = [];
                }
                data[name][id] = valor;
                data[name] = $.extend({}, data[name]);
            } else {
                data[name] = valor;
            }
        }
    });
    $(".xeditable").each(function (index, element) {
        var id = $(element).prop("id");
        var arr = $("#" + id).editable('getValue');
        data.constantes[id] = arr[id];
    });
    data.html = $(oficio).html();
    var url = base_url + 'Documentos/guardar';
    $("button.boton_guardar").prop('disabled', true).addClass('disabled').html('Guardando...');
    $.post(url, data, function (json) {
        if (json.success) {
            if ($("#documentos_id").val() === '0') {
                $(".actualizar_id").each(function (index, element) {
                    var href = $(element).prop('href') + "/" + json.documentos_id;
                    $(element).prop('href', href);
                });
            }
            $("#documentos_id").val(json.documentos_id);
            $(".actualizar_id").removeClass('hidden-xs-up');
            $("#accion").val('modificar');
            //alert("Cambios actualizados");
        }
        $("button.boton_guardar").prop('disabled', false).removeClass('disabled').html('Guardar');
    }, "json");
    return true;
}

function plurales(mostrar, selector) {
    if (mostrar) {
        $("plural", selector).show();
    } else {
        $("plural", selector).hide();
    }
    singulares(!mostrar, selector);
}

function singulares(mostrar, selector) {
    if (mostrar) {
        $("singular", selector).show();
    } else {
        $("singular", selector).hide();
    }
}

function actualizar_plurales() {
    var mostrar;
    mostrar = $("span.resaltar", "#seccion_involucrados").length > 1 ? true : false;
    plurales(mostrar, "#seccion_involucrados");
    plurales(mostrar, "#seccion_involucrados_2");
    var mostrar = $("span.resaltar", "#seccion_testigos").length > 1 ? true : false;
    plurales(mostrar, "#seccion_testigos");
    plurales(mostrar, "#seccion_testigos_2");
}

String.prototype.capitalize = function () {
    return this.replace(/(^|\s)([a-z])/g, function (m, p1, p2) {
        return p1 + p2.toUpperCase();
    });
};

function agregar_involucrado($this, suggestion, tipo_asistencia, documentos_id) {
    var seccion = "involucrados" + (tipo_asistencia == 4 ? '_contraloria' : '');
    if ($("#direccion_" + suggestion.direcciones_id).length == 0) {
        html = '<span class="resaltar" id="direcciones' + suggestion.direcciones_id + '">' + suggestion.nombre_completo_direccion + ', </span>';
        $("#seccion_" + seccion).append(html);
    }
    var html = '<span class="resaltar" id="empleado_' + suggestion.empleados_id + '">' +
            suggestion.empleados_nombre_titulado + ', ' + suggestion.empleados_cargo +
            (documentos_id == 29 && suggestion.empleados_id == $("#auditorias_enlace_designado").val() ? ',Enlace Designado' : '') +
            '<input type="hidden" name="' + seccion + '[]" value="' + suggestion.empleados_id + '">' +
            ' <span type="button" class="autocomplete_empleados_delete label label-danger" title="Eliminar" data-empleados-id="' + suggestion.empleados_id + '">&times;</span>, ' +
            '</span>';
    if ($("span.resaltar", "#seccion_" + seccion).length > 0 && $("span.plural", "#seccion_" + seccion).length == 0) {
        html = '<span class="plural conjuncion"> y ' + (suggestion.empleados_genero == 1 ? 'del' : 'de la') + ' </span>' + html;
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

function agregar_testigo($this, suggestion, tipo_asistencia, documentos_tipos_id) {
    var html = '<span class="resaltar empleado_' + suggestion.empleados_id + '">' +
            (suggestion.empleados_genero == GENERO_MASCULINO ? ' el ' : ' la ') +
            suggestion.empleados_nombre_titulado + ', ' + suggestion.empleados_cargo +
            '<input type="hidden" name="testigos[]" value="' + suggestion.empleados_id + '">' +
            '<span type="button" class="autocomplete_empleados_delete label label-danger" title="Eliminar" data-empleados-id="' + suggestion.empleados_id + '">&times;</span> ' +
            '</span>';
    if ($("span.resaltar", "#seccion_testigos").length > 0 && $("span.plural", "#seccion_testigos").length == 0) {
        html = '<span class="plural conjuncion"> y </span>' + html;
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

function mostrar_parrafo(id, obj) {
    if ($("#" + id).length > 0) {
        $("#" + id).removeClass('hidden-xs-up');
        $(obj).addClass('hidden-xs-up');
        $(obj).parents(".show-hide")
                .removeClass('text-xs-center')
                .addClass('text-justify texto-sangria bg-punteado')
                .find(".btn-hide")
                .removeClass('hidden-xs-up');
        $(obj).parents('.show-hide').find('input').val(1);
    }
    return false;
}

function ocultar_parrafo(id, obj) {
    if ($("#" + id).length > 0) {
        $("#" + id).addClass('hidden-xs-up');
        $(obj).addClass('hidden-xs-up');
        $(obj).parents(".show-hide")
                .removeClass('text-justify texto-sangria bg-punteado')
                .addClass('text-xs-center')
                .find(".btn-show")
                .removeClass('hidden-xs-up');
        $(obj).parents('.show-hide').find('input').val(0);
    }
    return false;
}

function mostrar_span(id, obj) {
    if ($("#" + id).length > 0) {
        $("#" + id).removeClass('hidden-xs-up');
        $(obj).addClass('hidden-xs-up');
        $(obj).parents(".show-hide")
                .find(".btn-hide")
                .removeClass('hidden-xs-up');
    }
    return false;
}

function ocultar_span(id, obj) {
    if ($("#" + id).length > 0) {
        $("#" + id).addClass('hidden-xs-up');
        $(obj).addClass('hidden-xs-up');
        $(obj).parents(".show-hide")
                .find(".btn-show")
                .removeClass('hidden-xs-up');
    }
    return false;
}