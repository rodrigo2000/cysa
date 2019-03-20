$(document).ready(function () {
    $(".xeditable").editable({
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
        var url = base_url + 'Documentos/guardar'
        $.post(url, data, function (json) {
            if (json.success) {
                $("#documentos_id").val(json.documentos_id);
                alert("Cambios actualizados");
            }
        }, "json");
    });
});