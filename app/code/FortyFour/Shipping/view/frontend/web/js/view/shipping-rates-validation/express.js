/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        '../../model/shipping-rates-validator',
        '../../model/shipping-rates-validation-rules'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        expressShippingProviderShippingRatesValidator,
        expressShippingProviderShippingRatesValidationRules
    ) {
        "use strict";

        defaultShippingRatesValidator.registerValidator('express', expressShippingProviderShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('express', expressShippingProviderShippingRatesValidationRules);
        return Component;
    }
);
