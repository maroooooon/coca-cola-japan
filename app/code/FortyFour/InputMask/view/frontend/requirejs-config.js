var config = {
    "map": {
        "*": {
            "inputmask" : "FortyFour_InputMask/js/jquery.inputmask.min"
        }
    },
    shim: {
        'inputmask': {
            deps: ['jquery']
        },
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping-information': {
                'FortyFour_InputMask/js/view/shipping-information-mixin': true
            },
            'Magento_Ui/js/lib/validation/validator': {
                'FortyFour_InputMask/js/validator-mixin': true
            },
            'mage/validation': {
                'FortyFour_InputMask/js/validation-mixin': true
            }
        }
    }
};
