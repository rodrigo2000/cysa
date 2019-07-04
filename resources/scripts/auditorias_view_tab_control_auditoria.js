$(document).ready(function () {
    $("#auditorias_representante_designado").on('change', function () {
        if (this.checked) {
            asignar_enlace_designado(null);
            $("#show-hide-label-enlace-designado").slideUp('slow', function () {
                $("#label-enlace-designado").html('SELECCIONAR');
                $("#show-hide-asignar-enlace-designado").slideUp('slow');
            });

        } else {
            $("#show-hide-label-enlace-designado").slideDown('slow');
        }
    });

    $("#btn-editar-enlace-designado").click(function () {
        $("#show-hide-asignar-enlace-designado").slideDown('slow');
    });

    if ($("#empleados_involucrados").length > 0) {
        $("#equipo_auditoria, #permisos_adicionales, #empleados_involucrados").multiSelect({
            keepOrder: true,
            noneSelectedText: 'Ninguno',
            selectableHeader: '<input type="text" class="search-input form-control" autocomplete="off" placeholder="Buscar empleado">',
            selectionHeader: '<input type="text" class="search-input form-control" autocomplete="off" placeholder="Buscar empleado">',
            afterInit: function (ms) {
                var that = this,
                        $selectableSearch = that.$selectableUl.prev(),
                        $selectionSearch = that.$selectionUl.prev(),
                        selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                        selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

                that.qs1 = $selectableSearch.quicksearch(selectableSearchString).on('keydown', function (e) {
                    if (e.which === 40) {
                        that.$selectableUl.focus();
                        return false;
                    }
                });

                that.qs2 = $selectionSearch.quicksearch(selectionSearchString).on('keydown', function (e) {
                    if (e.which == 40) {
                        that.$selectionUl.focus();
                        return false;
                    }
                });
            },
            afterSelect: function () {
                this.qs1.cache();
                this.qs2.cache();
            },
            afterDeselect: function () {
                this.qs1.cache();
                this.qs2.cache();
            }
        });
    }

    $("button#btn-asignar-enlace-designado").click(function (event) {
        let empleados_id = $("select#auditorias_enlace_designado").val();
        asignar_enlace_designado(empleados_id);
        return false;
    });

//    $("#empleados_involucrados").change(function () {
//        $("#empleados_involucrados").multiSelect('refreshRightList');
//    });

    $("#btn-actualizar-empleados-involucrados").click(function () {
        let url = base_url + controller + "/set_empleados_involucrados";
        var a = $("#empleados_involucrados").val();
        let data = {
            'empleados_id': a
        }
        $(this).html(ICON_SPINNER).prop('disabled', true);
        $.post(url, data, function (json) {
            if (json.success) {
                $("#btn-actualizar-empleados-involucrados").html("Involucrados actualizados").removeClass('btn-primary').addClass('btn-success');
                setTimeout(function () {
                    $("#btn-actualizar-empleados-involucrados")
                            .removeClass("btn-success")
                            .addClass("btn-primary")
                            .html('Actualizar involucrados')
                            .prop('disabled', false);
                }, 3000);
            } else {
                alert(json.message);
                $("#btn-actualizar-empleados-involucrados").html('Actualizar involucrados').prop('disabled', false);
            }
        }, "json");
    });

    $("#btn-actualizar-equipo-auditoria").click(function () {
        let url = base_url + controller + "/set_equipo_de_auditoria";
        var a = $("#equipo_auditoria").val();
        let data = {
            'empleados_id': a
        }
        $(this).html(ICON_SPINNER).prop('disabled', true);
        $.post(url, data, function (json) {
            if (json.success) {
                $("#btn-actualizar-equipo-auditoria").html("Equipo actualizado").removeClass('btn-primary').addClass('btn-success');
                setTimeout(function () {
                    $("#btn-actualizar-equipo-auditoria")
                            .removeClass("btn-success")
                            .addClass("btn-primary")
                            .html('Actualizar equipo')
                            .prop('disabled', false);
                }, 3000);
            } else {
                alert(json.message);
                $("#btn-actualizar-equipo-auditoria").html('Actualizar equipo').prop('disabled', false);
            }
        }, "json");
    });

    $("#btn-actualizar-permisos-adicionales").click(function () {
        let url = base_url + controller + "/set_permisos_adicionales";
        var a = $("#permisos_adicionales").val();
        let data = {
            'empleados_id': a
        }
        $(this).html(ICON_SPINNER).prop('disabled', true);
        $.post(url, data, function (json) {
            if (json.success) {
                $("#btn-actualizar-permisos-adicionales").html("Permisos actualizados").removeClass('btn-primary').addClass('btn-success');
                setTimeout(function () {
                    $("#btn-actualizar-permisos-adicionales")
                            .removeClass("btn-success")
                            .addClass("btn-primary")
                            .html('Actualizar permisos')
                            .prop('disabled', false);
                }, 3000);
            } else {
                alert(json.message);
                $("#btn-actualizar-permisos-adicionales").html('Actualizar permisos').prop('disabled', false);
            }
        }, "json");
    });
});
$('a.actualizar_campo').on('click', function (e) {
    var campo = $(e.target).attr("data-campo");
    var valor = $("#" + campo).val();
    var url = base_url + controller + "/actualizar_campo";
    var data = {
        campo: campo,
        valor: valor,
        auditorias_id: $("select[name=mis_auditorias_id]").val()
    }
    $(e.target).html(ICON_SPINNER).addClass('disabled');
    $.post(url, data, function (json) {
        $(e.target).html('Actualizar').removeClass('disabled');
        if (!json.success) {
            alert(json.message);
        }
    }, "json");
});

function asignar_enlace_designado(empleados_id) {
    let data = {
        empleados_id: empleados_id
    };
    let url = base_url + controller + '/asignar_enlace_designado';
    $.post(url, data, function (json) {
        $("#label-enlace-designado").html(json.empleado.empleados_nombre_titulado_siglas);
        $("#show-hide-asignar-enlace-designado").slideUp('slow');
    }, "json");
}

function convertir_a_multi_select(elemento) {
    $(elemento).multiSelect({
        keepOrder: true,
        noneSelectedText: 'Ninguno',
        selectableHeader: '<input type="text" class="search-input form-control" autocomplete="off" placeholder="Buscar empleado">',
        selectionHeader: '<input type="text" class="search-input form-control" autocomplete="off" placeholder="Buscar empleado">',
        afterInit: function (ms) {
            var that = this,
                    $selectableSearch = that.$selectableUl.prev(),
                    $selectionSearch = that.$selectionUl.prev(),
                    selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                    selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

            that.qs1 = $selectableSearch.quicksearch(selectableSearchString).on('keydown', function (e) {
                if (e.which === 40) {
                    that.$selectableUl.focus();
                    return false;
                }
            });

            that.qs2 = $selectionSearch.quicksearch(selectionSearchString).on('keydown', function (e) {
                if (e.which == 40) {
                    that.$selectionUl.focus();
                    return false;
                }
            });
        },
        afterSelect: function () {
            this.qs1.cache();
            this.qs2.cache();
        },
        afterDeselect: function () {
            this.qs1.cache();
            this.qs2.cache();
        }
    });
}