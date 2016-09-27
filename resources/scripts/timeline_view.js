$(document).ready(function () {
    var h = $(".timeline").height() - $('.timeline-card:last-child .timeline-content', '.timeline').height();
    $(".timeline").css('height', h - 5);

    $('.timeline-toggle .btn').on('click', function (e) {
        $(".timeline").css('height', '100%');
        var val = $(this).find('input').val();
        $("label", ".timeline-toggle").removeClass("active");
        $(this).addClass("active");
        if (val === 'stacked') {
            $('.timeline').addClass('stacked');
        } else {
            $('.timeline').removeClass('stacked');
            var h = $(".timeline").height() - $('.timeline-card:last-child .timeline-content', '.timeline').height();
            $(".timeline").height(h - 5);
        }
    });

    $("#btnShowHiddenCards").on("click", function () {
        if ($("#tareas_pendientes").css("display") == "none") {
            $(this).html("Ocultar tareas pendientes");
        } else {
            $(this).html("Mostrar tareas pendientes");
        }
        $("#tareas_pendientes").slideToggle();
    });

    if ($("button.btn.productoNoConforme").length) {
        $("button.btn.productoNoConforme").on("click", function () {
            $.post(base_url + 'Productos/cargar_producto_no_conforme', {}, function (html) {
                bootbox.dialog({
                    title: 'Nuevo Producto No Conforme',
                    message: html,
                    onEscape: true,
                    size: 'large',
                    backdrop: true,
                    buttons: {
                        danger: {
                            label: "Cancelar!",
                            className: "btn-default",
                            callback: function () {
                            }
                        },
                        success: {
                            label: "Guardar",
                            className: "btn-success",
                            callback: function () {

                            }
                        }
                    }
                });
            });
        });
    }
});