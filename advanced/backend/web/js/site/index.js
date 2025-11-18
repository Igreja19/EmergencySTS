// ===============================
// LER URL DA VIEW
// ===============================
const cfg = document.getElementById("chart-config");
const dadosUrl = cfg?.dataset.url;

// ===============================
// BUSCAR DADOS AO CONTROLLER
// ===============================
async function carregarGraficos() {
    if (!dadosUrl) return;

    try {
        const resp = await fetch(dadosUrl);
        const dados = await resp.json();

        const { manchester, evolucaoLabels, evolucaoData } = dados;

        // ===============================
        // DONUT — Prioridades Manchester
        // ===============================
        const donut = document.getElementById("chartManchester");

        if (donut) {
            new Chart(donut, {
                type: "doughnut",
                data: {
                    labels: ["Vermelho", "Laranja", "Amarelo", "Verde", "Azul"],
                    datasets: [{
                        data: [
                            manchester.vermelho,
                            manchester.laranja,
                            manchester.amarelo,
                            manchester.verde,
                            manchester.azul
                        ],
                        backgroundColor: ["#dc3545", "#fd7e14", "#ffc107", "#198754", "#0d6efd"]
                    }]
                },
                options: {
                    plugins: { legend: { position: "bottom" } }
                }
            });
        }

        // ===============================
        // LINHA — Evolução das Triagens
        // ===============================
        const line = document.getElementById("chartEvolucao");

        if (line) {
            new Chart(line, {
                type: "line",
                data: {
                    labels: evolucaoLabels,
                    datasets: [{
                        label: "Triagens",
                        data: evolucaoData,
                        tension: 0.35,
                        borderColor: "#198754",
                        backgroundColor: "rgba(25,135,84,0.1)",
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: "#198754"
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                callback: (value) =>
                                    Number.isInteger(value) ? value : ""
                            }
                        }
                    }
                }
            });
        }

    } catch (e) {
        console.error("Erro a carregar gráficos", e);
    }
}

// Executar
carregarGraficos();
