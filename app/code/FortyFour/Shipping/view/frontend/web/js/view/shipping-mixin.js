define([
    'jquery',
    'FortyFour_Shipping/js/action/express-standard/save-delivery-comment'
], function ($, saveExpressStandardDeliveryComment) {
    'use strict';

    return function (target) {
        return target.extend({

            /**
             * Set shipping information handler
             */
            setShippingInformation: function () {
                var deliveryComment = $('textarea[name="express_standard_delivery_comment"]').val();

                if (deliveryComment) {
                    saveExpressStandardDeliveryComment.save(deliveryComment);
                }

                this._super();
            }
        });
    }
});
