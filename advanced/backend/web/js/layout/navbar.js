console.log("navbar.js carregado");

$(document).on('click', '.notif-btn', function (e) {
    e.preventDefault();
    console.log("Clique no sino detectado!");

    let container = $('.notif-body');
    let url = $(this).data('url');

    console.log("URL a carregar:", url);

    container.html(`
        <div class="text-center p-3 text-muted">
            <i class="fas fa-spinner fa-spin"></i> A carregar...
        </div>
    `);

    $.get(url, function (html) {
        console.log("HTML recebido:");
        container.html(html);
    });
});

// Marcar como lida sem fechar dropdown
$(document).on("click", ".marcar-lida", function (e) {
    e.preventDefault();
    e.stopPropagation();

    let url = $(this).data("url");
    let id = $(this).data("id");
    let item = $(this).closest(".notif-item");

    $.post(url + "?id=" + id, (res) => {
        if (res.success) {
            item.fadeOut(200, function () { $(this).remove() });
        }
    });
});
