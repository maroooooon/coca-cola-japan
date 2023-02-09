define([
    'jquery',
    'underscore',
    'matchMedia',
    'mage/mage'
], function ($, _, mediaCheck) {
    'use strict';

    $(function() {
        sticky('.page-header', '.page-wrapper');
    });

    /**
     *
     * @param element
     * @param container
     * @param offsetTop
     */
    function sticky(element, container, offsetTop) {
        let elem = $(element);
        container = container || '#maincontent';
        offsetTop = offsetTop || 0;
        if(!elem.length) {
            return;
        }

        let sticky = elem.get(0).offsetTop;
        //Check on scroll
        window.onscroll = function() {stickyCheck()};

        function stickyCheck() {
            if (window.pageYOffset > sticky) {
                elem.addClass("sticky");
                elem.css('top', offsetTop);
                $(container).css('padding-top', elem.outerHeight(true));

                mediaCheck({
                    media: '(min-width: 768px)',
                    // Switch to Desktop Version
                    entry: function () {
                        $('.sections.nav-sections').attr('style', '');
                    },
                    // Switch to Mobile Version
                    exit: function () {
                        let maxHeight = $(window).height()- elem.height();
                        $('.sections.nav-sections').css('top', elem.height()).css('position', 'absolute').css('max-height',maxHeight);
                    }
                });
            } else {
                elem.removeClass("sticky");
                $(container).css('padding-top', 0);
            }
        }
    }

});
