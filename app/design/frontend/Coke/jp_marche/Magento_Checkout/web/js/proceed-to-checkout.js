/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Customer/js/model/authentication-popup',
    'Magento_Customer/js/customer-data'
], function ($, authenticationPopup, customerData) {
    'use strict';

    return function (config, element) {
        $(element).click(function (event) {

            var cart = customerData.get('cart'),
                customer = customerData.get('customer');

            event.preventDefault();

            const bundledControlsSku = String(config.bundledControlsSku);
            var cartArray = Array();
            var popup = $('<div class="add-to-cart-modal-popup"/>').html('<h2 class="custom-modal-popup-h2">' + config.addToCartMessage + '</h2>' +
                '<p class="custom-modal-popup-p">' + config.bundledControlMessage + '<br>' + config.pleaseEmptyCart + '</p>'
            )
            .modal({
                buttons: [
                    {
                        text: 'OK',
                        click: function () {
                            this.closeModal();
                        }
                    }
                ]
            });

            for (var i = 0; i < cart().items.length; i++){
                cartArray.push(cart().items[i].product_sku)
            }

            var judgment = cartArray.some(function (value){
                return value === bundledControlsSku;
            });

            var judgmentFalse = cartArray.some(function (value){
                return value !== bundledControlsSku;
            });

            if(judgment && judgmentFalse){
                event.stopImmediatePropagation();
                popup.modal('openModal');
                return true;
            }

            if (!config.newCustomer) {
                if (!customer().firstname && cart().isGuestCheckoutAllowed === false) {
                    authenticationPopup.showModal();

                    return false;
                }
            }

            location.href = config.checkoutUrl;
        });

    };
});
