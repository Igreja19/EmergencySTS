// DONUT
fetch('/dashboard/manchester')
    .then(response => response.json())
    .then(data => {
        const donut = document.getElementById("chartManchester");

        if (donut) {
            new Chart(donut, {
                type: "doughnut",
                data: {
                    labels: ["Vermelho", "Laranja", "Amarelo", "Verde", "Azul"],
                    datasets: [{
                        data: [
                            data.vermelho,
                            data.laranja,
                            data.amarelo,
                            data.verde,
                            data.azul
                        ],
                        backgroundColor: ["#dc3545", "#fd7e14", "#ffc107", "#198754", "#0d6efd"]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: "bottom" }},
                    cutout: "65%"
                }
            });
        }
    });


// LINHA – EVOLUÇÃO DAS TRIAGENS
const line = document.getElementById("chartEvolucao");
let triagemChart = null;

if (line) {
    triagemChart = new Chart(line, {
        type: "line",
        data: {
            labels: '.json_encode($evolucaoLabels).',
            datasets: [{
                label: "Triagens",
                data: '.json_encode($evolucaoData).',
                tension: 0.3,
                borderColor: "#198754",
                backgroundColor: "rgba(25,135,84,0.15)",
                fill: true,
                pointRadius: 5,
                pointBackgroundColor: "#198754",
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false }},
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0   // 👈 garante números inteiros
                    }
                }
            }
        }
    });
}