document.addEventListener("DOMContentLoaded", function () {
    if (window.contaDesativada) {
        Swal.fire({
            title: 'Conta desativada',
            text: 'A sua conta encontra-se desativada. Caso desconheça o motivo desta situação, por favor contacte o suporte.',
            icon: 'warning',
            confirmButtonText: 'Entendi',
            allowOutsideClick: false,
            allowEscapeKey: false
        });
    }
});
