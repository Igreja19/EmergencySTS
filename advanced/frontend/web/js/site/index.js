document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
        title: 'Bem-vindo!',
        text: 'Antes de iniciar, por favor preencha o seu perfil para continuar.',
        icon: 'info',
        confirmButtonText: 'Ok, preencher agora',
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "<?= $profileUrl ?>";
        }
    });
});