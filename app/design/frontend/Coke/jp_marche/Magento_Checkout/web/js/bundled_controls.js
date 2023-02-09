define([
    'jquery',
    'mage/mage',
    'Magento_Catalog/product/view/validation',
    'Magento_Catalog/js/catalog-add-to-cart',
    "Magento_Ui/js/modal/modal"
], function ($, modal) {
    'use strict';

    return function (config) {
        $("button.action.tocart.primary, a.action.create.primary, .coupon-validate-message a").click(function(e) {
            const bundledControlsSku = String(config.bundledControlsSku);
            var cartArray = Array();
            var dataName = String($(this).closest('form').data('productSku'));
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

            for (var i = 0; i < require('Magento_Customer/js/customer-data').get('cart')().items.length; i++){
                cartArray.push(require('Magento_Customer/js/customer-data').get('cart')().items[i].product_sku)
            }

            var judgment = cartArray.some(function (value){
                return value === bundledControlsSku;
            });

            var judgmentFalse = cartArray.some(function (value){
                return value !== bundledControlsSku;
            });

            if(dataName === "undefined" && judgment && judgmentFalse){
                e.preventDefault();
                popup.modal('openModal');
            } else if(dataName === bundledControlsSku && cartArray.length === 0
                || dataName === "undefined"){
                return true;
            } else if(dataName === bundledControlsSku && judgmentFalse){
                e.preventDefault();
                popup.modal('openModal');
            } else if(dataName !== bundledControlsSku && judgment) {
                e.preventDefault();
                popup.modal('openModal');
            }
        });
    }
});
