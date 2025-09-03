jQuery(document).ready(function($) {
    if($('.duydev-carousel').length <= 0) {
        return;
    }

    const treatmentsSwiper = new Swiper('.treatments-slider', {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        // autoplay: {
        //     delay: 5000,
        //     disableOnInteraction: false,
        // },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            640: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 5,
            },
        }
    });
});
