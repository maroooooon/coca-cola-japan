define([
        'ko',
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'uiRegistry',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Ui/js/model/messages',
        'uiLayout',
        'Magento_Checkout/js/action/redirect-on-success'
    ], function (
    ko,
    $,
    Component,
    placeOrderAction,
    selectPaymentMethodAction,
    quote,
    customer,
    paymentService,
    checkoutData,
    checkoutDataResolver,
    registry,
    additionalValidators,
    Messages,
    layout,
    redirectOnSuccessAction
    ) {
        'use strict';

        var mixin = {
            isChecked: ko.computed(function () {
                var paymentMethod = $('body').hasClass('checkout-index-index') ? 'stripe_payments' : null;
                if (quote.totals()['grand_total'] <= 0 && !quote.isAwSarp2QuoteSubscription()) {
                    paymentMethod = 'free';
                }
                return quote.paymentMethod() ? quote.paymentMethod().method : paymentMethod;
            }),
        };

        return function (target) {
            return target.extend(mixin);
        };
    }
);
