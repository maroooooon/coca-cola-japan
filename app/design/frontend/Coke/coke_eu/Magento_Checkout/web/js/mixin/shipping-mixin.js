define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/form',
    'ko',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/address-converter',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/create-shipping-address',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Magento_Checkout/js/model/shipping-address/form-popup-state',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Ui/js/modal/modal',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Checkout/js/checkout-data',
    'uiRegistry',
    'mage/translate',
    'Magento_Checkout/js/model/shipping-rate-service'
], function ($,
             _,
             Component,
             ko,
             customer,
             addressList,
             addressConverter,
             quote,
             createShippingAddress,
             selectShippingAddress,
             shippingRatesValidator,
             formPopUpState,
             shippingService,
             selectShippingMethodAction,
             rateRegistry,
             setShippingInformationAction,
             stepNavigator,
             modal,
             checkoutDataResolver,
             checkoutData,
             registry,
             $t) {
    'use strict';

    return function (target) {
        var checkoutConfig = window.checkoutConfig,
            agreementsConfig = checkoutConfig ? checkoutConfig.checkoutAgreements : {},
            agreementsInputPath = '#checkout-step-shipping_method div.checkout-agreements input';

        return target.extend({
            setShippingInformation: function () {
                this.validateAutoCompleteShippingAddress();
                this._super();
            },

            /**
             * @return {Boolean}
             */
            validateShippingInformation: function () {
                var shippingAddress,
                    addressData,
                    loginFormSelector = 'form[data-role=email-with-possible-login]',
                    emailValidationResult = customer.isLoggedIn(),
                    field,
                    option = _.isObject(this.countryOptions) && this.countryOptions[quote.shippingAddress().countryId],
                    messageContainer = registry.get('checkout.errors').messageContainer;

                if (!quote.shippingMethod()) {
                    this.errorValidationMessage(
                        $t('The shipping method is missing. Select the shipping method and try again.')
                    );

                    return false;
                }

                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }

                if (this.isFormInline) {
                    this.source.set('params.invalid', false);
                    this.triggerShippingDataValidateEvent();

                    if (!quote.shippingMethod()['method_code']) {
                        this.errorValidationMessage(
                            $t('The shipping method is missing. Select the shipping method and try again.')
                        );
                    }

                    if (emailValidationResult &&
                        this.source.get('params.invalid') ||
                        !quote.shippingMethod()['method_code'] ||
                        !quote.shippingMethod()['carrier_code'] ||
                        !this.validateTC()
                    ) {
                        this.focusInvalid();
                        this.validateTC();

                        return false;
                    }

                    shippingAddress = quote.shippingAddress();
                    addressData = addressConverter.formAddressDataToQuoteAddress(
                        this.source.get('shippingAddress')
                    );

                    //Copy form data to quote shipping address object
                    for (field in addressData) {
                        if (addressData.hasOwnProperty(field) &&  //eslint-disable-line max-depth
                            shippingAddress.hasOwnProperty(field) &&
                            typeof addressData[field] != 'function' &&
                            _.isEqual(shippingAddress[field], addressData[field])
                        ) {
                            shippingAddress[field] = addressData[field];
                        } else if (typeof addressData[field] != 'function' &&
                            !_.isEqual(shippingAddress[field], addressData[field])) {
                            shippingAddress = addressData;
                            break;
                        }
                    }

                    if (customer.isLoggedIn()) {
                        shippingAddress['save_in_address_book'] = 1;
                    }
                    selectShippingAddress(shippingAddress);
                } else if (customer.isLoggedIn() &&
                    option &&
                    option['is_region_required'] &&
                    !quote.shippingAddress().region
                ) {
                    messageContainer.addErrorMessage({
                        message: $t('Please specify a regionId in shipping address.')
                    });

                    return false;
                }

                if (!emailValidationResult) {
                    $(loginFormSelector + ' input[name=username]').focus();

                    return false;
                }

                if(!this.validateTC()){
                    return false;
                }

                return true;
            },

            validateAutoCompleteShippingAddress: function() {
                let elem = $('[name="custom_attributes[address_finder]"]');
                if(elem.val() === ''){
                    elem.parent().find('button').trigger('click');
                }
                return true;
            },

            validateTC: function (hideError) {
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
                    }
                });

                return isValid;
            }
        });
    }
});
