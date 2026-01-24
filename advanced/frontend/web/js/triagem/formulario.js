document.addEventListener('DOMContentLoaded', function () {
    const campoData = document.querySelector('#triagem-iniciosintomas');

    if (campoData) {
        const anoAtual = new Date().getFullYear();
        const anoMinimo = anoAtual - 100;

        campoData.addEventListener('input', function () {
            const valor = campoData.value;

            if (valor.length >= 4) {
                const ano = parseInt(valor.substring(0, 4));

                if (isNaN(ano) || ano < anoMinimo || ano > anoAtual) {
                    campoData.setCustomValidity(
                        `O ano deve estar entre ${anoMinimo} e ${anoAtual}.`
                    );
                } else {
                    campoData.setCustomValidity("");
                }
            }
        });
    }
});

document.querySelector('#form-triagem').addEventListener('submit', function() {
    const btn = document.querySelector('.submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> A enviar...';
});