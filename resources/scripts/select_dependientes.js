function cargar_select_de_direcciones(grupo, periodos_id, callback) {
    if (periodos_id > 0) {
        let data = {
            grupo: grupo,
            periodos_id: periodos_id
        }
        let url = base_url + 'SAC/get_direcciones';
        $("select.direcciones_dependiente" + grupo).html('<option value="0">Cargando direcciones...</option>').prop('disabled', true);
        $("select.subdirecciones_dependiente" + grupo).html('<option value="0">SELECCIONE SUBDIRECCIÓN</option>').prop('disabled', true);
        $("select.departamentos_dependiente" + grupo).html('<option value="0">SELECCIONE DEPARTAMENTO</option>').prop('disabled', true);
        $("select.empleados_dependiente" + grupo).html('<option></option>').prop('disabled', true);
        $.post(url, data, function (json) {
            if (json.success) {
                let grupo = json.grupo;
                if (json.data.length > 0) {
                    let html = '<option value="0" selected="selected">SELECCIONE DIRECCIÓN</option>';
                    $.each(json.data, function (index, element) {
                        html += '<option value="' + element.direcciones_id + '">' + numeral(element.cc_etiqueta_direccion).format('00') + ' - ' + element.direcciones_nombre + '</option>';
                    });
                    $("select.direcciones_dependiente" + grupo).html(html).prop('disabled', false);
                } else {
                    $("select.direcciones_dependiente" + grupo).html('<option value="0">No se encontraron direcciones</option>');
                }
                if (isset(json.empleados) && json.empleados.length > 0) {
                    mostrar_empleados_en_select(grupo, json.empleados);
                }
                if (typeof (callback) === "function") {
                    callback(grupo);
                }
            }
        }, "json");
    } else {
        $("select.direcciones_dependiente" + grupo).html('<option value="0">SELECCIONE DIRECCIÓN</option>');
        $("select.subdirecciones_dependiente" + grupo).html('<option value="0">SELECCIONE SUBDIRECCIÓN</option>');
        $("select.departamentos_dependiente" + grupo).html('<option value="0">SELECCIONE DEPARTAMENTO</option>');
        $("select.empleados_dependiente" + grupo).html('');
    }
}

function cargar_select_de_subdirecciones(grupo, periodos_id, direcciones_id, callback) {
    if (direcciones_id > 0) {
        let data = {
            grupo: grupo,
            periodos_id: periodos_id,
            direcciones_id: direcciones_id
        }
        let url = base_url + 'SAC/get_subdirecciones';
        $("select.subdirecciones_dependiente" + grupo).html('<option value="0">Cargando subdirecciones...</option>').prop('disabled', true);
        $("select.departamentos_dependiente" + grupo).prop('disabled', true);
        $("select.empleados_dependiente" + grupo).prop('disabled', true);
        $.post(url, data, function (json) {
            if (json.success) {
                let grupo = json.grupo;
                if (json.data.length > 0) {
                    let html = '<option value="0" selected="selected">SELECCIONE SUBDIRECCIÓN</option>';
                    $.each(json.data, function (index, element) {
                        html += '<option value="' + element.subdirecciones_id + '">' + numeral(element.cc_etiqueta_direccion).format('00') + '.' + numeral(element.cc_etiqueta_subdireccion).format('00') + ' - ' + element.subdirecciones_nombre + '</option>';
                    });
                    $("select.subdirecciones_dependiente" + grupo).html(html).prop('disabled', false);
                } else {
                    $("select.subdirecciones_dependiente" + grupo).html('<option value="0">No se encontraron subdirecciones</option>');
                }
                if (isset(json.empleados) && json.empleados.length > 0) {
                    mostrar_empleados_en_select(grupo, json.empleados);
                }
                if (typeof (callback) === "function") {
                    callback(grupo);
                }
            }
        }, "json");
    } else {
        $("select.subdirecciones_dependiente" + grupo).html('<option value="0"SELECCIONE SUBDIRECCIÓN</option>');
        $("select.departamentos_dependiente" + grupo).html('<option value="0">SELECCIONE DEPARTAMENTO</option>');
        $("select.empleados_dependiente" + grupo).html('');
    }
}

function cargar_select_de_departamentos(grupo, periodos_id, direcciones_id, subdirecciones_id, callback) {
    if (subdirecciones_id > 0) {
        let data = {
            grupo: grupo,
            periodos_id: periodos_id,
            direcciones_id: direcciones_id,
            subdirecciones_id: subdirecciones_id
        }
        let url = base_url + "SAC/get_departamentos";
        var select_empleados = $("select.empleados_dependiente" + grupo);
        var has_dual_list = select_empleados.hasClass('dual-list');
        if (!has_dual_list) {
            $("select.departamentos_dependiente" + grupo).html('<option value="0">Cargando departamentos...</option>').prop('disabled', true);
            $("select.empleados_dependiente" + grupo).prop('disabled', true);
        }
        $.post(url, data, function (json) {
            if (json.success) {
                let grupo = json.grupo;
                if (json.data.length > 0) {
                    let html = '<option value="0">SELECCIONE DEPARTAMENTO</option>';
                    $.each(json.data, function (index, element) {
                        html += '<option value="' + element.departamentos_id + '">' + numeral(element.cc_etiqueta_direccion).format('00') + '.' + numeral(element.cc_etiqueta_subdireccion).format('00') + '.' + numeral(element.cc_etiqueta_departamento).format('00') + ' - ' + element.departamentos_nombre + '</option>';
                    });
                    $("select.departamentos_dependiente" + grupo).html(html).prop('disabled', false);
                    $("select.empleados_dependiente" + grupo).prop('disabled', true);
                } else {
                    $("select.departamentos_dependiente" + grupo).html('<option value="0">No se encontraron departamentos</option>');
                }
                if (isset(json.empleados) && json.empleados.length > 0) {
                    mostrar_empleados_en_select(grupo, json.empleados);
                }
                if (typeof (callback) === "function") {
                    callback(grupo);
                }
            }
        }, "json");
    } else {
        $("select.departamentos_dependiente" + grupo).html('<option value="0">SELECCIONE DEPARTAMENTO</option>');
        $("select.empleados_dependiente" + grupo).html('');
    }
}

function cargar_select_de_empleados(grupo, periodos_id, direcciones_id, subdirecciones_id, departamentos_id, callback) {
    if (departamentos_id > 0) {
        let data = {
            grupo: grupo,
            periodos_id: periodos_id,
            direcciones_id: direcciones_id,
            subdirecciones_id: subdirecciones_id,
            departamentos_id: departamentos_id
        }
        let url = base_url + "SAC/get_empleados_de_departamento";
        var select_empleados = $("select.empleados_dependiente" + grupo);
        var has_dual_list = select_empleados.hasClass('dual-list');
        if (!has_dual_list) {
            select_empleados.html('<option value="0" disabled="disabled">Cargando empleados...</option>');
        }
        $.post(url, data, function (json) {
            if (json.success) {
                let grupo = json.grupo;
                var d = {
                    empleados_id: 0,
                    empleados_nombre_completo: 'No se encontraron empleados',
                    empleados_numero_empleado: 0
                };
                var data = [d];
                if (json.data.length > 0) {
                    data = json.data;
                }
                mostrar_empleados_en_select(grupo, data);
                if (typeof (callback) === "function") {
                    callback(grupo);
                }
            }
        }, "json");
    }
}

function mostrar_empleados_en_select(grupo, empleados) {
    let html = '';
    var select = $("select.empleados_dependiente" + grupo);
    var has_dual_list = select.hasClass('dual-list');
    if (has_dual_list) {
        var selected = [];
        if ($("option:selected", select).length > 0) {
            $("option:selected", select).each(function (index, element) {
                html += '<option value="' + $(element).val() + '" selected="selected">' + $(element).text() + '</option>';
                selected.push(parseInt($(element).val()));
                selected.push($(element).val());
            });
        }
        $.each(empleados, function (index, element) {
            if ($.inArray(element.empleados_id, selected) == -1 && parseInt(element.empleados_nombre_completo) !== '') { // Si no lo encuentra, lo agregamos
                html += '<option value="' + element.empleados_id + '" ' + (element.empleados_id == 0 ? 'disabled="disabled"' : '') + '>' + element.empleados_nombre_completo + (element.empleados_id > 0 ? ' (' + element.empleados_numero_empleado + ')' : '') + '</option>';
            }
        });
        $(select).html(html).prop('disabled', false);
        $(select).trigger("change");
        $(select).multiSelect('refresh');
    } else {
        $.each(empleados, function (index, element) {
            html += '<option value="' + element.empleados_id + '">' + element.empleados_nombre_completo + ' (' + element.empleados_id + ')</option>';
        });
        $(select).html(html).prop('disabled', false);
        $(select).trigger("change");
    }
}

var despues_de_cargar_direcciones = despues_de_cargar_subdirecciones = despues_de_cargar_departamentos = despues_de_cargar_empleados = function (grupo) {}

$(document).ready(function () {
    $("select.periodos_dependiente").change(function () {
        var grupo = ":not([grupo])";
        if (!isEmpty($(this).attr("grupo"))) {
            grupo = '[grupo="' + $(this).attr("grupo") + '"]';
        }
        let periodos_id = parseInt(this.value);
        cargar_select_de_direcciones(grupo, periodos_id, despues_de_cargar_direcciones);
    });

    $("select.direcciones_dependiente").change(function () {
        var grupo = ":not([grupo])";
        if (!isEmpty($(this).attr("grupo"))) {
            grupo = '[grupo="' + $(this).attr("grupo") + '"]';
        }
        let periodos_id = parseInt($(".periodos_dependiente" + grupo).val());
        let direcciones_id = parseInt(this.value);
        cargar_select_de_subdirecciones(grupo, periodos_id, direcciones_id, despues_de_cargar_subdirecciones);
    });

    $("select.subdirecciones_dependiente").change(function () {
        var grupo = ":not([grupo])";
        if (!isEmpty($(this).attr("grupo"))) {
            grupo = '[grupo="' + $(this).attr("grupo") + '"]';
        }
        let periodos_id = parseInt($(".periodos_dependiente" + grupo).val());
        let direcciones_id = parseInt($(".direcciones_dependiente" + grupo).val());
        let subdirecciones_id = parseInt(this.value);
        cargar_select_de_departamentos(grupo, periodos_id, direcciones_id, subdirecciones_id, despues_de_cargar_departamentos);
    });


    $("select.departamentos_dependiente").change(function () {
        var grupo = ":not([grupo])";
        if (!isEmpty($(this).attr("grupo"))) {
            grupo = '[grupo="' + $(this).attr("grupo") + '"]';
        }
        let periodos_id = parseInt($(".periodos_dependiente" + grupo).val());
        let direcciones_id = parseInt($(".direcciones_dependiente" + grupo).val());
        let subdirecciones_id = parseInt($(".subdirecciones_dependiente" + grupo).val());
        let departamentos_id = parseInt(this.value);
        cargar_select_de_empleados(grupo, periodos_id, direcciones_id, subdirecciones_id, departamentos_id, despues_de_cargar_empleados);
    });
});