document.addEventListener("DOMContentLoaded", function() {

    if (window.firstLoginProfileUrl) {
        Swal.fire({
            title: 'Bem-vindo!',
            text: 'Antes de iniciar, por favor preencha o seu perfil para continuar.',
            icon: 'info',
            confirmButtonText: 'Ok, preencher agora',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then(() => {
            window.location.href = window.firstLoginProfileUrl;
        });
    }
});