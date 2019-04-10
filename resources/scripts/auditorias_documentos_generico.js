$(document).ready(function () {
    $("span.editable:not(.xeditable)").each(function (index, element) {
        if ($(this).html() === "") {
            $(this).html($(this).attr("default-value"));
        }
    }).on('blur', function () {
        if ($(this).html() === "") {
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

    $("input", ".autorizado").prop("readonly", true).prop("disabled", true);

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
});

function get_form_data(async = false) {
    var formData = $("#frmOficios").serializeArray();
    var data = {};
    $(formData).each(function (index, obj) {
        data[obj.name] = obj.value;
    });
    data.headers_id = $(".dd-selected-value", "#headers_id").attr('value');
    data.footers_id = $(".dd-selected-value", "#footers_id").attr('value');
    data.constantes = {};
    $("span.editable").each(function (index, element) {
        var id = $(element).prop('id');
        var valor = $(element).html();
        data.constantes[id] = valor;
    });
    $(".xeditable").each(function (index, element) {
        var id = $(element).prop("id");
        var arr = $("#" + id).editable('getValue');
        data.constantes[id] = arr[id];
    });
    var url = base_url + 'Documentos/guardar';
    $.post(url, data, function (json) {
        if (json.success) {
            if ($("#documentos_id").val() === '0') {
                 $(".actualizar_id").each(function(index, element){
                     var href = $(element).prop('href') + "/" + json.documentos_id;
                     $(element).prop('href', href);
                 });
            }
            $("#documentos_id").val(json.documentos_id);
            $(".actualizar_id").removeClass('hidden-xs-up');
            $("#accion").val('modificar');
            alert("Cambios actualizados");
        }
    }, "json");
    return true;
}