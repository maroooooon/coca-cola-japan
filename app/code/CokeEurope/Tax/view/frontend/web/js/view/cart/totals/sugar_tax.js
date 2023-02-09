define([
    'Magento_Checkout/js/view/summary/cart-items',
    'Magento_Catalog/js/price-utils'
], function (Component, priceUtils) {
    'use strict';

    return Component.extend({

        /* This function is checking if there is any sugar tax in the cart. */
        isSugarTaxEnabled: function () {
            if (this.getSugarTaxAmount()) {
                return true;
            };

            return false;
        },

         /* Calculating the total sugar tax amount for all items in the cart. */
         getSugarTaxAmount: function() {
             if (window.checkoutConfig.totalsData.sugar_tax_total) {
                 return priceUtils.formatPrice(window.checkoutConfig.totalsData.sugar_tax_total, false);
             }

             return null;
         }
    });
});
