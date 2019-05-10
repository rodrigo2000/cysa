$(document).on('change', "span.opciones", function (event, opcion) {
    console.log(opcion);
    if (opcion != 2) {
        $("span.si_no").addClass('hidden-xs-up');
    } else {
        $("span.si_no").removeClass('hidden-xs-up');
    }
});