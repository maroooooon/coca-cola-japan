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
        standardShippingProviderShippingRatesValidator,
        standardShippingProviderShippingRatesValidationRules
    ) {
        "use strict";

        defaultShippingRatesValidator.registerValidator('standard', standardShippingProviderShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('standard', standardShippingProviderShippingRatesValidationRules);
        return Component;
    }
);
