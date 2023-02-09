/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

 define(['jquery','slick'], function ($, slick) {
    'use strict';
    $.widget('mage.coke_cans', {

        selectors: {
            cta: '#canCta',
            canToggle: '.can-toggle',
        },
        /**
         * @private
         */
        _create: function () {
            var self = this;
            self.carousel = self.element.find('.carousel');
            self._bind();
            self._startCarousel();
        },
        /* Bind Events */
        _bind: function () {
            var self = this;
            /* Show carousel once initialized */
            $(self.carousel).on('init', self._showCarousel.bind(this));
            /* toggle cans on click */
            $(self.selectors.canToggle).on('click', self._toggleCans.bind(this));
            /* redirect to selected product on click */
            $(self.selectors.cta).on('click', self._handleRedirect.bind(this));
        },
        _startCarousel: function() {
            var self = this;
            $(self.carousel).slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                dots: true,
                arrows: true,
                infinite: false,
                focusOnSelect: false,
                responsive: [
                    {
                        breakpoint: 768,
                        settings: {
                            arrows: false
                        }
                    }
                ],
            });
        },
        _showCarousel: function(){
            var self = this;
            $(self.carousel).css('opacity', '1');
        },
        _toggleCans: function(e) {
            var self = this;
            $(self.element).removeClass('coca-cola coca-cola-sans-sucres');
            $(self.element).addClass(e.currentTarget.dataset.type);
            $(self.element).data('id', e.currentTarget.dataset.id);
        },

        _handleRedirect: function(event, slick, currentSlide, nextSlide) {
            var self = this;
            var activeItem = $(self.element).find('.slick-current .item')[0];
            if ($(self.element).hasClass('coca-cola-sans-sucres')) {
                window.location.href = '/' + activeItem.dataset.url + '.html?brand_swatch='+self.options.types[1].value;
            } else {
                window.location.href = '/' + activeItem.dataset.url + '.html?brand_swatch='+self.options.types[0].value;
            }
        }
    });
    return $.mage.coke_cans;
});
