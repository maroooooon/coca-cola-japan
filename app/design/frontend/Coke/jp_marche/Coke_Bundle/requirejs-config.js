var config = {
    config: {
        mixins: {
            'Magento_Bundle/js/price-bundle': {
                'Coke_Bundle/js/price-bundle-mixin': false
            }
        }
    },

    map: {
        '*': {
            'Magento_Bundle/js/price-bundle':'Coke_Bundle/js/price-bundle',
            customBundleSubscriptionDetails: 'Coke_Bundle/js/product/custom-bundle-subscription-details'
        }
    }
};
