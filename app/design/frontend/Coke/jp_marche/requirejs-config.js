var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/form/element/email': {
                'Magento_Checkout/js/view/form/element/email-mixin': true
            },
            'Aheadworks_Sarp2/js/product/subscription-details': {
                'Aheadworks_Sarp2/js/product/subscription-details-mixin': true
            }
        }
    },
    deps: [
        "js/sticky-header"
    ],
};

