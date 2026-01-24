window.addEventListener('scroll', function() {
    const nav = document.querySelector('.navbar');
    nav.classList.toggle('scrolled', window.scrollY > 20);
});

if ($(".header-carousel").length) {
    $(".header-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 800,
        items: 1,
        loop: true,
        dots: true,
        nav: false
    });
    console.log("🟢 Owl Carousel iniciado");
}