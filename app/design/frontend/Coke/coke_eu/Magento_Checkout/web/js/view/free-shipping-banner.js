define([
    'jquery',
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils'
], function ($, Component, ko, quote, priceUtils) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/free-shipping-banner'
        },

        isEnabled: ko.observable(false),
        amount : ko.observable(),
        amountAsPrice : ko.observable(),

        /**
         * @param config
         */
        initialize: function(config) {
            this._super();
            this.isEnabled(this.is_enabled);

            var totals = quote.getTotals()();
            if (totals && totals.subtotal &&  this.threshold && this.threshold !== 0) {
               this.amount(this.calculateAmountToFreeShipping(totals.subtotal, this.threshold));
               this.amountAsPrice(this.formatAsPrice(this.calculateAmountToFreeShipping(totals.subtotal, this.threshold)));
            }
        },

        /**
         *
         * @param subtotal
         * @param threshold
         * @returns {number}
         */
        calculateAmountToFreeShipping(subtotal, threshold){
            return threshold - subtotal;
        },

        /**
         *
         * @param number
         * @returns {*|string}
         */
        formatAsPrice(number){
            return priceUtils.formatPrice(number, quote.getPriceFormat());
        }
    });
});
