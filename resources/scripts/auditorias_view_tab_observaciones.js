var contador = 0;
$(document).ready(function () {
    $("#observaciones_menu li:first-child a, #observaciones_auditoria > div:first-child").addClass("active");
    convertir_tinymce("#observaciones_auditoria textarea.editor_html");
    $(".autosize").each(function (index, element) {
        autosize(element);
    });
    $(".nav-tabs").on("click", "> li > a", function (e) {
        e.preventDefault();
        if (!$(this).hasClass('add-contact')) {
            $(this).tab('show');
        }
    });
});
$(".nav-pills a[data-toggle=tab], .nav-tabs a[data-toggle=tab]").on("click", function (e) {
    if ($(this).hasClass("disabled")) {
        e.preventDefault();
        return false;
    }
});
$(document).on('shown.bs.tab', ".nav-tabs a", function (e) {
    $("div.tab-pane.active #recomendaciones textarea", e.target.hash).each(function (index, textarea) {
        autosize.update(textarea);
    });
});
$(document).on('click', '.add-observacion', function (e) {
    e.preventDefault();
    e.stopPropagation();
    var template = $("#template_nueva_observacion > div.tab-pane").clone(true);
    var id = template.prop("id") + contador;
    $(template).prop("id", id).addClass('active');
    $("a.nav-link.active", "#observaciones_menu").removeClass("active");
    $(this).closest('li').before('<li class="nav-item"><a class="nav-link active" dta-toggle="tab" href="#' + id + '" role="tab"><span>Nueva observación</span> <button class="btn btn-sm btn-danger btn-tab-close eliminar-observacion" type="button" data-observaciones-id="' + id + '" title="Eliminar observación">&times;</button></a></li>');
    $("#recomendaciones_observacion_", template).prop("id", "recomendaciones_observacion_" + contador);
    $('#observaciones_auditoria.tab-content > .tab-pane.active').removeClass('active');
    $('#observaciones_auditoria.tab-content').append(template);
    convertir_tinymce("#" + id + ".tab-pane.active textarea.editor_html");
    setTimeout(function () {
        $("#" + id, "#observaciones").focus().trigger("click");
        $("input[name^=observaciones_titulo]", "#" + id).focus();
        $("input[name^=observaciones_numero]", "#" + id).val(0);
        var aux;
        aux = $("a.tab-detalles", "#" + id).get(0);
        $("div.panel-detalles", "#" + id).get(0).id = aux.hash.replace("#", "") + contador;
        aux.href = aux.hash + contador;
        aux = $("a.tab-solventacion", "#" + id).get(0);
        $("div.panel-solventacion", "#" + id).get(0).id = aux.hash.replace("#", "") + contador;
        aux.href = aux.hash += contador;
        contador++;
    }, 100);
    return true;
});
$(document).on('click', '.guardar-observacion', function (e) {
    e.preventDefault();
    tinyMCE.triggerSave();
    var data = $(this).parents("form").serializeObject();
    data.selector = $("#observaciones_auditoria > div.tab-pane.active").prop("id");
    data['observaciones_id[]'] = $("input[name^=observaciones_id]", "#observaciones_auditoria > div.tab-pane.active").val();
    var url = base_url + "Observaciones/guardar/";
    var $this = this;
    $($this).addClass('disabled').html(ICON_SPINNER + ' Guardando...');
    var old_id = $("#observaciones_auditoria > .tab-pane.active").prop("id");
    $.post(url, data, function (json) {
        if (json.success) {
            $($this).removeClass("btn-primary").addClass("btn-success").html(ICON_SUCCESS + ' Guardado correctamente');
            setTimeout(function () {
                $($this).removeClass('disabled btn-success').addClass('btn-primary').html('Guardar observación');
            }, 3000);
            if (json.data.accion === "nuevo") {
                let new_selector = "observaciones_" + json.data.observaciones_id;
                $("a[href$=" + json.data.old_selector + "] ", "#observaciones_menu")
                        .prop("href", "#observaciones_" + json.data.observaciones_id)
                        .children('span').html('Observación ' + json.data.observaciones_numero)
                        .parent("a")
                        .children('button').attr("data-observaciones-id", json.data.observaciones_id);
                $("div#" + json.data.old_selector).prop('id', new_selector);
//                $("a.tab-detalles", "div#" + new_selector).prop("href", "#observacion_detalles_" + json.data.observaciones_id);
//                $("a.tab-solventacion", "div#" + new_selector).prop("href", "#observaciones_solventacion_" + json.data.observaciones_id);
//                $("div.panel-detalles", "div#" + new_selector).prop("id", "observacion_detalles_" + json.data.observaciones_id);
//                $("div.panel-solventacion", "div#" + new_selector).prop("id", "observacion_solventacion_" + json.data.observaciones_id);
                $("input[name^=observaciones_id]", "#" + new_selector).val(json.data.observaciones_id);
                $("input[name^=observaciones_numero]", "#" + new_selector).val(json.data.observaciones_numero);
                $("a.add-recomendacion", "#" + new_selector)
                        .attr("data-observacion-id", json.data.observaciones_id)
                        .parent('div').removeClass('hidden-xs-up');
            }
            // Actualizamos el titulo en la pestaña de información
            $("ul#observaciones li#observacion_" + json.data.observaciones_id, "#tab-informacion").html(json.data.observaciones_titulo);
            $("ul#observaciones_menu li a.active", "#tab-observaciones").prop("title", json.data.observaciones_titulo);
        } else {
            alert(json.message);
            $($this).removeClass('disabled').html('Guardar observación');
        }

    }, "json");
    return false;
});
$(document).on('click', '.eliminar-observacion', function (e) {
    e.preventDefault();
    var url = base_url + "Observaciones/eliminar_observacion";
    var data = {
        observaciones_id: $(this).attr('data-observaciones-id')
    };
    $.post(url, data, function (json) {
        if (json.success) {
            let observaciones_id = json.data.observaciones_id;
            let selector = json.data.selector;
            var tab_actual = $("a[href$=" + selector + "]", "#observaciones_menu").parents("li");
            if (tab_actual.prev('li').length > 0) {
                tab_actual.prev('li').children('a').trigger('click');
            } else if (tab_actual.next('li:not(#tab-add-observacion)').length > 0) {
                tab_actual.next('li:not(#tab-add-observacion)').children('a').trigger('click');
            }
            tab_actual.fadeOut('slow', function () {
                $(this).remove();
                reenumerar_observaciones(json.data.reenumeracion);
            });
            $("#" + selector, "#observaciones_auditoria").fadeOut('slow', function () {
                $(this).remove();
            });
        } else {
            alert(json.message);
        }
    }, "json");
    return false;
});
$(document).on('click', '.add-recomendacion', function (e) {
    var observaciones_id = $(this).parents("div.panel-detalles.active").children('input.observaciones_id').val();
    if (!isEmpty(observaciones_id)) {
        var selector = $(this).parents("div.panel-detalles.active").prop('id');
        var template = $("div.card", "#template_nueva_recomendacion").clone(true);
        $(template).prop("id", "nueva_recomendacion_" + contador);
        $(".recomendaciones_observaciones_id", template).val(observaciones_id);
        $("#recomendaciones", "#observaciones_auditoria #" + selector).append(template);
        // Aplicamos plugins
        var textarea = $('#' + selector + ' #recomendaciones #nueva_recomendacion_' + contador + ' textarea.autosize');
        autosize(textarea);
        contador++;
    }
});
$(document).on('click', '.guardar-recomendacion', function (e) {
    e.preventDefault();
    var url = base_url + "Recomendaciones/guardar";
    var $this = this;
    var data = $(this).parents("form").serializeObject();
    $(this).addClass("disabled").html(ICON_SPINNER);
    $.post(url, data, function (json) {
        if (json.success) {
            if (json.data.accion === 'nuevo') {
                var div = $($this).parent("div");
                $('.recomendaciones_id', div).val(json.data.insert_id);
                $("h4", div).html("Recomendación " + json.data.recomendaciones_numero);
            } else {
                $("#observacion_" + json.data.observaciones_id, "#tab-informacion ul#observaciones").html(json.data.observaciones_titulo);
            }
        } else {
            alert(json.message);
        }
        $($this).removeClass("disabled").html('Guardar');
    }, "json");
});
$(document).on('click', '.eliminar-recomendacion', function (e) {
    e.preventDefault();
    var url = base_url + 'Recomendaciones/eliminar_recomendacion';
    var data = {
        recomendaciones_id: $(this).parent('div.card-header').children('input[type=hidden]').val()
    };
    $.post(url, data, function (json) {
        $("#recomendacion_" + json.data.recomendaciones_id, "#recomendaciones").fadeOut('slow');
        if (json.success) {

        } else {
            alert(json.message);
        }
    }, "json");
    return false;
});
$(document).on('click', '.guardar-recomendacion-avance', function (e) {
    e.preventDefault();
    e.stopPropagation();
    var url = base_url + "Recomendaciones_avances/guardar";
    var $this = this;
    var form = $(this).parents('form');
    var recomendaciones_id = form.find('input.recomendaciones_id').val();
    var observaciones_id = form.find('input.recomendaciones_observaciones_id').val();
    var etapa = form.find('input.recomendaciones_etapa_id').val();
    var editor = 'recomendaciones_avaces_descripcion_' + observaciones_id + '_' + recomendaciones_id + '_' + etapa;
    ////
    var b = "<body>";
    var be = "</body>";
    var t = tinyMCE.get(editor).getContent();
    t = t.substring((t.indexOf(b) + b.length), (t.indexOf(be) - 1));
    t = $.trim(t);
    $("textarea.editor_html", "#observacion_solventacion_" + observaciones_id + " #recomendacion_" + recomendaciones_id).val(t);
    var data = form.serializeObject();
    $(this).addClass("disabled").html(ICON_SPINNER);
    $.post(url, data, function (json) {
        if (json.success) {
            if (json.data.accion === 'nuevo') {
                var div = $($this).parent("div");
                $('.recomendaciones_id', div).val(json.data.insert_id);
                $("h4", div).html("Recomendación " + json.data.recomendaciones_numero);
            } else {
                $("#observacion_" + json.data.observaciones_id, "#tab-informacion ul#observaciones").html(json.data.observaciones_titulo);
            }
        } else {
            alert(json.message);
        }
        $($this).removeClass("disabled").html('<i class="fa fa-save"></i>');
    }, "json");
});
$(document).on('click', 'button.imprimir', function (e) {
    var hashtag = $(this).parent("a.nav-link").prop('hash');
    var id = $("input.observaciones_id", "#observaciones_auditoria " + hashtag).val();
    if ($(this).hasClass('imprimir-todas')) {
        id = 0;
    }
    var etapa = $(this).attr("data-etapa");
    window.open(base_url + controller + "/descargar/CO/" + id + "/" + etapa);
});
function convertir_tinymce(selector) {
    tinymce.init({
        language: 'es_MX',
        statusbar: false,
        selector: selector,
        height: '300px',
        content_style: ".mce-content-body {font-size:15px;font-family:Barlow;}",
        font_formats: 'Andale Mono=andale mono,times; Alwyn=alwyn, Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Barlow=barlow; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats',
//        preview_styles: false,
        fontsize_formats: '10px 11px 12px 13px 14px 15px 16px 18px 24px 36px 48px',
        plugins: 'print preview fullpage searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern placeholder code',
        //plugins: 'print preview fullpage powerpaste searchreplace autolink directionality advcode visualblocks visualchars fullscreen image link media mediaembed template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount tinymcespellchecker a11ychecker imagetools textpattern formatpainter permanentpen pageembed tinycomments mentions linkchecker code',
        toolbar: 'formatselect | fontselect | fontsizeselect | bold italic strikethrough forecolor backcolor permanentpen formatpainter | link image table | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | removeformat | code fullscreen',
        menubar: false, // TRUE ó 'file edit insert view format table tools help'
//        menu: {
//            file: {title: 'File', items: 'newdocument'},
//            edit: {title: 'Edit', items: 'undo redo | cut copy paste pastetext | selectall'},
//            insert: {title: 'Insert', items: 'link media | template hr'},
//            view: {title: 'View', items: 'visualaid'},
//            format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
//            table: {title: 'Table', items: 'inserttable tableprops deletetable | cell row column'},
//            tools: {title: 'Tools', items: 'spellchecker code'}
//        },
        image_advtab: true,
        template_cdate_format: '[CDATE: %m/%d/%Y : %H:%M:%S]',
        template_mdate_format: '[MDATE: %m/%d/%Y : %H:%M:%S]',
        image_caption: true,
        spellchecker_dialog: true,
        // Constantes
        fullpage_default_title: 'Contraloria',
        // Subir archivo local
        /* enable title field in the Image dialog*/
        image_title: true,
        /* enable automatic uploads of images represented by blob or data URIs*/
        automatic_uploads: true,
        /*
         URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url)
         images_upload_url: 'postAcceptor.php',
         here we add custom filepicker only to Image dialog
         */
        file_picker_types: 'image',
        /* and here's our custom image picker*/
        file_picker_callback: function (cb, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            /*
             Note: In modern browsers input[type="file"] is functional without
             even adding it to the DOM, but that might not be the case in some older
             or quirky browsers like IE, so you might want to add it to the DOM
             just in case, and visually hide it. And do not forget do remove it
             once you do not need it anymore.
             */

            input.onchange = function () {
                var file = this.files[0];
                var reader = new FileReader();
                reader.onload = function () {
                    /*
                     Note: Now we need to register the blob in TinyMCEs image blob
                     registry. In the next release this part hopefully won't be
                     necessary, as we are looking to handle it internally.
                     */
                    var id = 'blobid' + (new Date()).getTime();
                    var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);
                    /* call the callback and populate the Title field with the file name */
                    cb(blobInfo.blobUri(), {title: file.name});
                };
                reader.readAsDataURL(file);
            };
            input.click();
        }
    });
}

function reenumerar_observaciones(data) {
    var $return = false;
    if (!isEmpty(data)) {
        $.each(data, function (observaciones_numero, observaciones_id) {
            var element = $("a[href$=observaciones_" + observaciones_id + "] span", "#observaciones_menu");
            var nueva_etiqueta = "Observación " + observaciones_numero;
            if (element.text() !== nueva_etiqueta) {
                element.addClass('implotar');
                setTimeout(function () {
                    element.removeClass('implotar').text(nueva_etiqueta).addClass('explotar')
                }, 1100);
            }
        });
        $return = true;
    }
    return $return;
}