$(document).ready(function () {
    $("#auditorias_is_sin_observaciones").on('change', function () {
        var url = base_url + controller + "/actualizar_campo";
        var data = {
            campo: 'auditorias_is_sin_observaciones',
            valor: this.checked ? 1 : 0,
            auditorias_id: auditorias_id
        };
        var $this = this;
        $.post(url, data, function (json) {
            if (json.success) {
                if ($this.checked) {
                    $("span.labelauty-checked-image", "label[for=auditorias_is_sin_observaciones]")
                            .addClass("faa-tada animated")
                            .css('animation-iteration-count', 1)
                            .removeClass;
                } else {
                    $("span.labelauty-checked-image", "label[for=auditorias_is_sin_observaciones]")
                            .removeClass("faa-tada animated");
                }
            }
        }, "json");
    });
});