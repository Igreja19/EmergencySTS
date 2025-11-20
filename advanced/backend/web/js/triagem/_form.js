$('#triagem-userprofile_id').on('change', function() {
    const userId = $(this).val();

    // Limpar dropdown
    $('#dropdown-pulseiras').html('<option value="">A carregar...</option>');

    if (!userId) {
        $('#dropdown-pulseiras').html('<option value="">Selecione primeiro o paciente</option>');
        return;
    }

    $.ajax({
        url: '/triagem/pulseiras-por-paciente',
        data: { id: userId },
        success: function(data) {
            let options = '<option value="">Selecione a pulseira</option>';

            if (data.length === 0) {
                options = '<option value="">Nenhuma pulseira encontrada</option>';
            } else {
                data.forEach(function(p) {
                    options += `<option value="\${p.id}">\${p.codigo}</option>`;
                });
            }

            $('#dropdown-pulseiras').html(options);
        }
    });
});