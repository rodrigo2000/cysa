function meses(m) {
    if (isNaN(m)) {
        return "";
    }
    m = parseInt(m, 10)
    var Mes = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    return Mes[m];
}

function nombreDia(numeroDia) {
    if (isNaN(numeroDia)) {
        return "";
    }
    d = parseInt(numeroDia, 10);
    var Dias = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
    return Dias[d];
}
var primeraVezCalendar = true;
var fechaEjecucion;
var fechaProgramada;
var winBootbox;
var configuracionesID = null;
var campoEjecucion = null;
var idElemento = "";
var anchos = [];
var listaArchivos = null;
var marginCard = 40;

function sticky_relocate() {
    $('.etapa-nombre').each(function (index, element) {
        if (Math.abs($(window).scrollTop()) > $(this).parents(".etapas").offset().top) {
            $(this).addClass('stick');
        } else {
            $(this).removeClass('stick');
        }
    });
}

$(document).ready(function () {
    $("a.entregar", "table.documentos_vobos").click(function (event) {
        var $this = $(this);
        $this.addClass("disabled").html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
        var idDocto = $this.attr("data-idDocto");
        var idEmpleado = $this.attr("data-idEmpleado");
        $.post("timeline_set_vobo.php", {entregar: 1, idDocto: idDocto, idEmpleado: idEmpleado}, function (json) {
            if (json.success) {
                $this.parent().html('Entregado');
            } else {
                $this.removeClass("disabled").html('Try again');
            }
        }, "json");
        return false;
    });
    $(".js-candlestick", "table.documentos_vobos").candlestick({
        allowManualDefault: true, // Enable the three options, set to false will disable the default option
        on: '1',
        off: '0',
        nc: '',
        swipe: false,
        mode: 'options',
        afterAction: function (input, wrapper, action) {
            var revisiones_id = input.attr("data-idRevisiones");
            var idDocto = input.attr("data-idDocto");
            var idEmpleado = input.attr("data-idEmpleado");
            var valor = null
            switch (action) {
                case 'on':
                    valor = 1;
                    break;
                case 'off':
                    valor = 0;
                    break;
                default:
                    valor:null;
                    break;
            }
            if (event.type === "click") {
                $.post("timeline_set_vobo.php", {revisiones_id: revisiones_id, idDocto: idDocto, valor: valor, idEmpleado: idEmpleado}, function (json) {
                    if (json.success) {
                        if (parseInt(json.valor) === 0) {
                            if (json.revision_fecha_entrega === "NULL") { // Este valor llega como cadena de texto
                                swal({
                                    title: 'No se ha especificado la fecha de entrega:',
                                    input: 'text',
                                    showCancelButton: true,
                                    confirmButtonText: 'Continuar',
                                    showLoaderOnConfirm: true,
                                    cancelButtonText: 'Cancelar',
                                    showLoaderOnConfirm: true,
                                    preConfirm: function (texto) {
                                        return new Promise(function (resolve, reject) {
                                            if (texto.length == 0) {
                                                reject('El motivo no puede estar vacío');
                                            } else {
                                                alert(texto)
                                                fecha_entrega = texto;
                                                resolve();
                                            }
                                        })
                                    },
                                    allowOutsideClick: false
                                });
                                $("input[type=text].swal2-input", "div.swal2-container").datepicker();
                            } else {
                                swal({
                                    title: 'Escriba el motivo del rechazo',
                                    input: 'textarea',
                                    showCancelButton: true,
                                    confirmButtonText: 'Guardar',
                                    showLoaderOnConfirm: true,
                                    cancelButtonText: 'Cancelar',
                                    showLoaderOnConfirm: true,
                                    preConfirm: function (texto) {
                                        return new Promise(function (resolve, reject) {
                                            if (texto.length == 0) {
                                                reject('El motivo no puede estar vacío');
                                            } else {
                                                $.post('timeline_guardar_rechazo.php', {revisiones_id: json.revisiones_id, idDocto: json.idDocto, motivo: texto, idEmpleado: json.idEmpleado}, function (json) {}, 'json').done(function (data) {
                                                    if (data.success) {
                                                        resolve();
                                                    } else {
                                                        reject("Error al intentar guardar la información");
                                                    }
                                                }).fail(function (data) {
                                                    reject('Error al intentar guardar la información.');
                                                });
                                            }
                                        })
                                    },
                                    allowOutsideClick: false
                                }).then(function (texto) {
                                    // Mostramos que el motivo se guardo exitosamente
                                    swal({
                                        type: 'success',
                                        title: 'Datos guardados'
                                    });
                                }, function (dismiss) {
                                    // Si presiono el boton CANCELAR entonces regresamos al estado anterior
                                    if (dismiss == 'cancel') {
                                        var valor_inicial = $(input).attr("data-valorInicial");
                                        $(input).candlestick(valor_inicial);
                                    }
                                });
                            }
                        }
                    } else {
                        swal('Oops!!!', json.error, 'error');
                    }
                }, "json");
            }
        }
    });
    $("#selectAnio", "#frmCambiarAuditoria").on('change', function () {
        var a = parseInt(this.value, 10);
        if (a === 0) {
            return false;
        }
        $("#selectAuditoria").prop('disabled', true).html('<option value="0">Cargando...</option>');
        $.post("../modelo/timeline_ajax_select.php", {funcion: 'get_auditorias_del_anio', anio: a}, function (json) {
            if (json.success) {
                $("#selectAuditoria").html('');
                $.each(json.data, function (tipo, auditorias) {
                    var optgroup = '<optgroup label="' + tipo + '"><option value="0">SELECCIONE UNA AUDITORIA</option>';
                    $.each(auditorias, function (index, element) {
                        optgroup += '<option value="' + element.id + '" ' + (auditorias_id == element.id ? 'selected="selected"' : '') + ' title="' + element.objetivo + '">' + (element.num != null ? (element.valnum + " - " + element.num) : 'S/N - ' + element.rubro) + '</option>';
                    });
                    optgroup += '</optgroup>';
                    $("#selectAuditoria").append(optgroup);
                });
            }
            $("#selectAuditoria").prop('disabled', false);
        }, "json");
    }).trigger('change');
    $("#selectAuditoria", "#frmCambiarAuditoria").on('change', function () {
        var n = noty({
            layout: 'center',
            type: 'information',
            text: 'Cargando...',
            modal: true
        });
        $('#frmCambiarAuditoria').submit();
    });
    $("a#prorrogas, a#ampliaciones, a#reprogramaciones").click(function () {
        window.opener.name = "ventana";
        window.open("", window.opener.name);
        var idObjeto = this.id;
        var d1 = $.Deferred();
        var d2 = $.Deferred();
        // Cuando las auditorias del año seleccionado este cargadas se ejecutará el siguiente PROMISE
        $.when(d1).then(function () {
            $("#docs", window.opener.document).trigger("click");
            setTimeout(function () {
                d2.resolve();
            }, 1000);
        });
        // Cuando ya este cargado DOCUMENTOS se hará la siguiente PROMISE
        $.when(d2).then(function () {
            var ele = null
            switch (idObjeto) {
                case "prorrogas":
                    ele = $(".menutitle:contains('Oficios Generales')", window.opener.contenidos.document);
                    var sub3 = $(ele).next("span#sub3");
                    $(sub3).css('display', 'block');
                    var sub5a = $("span#sub5a", sub3);
                    $(sub5a).css('display', 'block').children('a').trigger("click");
                    break;
                case "ampliaciones":
                    ele = $(".menutitle:contains('Ampliaciones')", window.opener.contenidos.document);
                    var sub2 = $("a", ele).get(0);
                    $(sub2).trigger("click");
                    var sub2a = $(ele).next("span#sub2a");
                    $(sub2a).css("display", "block");
                    break;
                case "reprogramaciones":
                    ele = $(".menutitle:contains('Reprogramaciones')", window.opener.contenidos.document);
                    var sub2 = $("a", ele).get(0);
                    $(sub2).trigger("click");
                    var sub2a = $(ele).next("span#sub2a");
                    $(sub2a).css("display", "block");
                    break;
            }

            window.opener.document.frmSelAudit.idAuditoria.value = $("select#selectAuditoria").val();
        });

        if (window.opener.document.frmSelAudit.anioHistorico.value != $("select#selectAnio").val()) {
            window.opener.document.frmSelAudit.anioHistorico.value = $("select#selectAnio").val();
            $(window.opener.document.frmSelAudit.idAuditoria).on("change", function () {
                setTimeout(function () {
                    window.opener.document.frmSelAudit.idAuditoria.value = $("select#selectAuditoria").val();
                }, 2000);
            });
            window.opener.document.frmSelAudit.anioHistorico.onchange();
        } else {
            window.opener.document.frmSelAudit.idAuditoria.value = $("select#selectAuditoria").val();
        }
        var postData = {
            anioHistorico: $("select#selectAnio").val(),
            permisoChange: 0,
            auditorias_id: $("select#selectAuditoria").val()
        }
        $.post("c_pant_auditoria.php", postData, function (html) {
            d1.resolve();
        });
    });
    $(document).scroll(sticky_relocate);
    sticky_relocate();
    corregirLineaCentralDeTimeLine();
    var middlePosOfWindow = window.innerHeight / 2;
    $("html, body").animate({scrollTop: $($(".timeline-icon.bg-success, .timeline-icon.bg-danger, .timeline-icon.bg-info").get(0)).offset().top - middlePosOfWindow}, 2000);
    $('#fileuploadARAPDF,#fileuploadARRPDF, #fileuploadOEDPDF, #fileuploadARRWord, #fileuploadARAWord, #fileuploadCOWord').fileupload({
        dataType: 'json',
        progressall: function (e, data) {
            var tipoDocto = $(e.target).attr('tipoDocto');
            var app = $(e.target).attr('app');
            var siglas = tipoDocto + app;
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress' + siglas).val(progress);
            if (progress == 100) {
                $("#files" + siglas, "form").html('<p class="msg"><span class="bg-success"><i class="fa fa-check"></i> Se ha almacenado el archivo</span></p>');
                setTimeout(function () {
                    $("p.msg", "#files" + siglas).fadeOut('slow');
                }, 5000);
            }
        },
        add: function (e, data) {
            var app = $(e.target).attr('app');
            var tipoDocto = $(e.target).attr('tipoDocto');
            var siglas = tipoDocto + app;
            var formato = data.files[0].type;
            if (formato === "application/pdf") {
                var mensaje = [];
                var text = "";
                $("#progress" + siglas).removeClass("hidden-xs-up");
                $("#files" + siglas, "form").html('');
                data.submit().complete(function (result, textStatus, jqXHR) {
                    if (result.reponseText === "") {
                        $("#files" + siglas).html('<div class="alert alert-danger">El JSON regres&oacute; con cadena vac&iacute;a.</div>');
                    }
                    var json = $.parseJSON(result.responseText);
                    if (json.success) {
                        $(".modal-body #downloadFile" + siglas, "#modalArticulo70").removeClass('hidden-xs-up');
                        $("#progress" + siglas).fadeOut('slow', function () {
                            $(this).addClass('hidden-xs-up').removeClass('hidden').css('display', 'block');
                        });
                    }
                });
            } else if (formato === "application/vnd.openxmlformats-officedocument.wordprocessingml.document") { // Viene de Word
                var mensaje = [];
                var text = "";
                $("#progress" + siglas).removeClass("hidden-xs-up");
                $("#files" + siglas, "form").html('');
                data.submit().complete(function (result, textStatus, jqXHR) {
                    if (result.reponseText === "") {
                        $("#files" + siglas).html('<div class="alert alert-danger">El JSON regres&oacute; con cadena vac&iacute;a.</div>');
                    }
                    var json = $.parseJSON(result.responseText);
                    if (json.success) {
                        $(".modal-body #downloadFile" + siglas, "#modalArticulo70").removeClass('hidden-xs-up');
                    } else {
                        $("#files" + siglas).html('<div class="alert alert-danger">' + mensaje + '</div>');
                    }
                    $("#progress" + siglas).fadeOut('slow', function () {
                        $(this).addClass('hidden-xs-up').removeClass('hidden').css('display', 'block');
                    });
                });
            } else {
                $("#files" + siglas).html('<div class="alert alert-danger">Formato de archivo incorrecto.<br>Solo se permiten archivos de Microsoft Word (DOCX).</div>');
            }
        }
    }).bind('fileuploadsubmit', function (e, data) {
        data.formData = {
            auditorias_id: auditorias_id,
            tipoDocto: $(e.target).attr('tipoDocto'),
            app: $(e.target).attr('app')
        }
    }
    );
    $("#modalArticulo70").on('show.bs.modal', function (e) {
        $(".modal-body .files", "#modalArticulo70").html('');
        $(".modal-body progress", "#modalArticulo70").addClass('hidden hidden-xs-up');
    });
    $('#periodo_from_label', "#modalArticulo70").datepicker({
        autoclose: true,
        minViewMode: 1,
        format: 'MM/yyyy',
        language: 'es',
    }).on('changeDate', function (selected) {
        var startDate = new Date(selected.date.valueOf());
        startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
        $('#periodo_to_label').datepicker('setStartDate', startDate);
        var inputHidden = $("#periodo_from_label").attr("data-hidden");
        var fechaISO = startDate.toISOString();
        var fecha = fechaISO.split("T");
        $("#" + inputHidden).val(fecha[0]);
    });
    $('#periodo_to_label', "#modalArticulo70").datepicker({
        autoclose: true,
        minViewMode: 1,
        format: 'MM/yyyy',
        language: 'es'
    }).on('changeDate', function (selected) {
        var FromEndDate = new Date(selected.date.valueOf());
        FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
        $('#periodo_from_label').datepicker('setEndDate', FromEndDate);
        var inputHidden = $("#periodo_to_label").attr("data-hidden");
        var fechaISO = FromEndDate.toISOString();
        var fecha = fechaISO.split("T");
        $("#" + inputHidden).val(fecha[0]);
    });
    $('#periodo_from_label', "#modalArticulo70").datepicker('setEndDate', $("#periodo_to_label").datepicker('getDate'));
    $('#periodo_to_label', "#modalArticulo70").datepicker('setStartDate', $('#periodo_from_label').datepicker('getDate'));
    $("button.btn-primary", "#modalArticulo70").on('click', function () {
        var data = $("form#frmArticulo70").serialize();
        $.post('../controlador/timeline_articulo70_guardar_data.php', data, function (json) {
            $("#periodo_from_label, #periodo_to_label, #ejercicios_auditados", "#modalArticulo70").parents('div.form-group').addClass('has-' + (json.success ? 'success' : 'danger'));
            setTimeout(function () {
                $("#periodo_from_label, #periodo_to_label, #ejercicios_auditados", "#modalArticulo70").parents('div.form-group').removeClass('has-success has-danger');
            }, 4000);
        }, "json");
    });
    $('#menu').on('click', 'a.timeline-toggle', function (e) {
        $(".timeline").css('height', '100%');
        var val = $(this).attr("data-value");
        $("i.material-icons", "a.timeline-toggle").remove();
        $(this).prepend('<i class="material-icons pull-xs-right">check</i>');
        if (val === 'stacked') {
            $('.timeline').each(function (index, element) {
                $(element).addClass('stacked');
                var lastCard = $('.timeline-card', element).last();
                var pos = lastCard.position();
                var marginBottom = lastCard.height() + 25;
                $(element).css({'height': pos.top + marginCard, 'margin-bottom': marginBottom});
                $(".etapa-nombre", element).removeClass('text-xs-center');
            });
        } else {
            $('.timeline').each(function (index, element) {
                $(element).removeClass('stacked');
                var lastCard = $('.timeline-card', element).last();
                var pos = lastCard.position();
                var marginBottom = lastCard.height() + 25;
                $(element).css({'height': pos.top + marginCard, 'margin-bottom': marginBottom});
                $(".etapa-nombre", element).addClass('text-xs-center');
            });
        }
    }).on('click', 'a.menuIconografia', function () {
        $.get("../vista/timeline_iconografia.php", {}, function (html) {
            var winBootboxIconografia = bootbox.dialog({
                message: html,
                size: 'large',
                title: "Iconografía",
                onEscape: true,
                buttons: {
                    danger: {
                        label: "Cerrar",
                        className: "btn-primary"
                    }
                }
            });
        });
    }).on('click', 'a.menuAmpliaciones, a.menuReprogramaciones', function () {
        idDocto = $(this).attr("data-idDocto");
        window.open('../vista/printDoctoHTML.php?idDocto=' + idDocto);
    }).on("click", "a.menuExportar", function () {
        var formato = $(this).attr("data-formato");
        var n = noty({
            text: 'Generando archivo ' + formato.toUpperCase() + '.<br><div class="loader-inner ball-pulse"><div></div><div></div><div></div></div>',
            layout: 'center',
            type: 'error',
            theme: 'bootstrapTheme',
            modal: true,
            //timeout: 5000
        });
        window.location.href = "timeline_exportar.php?auditorias_id=" + auditorias_id + "&formato=" + formato;
        setTimeout(function () {
            n.close()
        }, 2000);
    }).on('click', 'a.menuPNC', function () {
        var idPNC = $(this).attr("data-id-pnc");
        $.post('../vista/timeline_pnc_view.php', {idPNC: idPNC}, function (html) {
            bootbox.dialog({
                message: html,
                title: "Producto No Conforme",
                onEscape: true,
                buttons: {
                    main: {
                        label: "Cerrar",
                        className: "btn-primary"
                    }
                }
            });
        });
    });
//    $(window).on('resize', function () {
//        $('.timeline').each(function (index, element) {
//            $(element).css('height', '100%');
//            var lastCard = $('.timeline-card', element).last();
//            var pos = lastCard.position();
//            var marginBottom = lastCard.height() + 25;
//            $(element).css({'height': pos.top + marginCard, 'margin-bottom': marginBottom});
//        });
//        sticky_relocate();
//    });
    $("input.input_campo_ejecucion").each(function (index, element) {
        var isTimePicker = ($(element).attr("data-tipo-mysql") === 'DATETIME');
        $(element).daterangepicker({
            singleDatePicker: true,
//	    showWeekNumbers: true,
            timePicker: isTimePicker,
//	    showDropdowns: true,
            timePickerIncrement: 5,
            autoApply: false,
//	    parentEl: $(element).next(),
            locale: {
                format: 'YYYY-MM-DD' + (isTimePicker ? " H:mm" : ""),
                applyLabel: "Aplicar",
                cancelLabel: "Cancelar"
            },
            isInvalidDate: function (date) {
                return date.day() === 0 || date.day() === 6;
            }
        }).on('apply.daterangepicker', function (ev, picker) {
            cb(picker.startDate);
        });
    });
    $("a.campo_ejecucion").on('click', function () {
        var $this = this;
        var tareasNombre = $(this).attr("data-tareas-nombre");
        campoEjecucion = $(this).attr("data-campo-ejecucion");
        fechaProgramada = $(this).attr("data-tareas-fecha-programada");
        fechaEjecucion = $(this).attr("data-tareas-fecha-ejecucion");
        configuracionesID = $(this).attr("data-configuraciones-id");
        var input = $(this).prev('input');
        $(input).trigger('click');
        return false;
    });
// Activamos los elementos ToolTip
    if ($("[data-toggle=tooltip]").length > 0) {
        $("[data-toggle=tooltip]").tooltip({
            html: true
        });
    }

    $(".winFuncionarios").on('click', null, function () {
        idElemento = $(this).attr("id");
        var fechaConvocada = null;
        var tituloVentana = "Seleccione a los destinatarios: ";
        if (idElemento == "enviarConvocatoriaRevision") {
            fechaConvocada = $(this).prev('a.campo_ejecucion').attr('data-tareas-fecha-ejecucion');
            tituloVentana = "Reunión para el " + $(this).parents('.timeline-body').children('p.m-b-0').children('.fecha_ejecucion_valor').text();
        }
        $.post('../vista/timeline_encuesta_satisfaccion_view.php', {auditorias_id: auditorias_id, fechaConvocada: fechaConvocada, tipoEncuesta: idElemento}, function (html) {
            var winBootbox = bootbox.dialog({
                message: html,
                size: 'large',
                title: tituloVentana,
                onEscape: false,
                buttons: {
                    danger: {
                        label: "Cancelar",
                        className: "btn-danger"
                    },
                    success: {
                        label: "Enviar",
                        className: "btn-success",
                        callback: function () {
                            var btnEnviar = $("button.btn-success", ".modal-footer");
                            $(btnEnviar).html('<i class="fa fa-spinner fa-pulse fa-lg fa-fw"></i> Enviando').attr('disabled', true);
                            var correos1 = $("li.list-group-item.notification-bar-success.funcionario", "form#funcionarios").map(function () {
                                return $("a.xeditable", this).attr("data-correo");
                            });
                            var idEmpleados = $("li.list-group-item.notification-bar-success.funcionario", "form#funcionarios").map(function () {
                                return $("a.xeditable", this).attr("id").substr(6);
                            }).get();
                            var director = [];
                            var empleado_cco = $("a.xeditable", "li.empleado_cco").attr("data-correo");
                            if (idElemento == "enviarEncuestaSatisfaccion") {
                                if ($("li.director", "ul#funcionarios").length == 0) {
                                    swal('Faltó especificar al Director', 'Agregue un funcionario de tipo DIRECTOR para esta Dirección', 'error');
                                    $(btnEnviar).html('Enviar').attr("disabled", false);
                                    return false;
                                } else {
                                    director = $("a.xeditable", "ul#funcionarios  li.director  div.notification-bar-details").map(function () {
                                        return $(this).attr("data-correo");
                                    }).get();
                                    if (director.length === 0) {
                                        swal('Oops!!!', 'Falta especificar el correo electrónico del Director', 'error');
                                        $(btnEnviar).html('Enviar').attr("disabled", false);
                                        return false;
                                    }
                                }
                            }

                            var aux = $.unique(correos1);
                            for (var i = 0; i < aux.length; i++) {
                                if (aux[i] == "") {
                                    swal('Faltó especificar<br>un correo electrónico', '', 'error');
                                    return false;
                                }
                            }
                            var items = [];
                            $.each(aux, function (index, element) {
                                if ($.trim(element) != "") {
                                    items.push(element);
                                }
                            });
                            if (items.length > 0) {
                                var fechaConvocada = $("#fechaConvocada", "#winBootbox").val();
                                var deferreds = aplicarDeffered(items, auditorias_id, director, idElemento, fechaConvocada, empleado_cco, idEmpleados);
                                $.when.apply($, deferreds).done(function () {
                                    var success = false; // Inicializamos la variable
                                    var message = [];
                                    var excepcionDeDirecciones = [];
                                    $.each(arguments, function (index, arg) {
                                        if (arg[0] === "{" || $.isArray(arg)) {
                                            var stringJSON = (typeof (arg) === "string" ? arg : arg[0]);
                                            var json = jQuery.parseJSON(stringJSON);
                                            if (json.success) {
                                                success = true;
                                            }
                                            if (typeof (json.message) !== "undefined") {
                                                message.push(json.message);
                                            }
                                            for (var c = 0; c < json.data.length; c++) {
                                                if (typeof (json.data[c].enviado) !== "undefined" && !json.data[c].enviado) {
                                                    message.push(json.data[c].correo);
                                                }
                                                if (typeof (json.data[c].message) !== "undefined" && $.trim(json.data[c].message) !== "") {
                                                    excepcionDeDirecciones.push(json.data[c].message);
                                                }
                                            }
                                        }
                                    });
                                    excepcionDeDirecciones = $.unique(excepcionDeDirecciones);
                                    message = $.unique(message);
                                    if (success) {
                                        if (excepcionDeDirecciones.length > 2) {
                                            var ultimo = excepcionDeDirecciones.pop();
                                            var aux = [excepcionDeDirecciones.join(", "), ultimo];
                                            message.push(aux.join(" y "));
                                        } else {
                                            message.push(excepcionDeDirecciones.join(" y "));
                                        }
                                        $("button.btn-success", ".modal-footer").html('Enviar');
                                        winBootbox.modal("hide");
                                        swal('Correos enviados', message.join("<br>"), 'success');
                                    } else {
                                        swal('Oops!!!', message.join("<br>"), 'error');
                                    }
                                    $(btnEnviar).attr("disabled", false);
                                });
                            } else {
                                swal('Faltó especificar destinatarios', 'Debe seleccionar a los funcionarios a los que se enviar? la encuesta o especificar los correos electrónicos de los funcionarios seleccionados.', 'question');
                                $(btnEnviar).html('Enviar').attr("disabled", false);
                                return false;
                            }
                        }
                    }
                }
            });
            winBootbox.on('shown.bs.modal', function () {
                $(winBootbox).attr("id", "winBootbox");
                return false;
            });
        });
        return false;
    });
    $("a#datosAuditoria").on('click', function () {
        $.post('../vista/timeline_datos_auditoria_view.php', {id_auditoria: auditorias_id}, function (html) {
            bootbox.dialog({
                message: html,
                title: "Auditoría " + $("a#datosAuditoria").attr("nombreAuditoria"),
                onEscape: true,
                buttons: {
                    main: {
                        label: "Cerrar",
                        className: "btn-primary"
                    }
                }
            });
        });
    });
});
var miFecha = null;
function aplicarDeffered(items, auditorias_id, director, idElemento, fechaConvocada, empleado_cco, idEmpleados) {
    var deferreds = [];
    if (idElemento === "enviarEncuestaSatisfaccion") {
        for (var d = 0; d < director.length; d++) {
            deferreds.push(
                    $.post("timeline_enviar_correo.php",
                            {
                                correos: items,
                                auditorias_id: auditorias_id,
                                correoDirector: director[d],
                                idElemento: idElemento,
                                fechaConvocada: fechaConvocada,
                                empleado_cco: empleado_cco,
                                idEmpleados: idEmpleados
                            }
                    )
                    );
        }
    } else {
        deferreds.push(
                $.post("timeline_enviar_correo.php",
                        {
                            correos: items,
                            auditorias_id: auditorias_id,
                            correoDirector: director,
                            idElemento: idElemento,
                            fechaConvocada: fechaConvocada,
                            empleado_cco: empleado_cco,
                            idEmpleados: idEmpleados
                        }
                )
                );
    }
    return deferreds;
}

function cb(start) {
    var inputValue = start.format("YYYY-MM-DD H:mm");
    var diaDeLaSemana = parseInt(start.format('d')); // 1=Lunes, 7=Domingo
    if (diaDeLaSemana < 6) {
        $.post(base_url + "Timeline/guardar_fecha", {auditorias_id: auditorias_id, idConfiguraciones: configuracionesID, campoEjecucion: campoEjecucion, fecha: inputValue, fechaAlt: inputValue}, function (json) {
            if (json.success) {
                if (json.campoEjecucion.indexOf("fLimiteInfoRev1") > 0) {
                    $("span#campo_ejecucion_fLimiteInfoRev1").html(json.message);
                } else if (json.campoEjecucion.indexOf("fechaRecibeInfoRev1") > 0) {
                    var aux = json.campoEjecucion.replace(/\./ig, "-");
                    $("span.fecha_ejecucion_valor#" + aux, obj).html(json.message);
                } else {
                    var $this = $("a[data-campo-ejecucion='" + json.campoEjecucion + "']");
                    $($this).prev('input').val(json.fecha);
                    var obj = $($this).parents('div.timeline-body');
                    $("p.m-b-0", obj).removeClass('hidden-xs-up');
                    var aux = json.campoEjecucion.replace(/\./ig, "-");
                    $("span.fecha_ejecucion_valor#" + aux, obj).html(json.message);
                    var card = $($this).parents('div.timeline-card');
                    $('div.timeline-icon', card).removeClass('bg-success bg-default bd-warning bg-danger').addClass('bg-' + json.class);
                    // Mostramos u ocultamos el mensage de retraso
                    if (typeof json.message_retraso != "undefined") {
                        if ($('p.message-retraso', card).length == 1) {
                            $('p.message-retraso', card).html(json.message_retraso);
                        } else {
                            var html = '<p class="message-retraso">' + json.message_retraso + '</p>';
                            $('div.timeline-body > div.timeline-heading', card).after(html);
                            $("p.message-retraso [data-toggle=tooltip]", card).tooltip({
                                html: true
                            });
                        }
                    } else {
                        if ($('p.message-retraso', card).length == 1) {
                            $('p.message-retraso, button.productoNoConforme', card).remove();
                        }
                    }
                    $('div.timeline-icon > i.material-icons', card).html(json.icon);
                    $($this).attr('data-tareas-fecha-ejecucion', json.fecha);
                    var card2 = $($this).parents("div.timeline-card").prevAll("div.timeline-card[data-editable='true']").get(0);
                    if (card2) {
                        $('a.campo_ejecucion', card2).removeClass('hidden-xs-up');
                    }
                    if (json.campoEjecucion == "preprod_cysa.cat_auditoria_fechas.fechas_revision_avances_auditoria") {
                        $("a#enviarConvocatoriaRevision", card).removeClass('hidden-xs-up');
                    }
                }
            } else {
                swal('Oops!!!', json.message, 'error');
            }
        }, "json");
    } else {
        swal('Oops!!!', 'No se pueden elegir sábados ni domingos', 'error');
    }
    //$('input.swal2-input', 'div.swal2-container').val(start.format('DDDD, D [de] MMMMM [de] YYYY') + ' a las ' + start.format('h:mm A'));
    //$("a[data-campo-ejecucion='" + campoEjecucion + "']").data('daterangepicker').hide();
}

$(document).on("hidden.bs.modal", ".bootbox.modal#winBootbox", function () {
    $(document).off("click", 'li.funcionario .notification-bar-icon', null);
});
function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function abrirTimeline(url, data) {
    var form = document.createElement("form");
    document.body.appendChild(form);
    form.method = "POST";
    form.action = url;
    form.target = "_blank";
    var element1 = document.createElement('input');
    element1.value = data;
    element1.type = "hidden";
    element1.name = "id";
    form.appendChild(element1);
    form.submit();
}

function corregirLineaCentralDeTimeLine() {
// El DIV con clase CARD tiene un margin-top y margin-bottom de 40px; pero le resto 10 para que no quede tan exacto
// al momento de ajustar la raya de la linea de tiempo
    var marginCard = 40;
    $('.timeline').each(function (index, element) {
        var lastCard = $('.timeline-card', element).last();
        var pos = lastCard.position();
        var marginBottom = lastCard.height() + 25;
        $(element).css({'height': pos.top + marginCard, 'margin-bottom': marginBottom});
    });
}