
define([
    'jquery',
    'ko',
    'underscore',
    'uiComponent',
], function (
    $,
    ko,
    _,
    Component,
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/payment-additional-message',
        },
        initialize: function(){
            this._super();
            return this;
        },
        addOrderButton: function() {
            //mobile
            if (navigator.userAgent.match(/iPhone|Android.+Mobile/)) {
                $('.js_add_contents').remove()

                let order_button = $('.action.primary.checkout').clone(true)
                $(order_button).css('margin-top','10px').addClass('js_add_contents').insertBefore('.payment-additional-message')
            }
        }
    });
});
