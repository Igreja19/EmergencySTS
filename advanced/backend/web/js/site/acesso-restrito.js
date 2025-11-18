const redirectConfig = document.getElementById("redirect-config");
const targetUrl = redirectConfig?.dataset.url;

// Redirecionamento com contador
let s = 10;
let intv = setInterval(() => {
    s--;
    document.getElementById('contador').textContent = s;

    if (s <= 0) {
        clearInterval(intv);
        window.location.href = targetUrl;
    }
}, 1000);