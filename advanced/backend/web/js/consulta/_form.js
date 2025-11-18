// AJAX preencher paciente
$('#triagem-select').on('change', function () {
    var triagemId = $(this).val();

    if (triagemId) {
        $.ajax({
            url: window.triagemInfoUrl,
            data: { id: triagemId },
            success: function (data) {

                $('#userprofile-id').val(data.userprofile_id || '');
                $('#userprofile-nome').val(data.user_nome || '');
            }
        });
    } else {
        $('#userprofile-id').val('');
        $('#userprofile-nome').val('');
    }
});

// Mostrar/esconder campo de encerramento
$('#estado-select').on('change', function () {
    if ($(this).val() === 'Encerrada') {
        $('#campo-encerramento').slideDown();
    } else {
        $('#campo-encerramento').slideUp();
        $('#consulta-data_encerramento').val('');
    }
});

