define([
    'jquery',
    'mage/storage',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote'
], function ($, storage, customer, quote) {
    'use strict';

    var saveExpressStandardDeliveryCommentEndpoint = 'rest/V1/carts/mine/express-standard-delivery-comment',
        guestSaveExpressStandardDeliveryCommentEndpoint = 'rest/V1/guest-carts/%cartId/express-standard-delivery-comment';

    return {

        getSaveUrl: function (quoteId) {
            if (customer.isLoggedIn()) {
                return saveExpressStandardDeliveryCommentEndpoint;
            }

            return guestSaveExpressStandardDeliveryCommentEndpoint.replace('%cartId', quoteId);
        },

        /**
         * Save the Express Standard Delivery Comment
         *
         * @param quoteId
         * @param deliveryComment
         */
        save: function (deliveryComment) {
            var payload = { 'delivery_comment': deliveryComment };

            $('body').trigger('processStart');

            return storage.put(
                this.getSaveUrl(quote.getQuoteId()),
                JSON.stringify(payload)
            ).success(
                function (response) {
                    return response;
                }
            ).always(
                function () {
                    $('body').trigger('processStop');
                }
            );
        }
    };
});

