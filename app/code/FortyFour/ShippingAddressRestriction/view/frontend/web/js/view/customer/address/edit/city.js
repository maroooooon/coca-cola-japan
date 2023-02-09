define([
    'ko',
    'jquery',
    'uiComponent',
    'underscore',
    'FortyFour_ShippingAddressRestriction/js/action/city-service',
], function (ko, $, Component, _, cityService) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'FortyFour_ShippingAddressRestriction/view/customer/address/edit/city'
        },

        title: '',
        selectedCity: ko.observable(''),
        allowedCities: ko.observableArray([]),
        isBillingAddress: ko.observable(false),

        initialize: function (config) {
            var self = this;
            this._super();

            this.selectedCity(config.city);

            this.bindBillingAddressToggle();

            if (!this.isBillingAddress()) {
                this.makeRequestToGetCities()
                    .then(function (response) {
                        self.allowedCities(response);
                    })
            }
        },

        makeRequestToGetCities: function () {
            return cityService.getCities();
        },

        bindBillingAddressToggle: function () {
            var self = this;

            $('#billing-address-toggle').on('change', function () {
                if ($(this).is(':checked')) {
                    self.isBillingAddress(true);
                } else {
                    self.isBillingAddress(false);
                }
            })
        },

        afterSelectCity: function (event, target) {
            if (this.selectedCity() === target.target.value) {
                this.selectedCity('');
            }
            this.selectedCity(target.target.value);
        }
    });
});
