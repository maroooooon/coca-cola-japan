
define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data',
], function (
    $,
    ko,
    Component,
    customerData,
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/cart-contents',
        },
        initialize: function(){
            this._super();
            return this;
        },
        /**
         * @return {Array}
         */
        getDetails: function() {
            const cart_items = customerData.get('cart')().items;

            try {
                $.each(cart_items, function(index, value) {
                    value.product_total_price_value = '￥'+(value.product_price_value * value.qty).toLocaleString()

                    if (value.aw_sarp_is_subscription) {
                        let week_items = {}
                        let key_num = 0

                        if (value.product_type == "bundle") {
                            //original bundle
                            if (value.options[1]['label'] == 'SKU' && value.product_name == 'オリジナルバンドル') {
                                week_items = value.options[2]['value'].split('/')
                                key_num = 2
                            }
                            //pre bundle
                            else {
                                week_items = value.options[1]['value'].split('/')
                                key_num = 1
                            }

                            value.options[0]['value'] =  value.options[0]['value'].join(' ')
                        }
                        else {
                            week_items = value.options[0]['value'].split('/')
                        }

                        const week_num = week_items[1].trim() == '週' ? '1週' : week_items[1].trim()
                        value.options[key_num]['value'] = week_num + 'に1回'
                    }
                })
            } catch (e) {}

            return cart_items.reverse();
        }
    });
});