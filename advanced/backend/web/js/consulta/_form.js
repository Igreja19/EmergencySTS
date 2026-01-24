console.log("JS DA CONSULTA CARREGADO!");

$('#triagem-select').on('change', function () {

    let triagemId = $(this).val();

    if (!triagemId) {
        $('#userprofile-id').val('');
        $('#userprofile-nome').val('');
        return;
    }

    $.get(triagemInfoUrl, { id: triagemId })
        .done(function (data) {
            console.log("Resposta AJAX:", data);

            $('#userprofile-id').val(data.userprofile_id || '');
            $('#userprofile-nome').val(data.user_nome || '');
        })
        .fail(function (err) {
            console.error("ERRO AJAX:", err);
        });
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
