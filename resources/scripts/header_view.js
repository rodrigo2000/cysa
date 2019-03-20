$(document).ready(function () {
    $('select.mis_auditorias_anio').change(function () {
        var url = base_url + controller + '/get_mis_auditorias';
        $("select.mis_auditorias_id").html('<option value="0" selected="selected">Cargando auditorías...</option>');
        $.post(url, {auditorias_anio: this.value, auditorias_id: auditorias_id}, function (json) {
            var html = '<option value="0">SELECCIONE</option>';
            var APs = '', ICs = '';
            if (json.auditorias_AP.length > 0) {
                APs += '<optgroup label="AUDITORIÁS (AP/AE/SA)">';
                $.each(json.auditorias_AP, function (index, element) {
                    var label = element.numero_auditoria;
                    if (label == null) {
                        label = "S/N - " + element.auditorias_rubro;
                    }
                    APs += '<option value="' + element.auditorias_id + '" ' + (auditorias_id == element.auditorias_id ? 'selected="selected"' : '') + ' title="' + (!isEmpty(element.objetivo) ? element.objetivo : '') + '">' + label + '</option>';
                });
                APs += '</optgroup>';
            }
            html += APs;
            if (json.auditorias_IC.length > 0) {
                ICs += '<optgroup label="INTERVENCIÓN DE CONTROL">';
                $.each(json.auditorias_IC, function (index, element) {
                    var label = element.numero_auditoria;
                    if (label == null) {
                        label = "S/N - " + element.auditorias_rubro;
                    }
                    ICs += '<option value="' + element.auditorias_id + '" title="' + (!isEmpty(element.objetivo) ? element.objetivo : '') + '">' + label + '</option>';
                });
                ICs += '</optgroup>';
            }
            html += ICs;
            $("select.mis_auditorias_id").html(html);
        }, "json");
    });

    $(".mis_auditorias_id").change(function () {
        let id = this.value;
        let url = base_url + controller + "/" + id;
        window.location = url;
    });
});
