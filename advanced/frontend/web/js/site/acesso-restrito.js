document.addEventListener("DOMContentLoaded", function () {

    document.cookie = "advanced-frontend=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + window.location.hostname;
    document.cookie = "advanced-frontend=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

    let s = 10;
    const contador = document.getElementById('contador');

    const intv = setInterval(function () {
        s--;

        if (contador) {
            contador.textContent = s;
        }

        if (s <= 0) {
            clearInterval(intv);

            if (window.redirectHomeUrl) {
                window.location.href = window.redirectHomeUrl;
            }
        }
    }, 1000);

});