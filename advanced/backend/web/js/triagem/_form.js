const dropdownPulseira = $('#triagem-pulseira_id');
const dropdownPaciente = $('#triagem-userprofile_id');

dropdownPaciente.on('change', function () {

    const userId = $(this).val();

    dropdownPulseira.html('<option value="">A carregar...</option>');

    if (!userId) {
        dropdownPulseira.html('<option value="">Selecione primeiro o paciente</option>');
        return;
    }

    $.ajax({
        url: window.triagemPulseirasUrl,
        data: { id: userId },
        success: function (data) {

            let options = '<option value="">Selecione a pulseira</option>';

            if (!data || data.length === 0) {
                options = '<option value="">Nenhuma pulseira encontrada</option>';
            } else {
                data.forEach(function (p) {
                    options += `<option value="${p.id}">${p.codigo}</option>`;
                });
            }

            dropdownPulseira.html(options);
        }
    });
});

dropdownPulseira.on('change', function () {

    const pulseiraId = $(this).val();

    if (!pulseiraId) {
        return;
    }

    $.ajax({
        url: window.triagemDadosPulseiraUrl,
        data: { id: pulseiraId },
        success: function (data) {

            if (!data || Object.keys(data).length === 0) {
                return;
            }

            $('#triagem-prioridade_pulseira').val(data.prioridade);
            $('#triagem-motivoconsulta').val(data.motivoconsulta);
            $('#triagem-queixaprincipal').val(data.queixaprincipal);
            $('#triagem-descricaosintomas').val(data.descricaosintomas);
            $('#triagem-iniciosintomas').val(formatDateTime(data.iniciosintomas));
            $('#triagem-intensidadedor').val(data.intensidadedor);
            $('#triagem-alergias').val(data.alergias);
            $('#triagem-medicacao').val(data.medicacao);
        }
    });
});

function formatDateTime(value) {
    if (!value) return '';
    return value.replace(' ', 'T').substring(0, 16);
}
