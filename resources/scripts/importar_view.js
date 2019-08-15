$(document).ready(function () {
    $("#btn-importar").click(function () {
        let d = $("#frm-importar").serializeArray();
        var urls = [];
        $.each(d, function (index, element) {
            urls.push(element.value);
        });
        ajaxRequest(urls);
    });
});

function ajaxRequest(urls) {
    if (urls.length > 0) {
        var element = urls.shift();
        var url = base_url + controller + "/iniciar_importacion";
        $("#" + element)
                .html('<i class="fa fa-spinner fa-pulse fa-fw"></i>')
                .removeClass();
        let data = {'catalogo[]': element};
        $.post(url, data, function (json) {
            if (json.success) {
                $("#" + json.id)
                        .html('<i class="material-icons" aria-hidden="true">check</i>')
                        .addClass("label label-success");
            }
            if (json.message != "") {
                $("#error-message").html(json.message);
            }
            ajaxRequest(urls);
        }, "json");
    }
}