document.addEventListener("DOMContentLoaded", function () {

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