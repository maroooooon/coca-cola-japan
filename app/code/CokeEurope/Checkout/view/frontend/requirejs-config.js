var config = {
        map: {
            '*': {
                'Magento_CheckoutAgreements/js/model/agreement-validator':'CokeEurope_Checkout/js/model/agreement-validator',
                'Magento_CheckoutAgreements/js/model/agreements-assigner':'CokeEurope_Checkout/js/model/agreements-assigner'
                }
            },
        config: {
            mixins: {
                'Magento_Checkout/js/model/address-converter': {
                    'CokeEurope_Checkout/js/mixin/address-converter-mixin': true,
                }
            },
        },
    };
