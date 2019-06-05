var contador = 0;
$(document).ready(function () {
    $("#observaciones_menu li:first-child a, #observaciones_auditoria > div:first-child").addClass("active");

    convertir_tinymce("#observaciones_auditoria textarea.editor_html");

    $(".autosize").each(function (index, element) {
        autosize(element);
    });

    $(".nav-tabs").on("click", "a", function (e) {
        e.preventDefault();
        if (!$(this).hasClass('add-contact')) {
            $(this).tab('show');
        }
    }).on("click", "span", function () {
        var anchor = $(this).siblings('a');
        $(anchor.attr('href')).remove();
        $(this).parent().remove();
        $(".nav-tabs li").children('a').first().click();
    });
}).on('click', '.add-observacion', function (e) {
    e.preventDefault();
    var id = "nueva_observacion_" + contador;
    $(this).closest('li').before('<li class="nav-item"><a class="nav-link" dta-toggle="tab" href="#' + id + '" onclick="" role="tab">Nueva observación</a></li>');
    var template = $("div.tab-pane", "#template_nueva_observacion").clone(true);
    $(template).prop("id", id);
    $("#recomendaciones_observacion_", template).prop("id", "recomendaciones_observacion_" + contador);
    $(".add-recomendacion", template).attr("data-observacion", id);
    $('#observaciones_auditoria.tab-content > .tab-pane.active').removeClass('active');
    $('#observaciones_auditoria.tab-content').append(template);
    convertir_tinymce("#" + id + ".tab-pane.active textarea.editor_html");
    setTimeout(function () {
        $('.nav-tabs li:nth-child(' + contador + ') a', "#observaciones").trigger("click");
        $("input[name^=observaciones_titulo]", "#" + id + ".tab-pane.active").focus();
        $("input[name^=observaciones_numero]", "#" + id + ".tab-pane.active").val(contador);
    }, 100);
    contador++;
}).on('click', '.guardar-observacion', function (e) {
    e.preventDefault();
    tinyMCE.triggerSave();
    var data = $(this).parents("form").serializeObject();
    var url = base_url + "Observaciones/guardar/";
    var $this = this;
    $($this).addClass('disabled').html(ICON_SPINNER + ' Guardando...');
    $.post(url, data, function (json) {
        if (json.success) {
            var observaciones_id = json.data.insert_id;
            var index = json.data.observaciones_numero;
            var id = "observacion_" + observaciones_id;
            $($this).removeClass("btn-primary").addClass("btn-success").html(ICON_SUCCESS + ' Guardado correctamente');
            setTimeout(function () {
                $($this).removeClass('disabled btn-success').addClass('btn-primary').html('Guardar observación');
            }, 3000);
            $(".observaciones_id", "#nueva_observacion_" + index).val(observaciones_id);
            // Actualizamos el titulo en la pestaña de información
            $("ul#observaciones li#observacion_" + json.data.observaciones_id, "#tab-informacion").html(json.data.observaciones_titulo);
            $("ul#observaciones_menu li a.active", "#tab-observaciones").prop("title", json.data.observaciones_titulo);
        } else {
            alert(json.message);
            $($this).removeClass('disabled').html('Guardar observación');
        }

    }, "json");
    return false;
}).on('click', '.eliminar-observacion', function (e) {
    e.preventDefault();
    var url = base_url + "Observaciones/eliminar";
    var data = {};
    $.post(url, data, function (json) {
        if(json.success){
            
        }
    }, "json");
    return false;
}).on('click', '.add-recomendacion', function (e) {
    var id = $(this).parents('div.tab-pane.active').prop('id');
    var aux = id.split('_'); // id = 'observaciones_1345' ó 'nueva_observacion_1'
    var observaciones_id = aux[1];
    var template = $("div.card", "#template_nueva_recomendacion").clone(true);
    $(template).prop("id", "nueva_recomendacion_" + contador);
    $(".recomendaciones_observaciones_id", template).val(observaciones_id);
    $("#recomendaciones", "#observaciones_auditoria #" + id).append(template);
    // Aplicamos plugins
    var textarea = $('#' + id + ' #recomendaciones #nueva_recomendacion_' + contador + ' textarea.autosize');
    autosize(textarea);
    contador++;
}).on('click', '.guardar-recomendacion', function (e) {
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
                console.log("#observacion_" + json.data.observaciones_id + ", #tab-informacion ul#observaciones")
                $("#observacion_" + json.data.observaciones_id, "#tab-informacion ul#observaciones").html(json.data.observaciones_titulo);
            }
        } else {
            alert(json.message);
        }
        $($this).removeClass("disabled").html('Guardar');
    }, "json");
}).on('click', '.eliminar-recomendacion', function (e) {
    e.preventDefault();
    var url = base_url + 'Recomendaciones/eliminar';
    var data = {};
    $.post(url, data, function (json) {
    }, "json");
    return false;
});

function convertir_tinymce(selector) {
    tinymce.init({
        language: 'es_MX',
        statusbar: false,
        selector: selector,
        height: '300px',
        plugins: 'print preview fullpage searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern placeholder code',
        //plugins: 'print preview fullpage powerpaste searchreplace autolink directionality advcode visualblocks visualchars fullscreen image link media mediaembed template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount tinymcespellchecker a11ychecker imagetools textpattern formatpainter permanentpen pageembed tinycomments mentions linkchecker code',
        toolbar: 'formatselect | bold italic strikethrough forecolor backcolor permanentpen formatpainter | link image | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | removeformat | code fullscreen',
        menubar: false,
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