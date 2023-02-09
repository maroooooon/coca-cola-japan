/*
 * @copyright Copyright © 2022 Bounteous. All rights reserved.
 * @author tanya.lamontagne
 */

define([
    'jquery',
    'mage/validation',
    'Magento_Checkout/js/model/step-navigator'
], function ($, validation, stepNavigator) {
    'use strict';

    var checkoutConfig = window.checkoutConfig,
        agreementsConfig = checkoutConfig ? checkoutConfig.checkoutAgreements : {},
        agreementsInputPath = '#checkout-step-shipping_method div.checkout-agreements input';

    return {
        /**
         * Validate checkout agreements
         *
         * @returns {Boolean}
         */
        validate: function (hideError) {
            var isValid = true;

            if (!agreementsConfig.isEnabled || $(agreementsInputPath).length === 0) {
                return true;
            }

            $(agreementsInputPath).each(function (index, element) {
                if (!$.validator.validateSingleElement(element, {
                    errorElement: 'div',
                    hideError: hideError || false
                })) {
                    isValid = false;

                    stepNavigator.navigateTo('shipping');
                }
            });

            return isValid;
        }
    };
});
