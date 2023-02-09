define(["jquery", "Magento_PageBuilder/js/events", "slick"], function (
    $,
    events
) {
    "use strict";

    $(function () {
        initSlick(
            ".product-carousel .product-items",
            3,
            2,
            1,
            0,
            false,
            true,
            false
        );
    });

    function initSlick(
        sliderElement,
        desktopCount,
        laptopCount,
        mobileCount,
        stagePadding,
        infinite,
        arrows,
        dots
    ) {
        var $element = $(sliderElement);
        desktopCount = desktopCount || 3;
        laptopCount = laptopCount || 3;
        mobileCount = mobileCount || 2;
        stagePadding = stagePadding || 20;
        infinite = infinite || false;
        arrows = arrows || false;
        dots = dots || !arrows;

        /**
         * Prevent each slick slider from being initialized more than once which could throw an error.
         */
        if ($element.hasClass("slick-initialized")) {
            $element.slick("unslick");
        }

        $element.slick({
            centerMode: false,
            slidesToShow: desktopCount,
            slidesToScroll: desktopCount,
            dots: dots,
            infinite: infinite,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        arrows: arrows,
                        centerMode: false,
                        slidesToShow: laptopCount,
                        slidesToScroll: laptopCount,
                        dots: dots,
                    },
                },
                {
                    breakpoint: 767,
                    settings: {
                        arrows: arrows,
                        centerMode: false,
                        centerPadding: stagePadding + "px",
                        slidesToShow: mobileCount,
                        slidesToScroll: mobileCount,
                        dots: dots,
                    },
                },
            ],
        });
    }
});
