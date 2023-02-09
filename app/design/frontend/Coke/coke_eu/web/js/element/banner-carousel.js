define([
    'jquery',
    'Magento_PageBuilder/js/events',
    'slick'
], function ($, events) {
    'use strict';

    $(function() {
        initSlick('.global-top-banner .pagebuilder-column-group', 3, 3, 1, 0, true);
    });

    function initSlick (sliderElement, desktopCount, laptopCount, mobileCount, stagePadding, infinite, arrows, dots) {
        var $element = $(sliderElement);
        desktopCount = desktopCount|| 4;
        laptopCount = laptopCount || 3;
        mobileCount = mobileCount || 1;
        stagePadding = stagePadding || 20;
        infinite = infinite || false;
        arrows = arrows || false;
        dots = dots || false;

        /**
         * Prevent each slick slider from being initialized more than once which could throw an error.
         */
        if ($element.hasClass('slick-initialized')) {
            $element.slick('unslick');
        }

        $element.slick({
            centerMode: false,
            slidesToShow: desktopCount,
            slidesToScroll: desktopCount,
            dots: dots,
            infinite: infinite,
            autoplay: true,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        arrows: arrows,
                        centerMode: false,
                        slidesToShow: laptopCount,
                        slidesToScroll: laptopCount,
                        dots: dots,
                    }
                },
                {
                    breakpoint: 767,
                    settings: {
                        arrows: arrows,
                        centerMode: false,
                        centerPadding: stagePadding + 'px',
                        slidesToShow: mobileCount,
                        slidesToScroll: mobileCount,
                        dots: dots,
                    }
                }
            ]
        });
    }

});
