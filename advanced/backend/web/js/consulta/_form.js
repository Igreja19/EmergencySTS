// AJAX para preencher nome do paciente
$('#triagem-select').on('change', function() {
    let triagemId = $(this).val();

    if (!triagemId) {
        $('#userprofile-id').val('');
        $('#userprofile-nome').val('');
        return;
    }

    $.get('$triagemInfoUrl', {id: triagemId}, function(data) {
        $('#userprofile-id').val(data.userprofile_id || '');
        $('#userprofile-nome').val(data.user_nome || '');
    });
});

// Mostrar/esconder campo de encerramento
$('#estado-select').on('change', function() {
    if ($(this).val() === 'Encerrada') {
        $('#campo-encerramento').slideDown();
    } else {
        $('#campo-encerramento').slideUp();
        $('#consulta-data_encerramento').val('');
    }
});