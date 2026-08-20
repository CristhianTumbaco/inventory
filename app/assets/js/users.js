$(document).ready(function () {
    $('#usuarios').addClass('selected');
    $('#usuarios a').addClass('active');
    if ($('#table').length > 0) {
        formatDatatable('table', 0, 'asc');
    }
});
function mostrarPassword() {
    var cambio = document.getElementById("password");
    if (cambio.type == "password") {
        cambio.type = "text";
        $('.icon').text('visibility');
    } else {
        cambio.type = "password";
        $('.icon').text('visibility_off');
    }
} 