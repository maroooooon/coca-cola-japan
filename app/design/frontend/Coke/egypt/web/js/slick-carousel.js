define([
    'jquery',
    'slick-local'
], function ($) {
    'use strict';

    $(function() {
        initSlick(
            '.image-carousel',
            1,
            1,
            true,
            0,
            false,
            0
        );
    });

    function initSlick(
        sliderElement,
        desktopCount,
        mobileCount,
        mobileCenterMode,
        mobilePadding,
        desktopCenterMode,
        desktopPadding,
        breakPoint
    ) {
        var $element = $(sliderElement);
        desktopCount = desktopCount || 4;
        mobileCount = mobileCount || 1;
        mobileCenterMode = mobileCenterMode || true;
        desktopCenterMode = desktopCenterMode || true;
        mobilePadding = mobilePadding || 0;
        desktopPadding = desktopPadding || 0;

        /**
         * Prevent each slick slider from being initialized more than once which could throw an error.
         */
        if ($element.hasClass('slick-initialized')) {
            $element.slick('unslick');
        }

        $element.slick({
            centerMode: desktopCenterMode,
            centerPadding: desktopPadding + 'px',
            slidesToShow: desktopCount,
            slidesToScroll: desktopCount,
            dots: false,
            autoplay: true,
            autoplaySpeed: 7000,
            responsive: [
                {
                    breakpoint: 767,
                    settings: {
                        arrows: true,
                        centerMode: mobileCenterMode,
                        centerPadding: mobilePadding + 'px',
                        slidesToShow: mobileCount,
                        slidesToScroll: mobileCount
                    }
                }
            ]
        });
    }

});
