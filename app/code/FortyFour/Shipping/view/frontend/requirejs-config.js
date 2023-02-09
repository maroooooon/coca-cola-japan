var config = {
    map: {
        '*': {
            'Magento_Checkout/template/shipping-address/shipping-method-item':
                'FortyFour_Shipping/template/shipping-address/shipping-method-item',
            'Magento_Checkout/template/cart/shipping-rates':
                'FortyFour_Shipping/template/cart/shipping-rates'
        }
    },
    config: {
        mixins: {
            "Magento_Checkout/js/view/shipping": {
                "FortyFour_Shipping/js/view/shipping-mixin": true
            }
        }
    }
};
