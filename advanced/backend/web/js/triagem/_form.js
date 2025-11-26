const dropdownPulseira = $('#triagem-pulseira_id');

$('#triagem-userprofile_id').on('change', function() {

    const userId = $(this).val();

    dropdownPulseira.html('<option value="">A carregar...</option>');

    if (!userId) {
        dropdownPulseira.html('<option value="">Selecione primeiro o paciente</option>');
        return;
    }

    $.ajax({
        url: window.triagemPulseirasUrl,
        data: { id: userId },
        success: function(data) {

            let options = '<option value="">Selecione a pulseira</option>';

            if (data.length === 0) {
                options = '<option value="">Nenhuma pulseira encontrada</option>';
            } else {
                data.forEach(function(p) {
                    options += `<option value="${p.id}">${p.codigo}</option>`;
                });
            }

            dropdownPulseira.html(options);
        }
    });
});

