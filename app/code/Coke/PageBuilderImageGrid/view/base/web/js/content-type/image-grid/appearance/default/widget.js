/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define(["jquery", "underscore", "slick"], function ($, _) {
    "use strict";

    return function (config, element) {
        // Skip if mobile carousel is disabled
        if (!element.data("carousel")) return;
        var carousel = element.find(".image-grid-items");

        // Function to check the screen size and init or destroy the carousel
        function checkScreen() {
            var screenWidth = $(window).width();
            // Remove slick if screen width is 768px or greater
            if (screenWidth > 767) {
                if (!carousel.hasClass("slick-initialized")) return;
                carousel.slick("unslick");
            }
            // Init slick if screen width is 767px or less
            if (screenWidth <= 767) {
                if (carousel.hasClass("slick-initialized")) return;
                carousel.slick({
                    slidesToShow: 1,
                    centerMode: true,
                    centerPadding: "12%",
                });
            }
        }
        // Check screen on dom ready & window resize / orientation change
        $(document).ready(checkScreen);
        $(window).on("resize orientationchange", _.debounce(checkScreen, 250));
    };
});
