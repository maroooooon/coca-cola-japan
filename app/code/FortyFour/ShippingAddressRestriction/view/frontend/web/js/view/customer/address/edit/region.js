define([
    'ko',
    'jquery',
    'uiComponent',
    'underscore',
    'uiRegistry',
    'FortyFour_ShippingAddressRestriction/js/action/region-service',
], function (ko, $, Component, _, Registry, regionService) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'FortyFour_ShippingAddressRestriction/view/customer/address/edit/region'
        },

        selectedRegion: ko.observable(''),
        title: '',
        cityRegistryPath: '',
        allowedRegions: ko.observableArray([]),
        isBillingAddress: ko.observable(false),

        initialize: function (config) {
            var self = this;

            this._super();
            this.bindBillingAddressToggle();

            this.selectedRegion(config.region);
            this.bindUpdateRegions();
        },

        makeRequestToGetRegionsByCity: function (cityName) {
            var self = this;

            regionService.getRegionsByCity(cityName)
                .then(function (response) {
                    console.log('makeRequestToGetRegionsByCity', response);
                    self.allowedRegions(response);
                })

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

        bindUpdateRegions: function() {
            let cityComponent = Registry.get(this.cityRegistryPath),
                self = this;

            cityComponent.selectedCity.subscribe(function (city) {
                console.log('city', city);
                if (!self.isBillingAddress()) {
                    if (city) {
                        console.log('city', city);
                        self.makeRequestToGetRegionsByCity(city);
                    }
                }
            });
        },

        afterSelectRegion: function (event, target) {
            if (this.selectedRegion() === target.target.value) {
                this.selectedRegion('');
            }
            this.selectedRegion(target.target.value);
        }
    });
});
