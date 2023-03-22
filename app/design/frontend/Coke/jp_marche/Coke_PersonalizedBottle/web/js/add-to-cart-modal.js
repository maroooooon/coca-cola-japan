define([
    'jquery',
    "Magento_Ui/js/modal/modal",
    'mage/translate',
    'Magento_Catalog/js/catalog-add-to-cart'
], function ($, modal, $t) {
    $(function() {
        const body = $('body');
        if( body.data('store-code') === "jp_marche_ja" && body.hasClass("product-personalized-bottle")){
            var LoadSku = String($('#product_addtocart_form').data('productSku'));
            var cookieSku = String($.cookie('productSku'));
            if (LoadSku === cookieSku) {
                var product_name = $('.page-title-wrapper .base').text();
                var popup = $('<div class="add-to-cart-modal-popup"/>').html( product_name +
                    $t('has been added'))
                    .modal({
                        modalClass: 'add-to-cart-popup',
                        buttons: [
                            {
                                text: $t('Go to cart'),
                                click: function () {
                                    window.location = '/checkout/cart/'
                                }
                            }
                        ]
                    });
                popup.modal('openModal');
                $.cookieStorage.set('productSku', null);
            }
        }
    })

    return function (config) {
        $("button.action.tocart.primary").click(function() {
            const body = $('body');
            if( body.data('store-code') === "jp_marche_ja" && body.hasClass("product-personalized-bottle") ){
                const bundledControlsSku = String(config.bundledControlsSku);
                var cartArray = Array();

                for (var i = 0; i < require('Magento_Customer/js/customer-data').get('cart')().items.length; i++){
                    cartArray.push(require('Magento_Customer/js/customer-data').get('cart')().items[i].product_sku)
                }

                var judgment = cartArray.some(function (value){
                    return value === bundledControlsSku;
                });

                if(!judgment) {
                    var dataName = String($(this).closest('form').data('productSku'));
                    $.cookieStorage.set('productSku', dataName);
                }
            }
        });
    }
});
