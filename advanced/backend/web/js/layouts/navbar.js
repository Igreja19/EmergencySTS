const navConfig = document.getElementById("navbar-config");
window.notifUrl = navConfig?.dataset.notif;
async function carregarNotificacoesNavbarReal() {
    try {
        const response = await fetch(window.notifUrl);
        const data = await response.json();

        const badge = document.getElementById('navbarNotifBadge');
        const count = document.getElementById('navbarNotifCount');
        const list  = document.getElementById('navbarNotifList');

        list.innerHTML = '';

        if (!data || data.length === 0) {
            badge.style.display = 'none';
            count.textContent = '';

            list.innerHTML = `
                <div class="text-center p-3 text-muted">
                    <i class="far fa-bell-slash fa-2x"></i>
                    <p class="mt-2">Sem novas notificações</p>
                </div>
            `;
            return;
        }

        badge.style.display = 'inline-block';
        badge.textContent = data.length;

        count.textContent = data.length + " novas";

        data.forEach(n => {
            list.innerHTML += `
                <a href="#" class="dropdown-item">
                    <i class="fas fa-exclamation-circle mr-2 text-success"></i>
                    <strong>${n.titulo}</strong>
                    <div class="small text-muted">${n.mensagem}</div>
                    <div class="small">
                        <i class="far fa-clock"></i> ${n.dataenvio}
                    </div>
                </a>
                <div class="dropdown-divider"></div>
            `;
        });

    } catch (e) {
        console.error("Erro AJAX notificações:", e);
    }
}

carregarNotificacoesNavbarReal();
setInterval(carregarNotificacoesNavbarReal, 10000);