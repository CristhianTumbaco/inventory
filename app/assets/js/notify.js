const ruta_web = '/app';
const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    showCloseButton: true,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});
function Notify(tipo, mensaje) {
    if (tipo == 1) {
        Toast.fire({
            icon: "success",
            title: mensaje
        });
    } else {
        Toast.fire({
            icon: "error",
            title: mensaje
        });
    }
}
