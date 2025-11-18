// ======= CONFIGURAÇÃO =======
const configEl = document.getElementById("config");

// URL do SSE vinda da view (sem PHP dentro do JS)
const sseUrl = configEl?.dataset.sse;

// URL do som
const audioUrl = configEl?.dataset.sound;

// Aplica o som dinamicamente
const audio = document.getElementById("notifSound");
if (audioUrl) {
    audio.src = audioUrl;
}


// ======= Scroll effect =======
document.addEventListener("scroll", function () {
    const header = document.querySelector(".sticky-header");
    if (header) header.classList.toggle("scrolled", window.scrollY > 10);
});


let ultimoCount = 0;


// ======= SSE (Server-Sent Events) =======
function ligarSSE() {
    if (!sseUrl) {
        console.error("ERRO: SSE URL não encontrada.");
        return;
    }

    let evtSource = new EventSource(sseUrl);

    evtSource.onmessage = function(event) {
        processarNotificacoes(event.data);
    };

    evtSource.onerror = function() {
        evtSource.close();
        setTimeout(ligarSSE, 3000);
    };
}


// ======= Processar notificações =======
function processarNotificacoes(rawData) {
    const data = JSON.parse(rawData);
    const count = data.length;

    const bell = document.querySelector(".notif-btn");
    const list = document.querySelector(".notif-body");
    const headerBadge = document.querySelector(".notif-header .badge");

    if (!list) return;

    list.innerHTML = "";

    // Quando não há notificações
    if (count === 0) {
        list.innerHTML = `
            <div class='text-center text-muted py-3'>
                <i class='bi bi-inbox fs-2 mb-2'></i>
                <small>Sem novas notificações</small>
            </div>
        `;

        if (headerBadge) headerBadge.remove();
        const red = bell.querySelector(".notif-badge");
        if (red) red.remove();

        ultimoCount = 0;
        return;
    }

    // Badge vermelha do sino
    if (!bell.querySelector(".notif-badge")) {
        bell.insertAdjacentHTML("beforeend", "<span class='notif-badge'></span>");
    }

    // Badge do header
    if (headerBadge) {
        headerBadge.textContent = count;
    } else {
        document.querySelector(".notif-header").insertAdjacentHTML(
            "beforeend",
            `<span class='badge bg-success rounded-pill'>${count}</span>`
        );
    }

    // Render das notificações
    data.forEach(n => {
        list.insertAdjacentHTML("beforeend", `
            <div class='notif-item d-flex p-2 mb-1 rounded-3'>
                <div class='notif-icon me-2'>
                    <i class='bi bi-exclamation-circle-fill text-success fs-5'></i>
                </div>
                <div class='flex-grow-1'>
                    <div class='fw-semibold'>${n.titulo}</div>
                    <div class='text-muted small'>${n.mensagem}</div>
                </div>
            </div>
        `);
    });

    // Tocar som se aumentou o count
    if (count > ultimoCount) {
        audio.currentTime = 0;
        audio.play();
    }

    ultimoCount = count;
}


// Iniciar SSE
ligarSSE();
