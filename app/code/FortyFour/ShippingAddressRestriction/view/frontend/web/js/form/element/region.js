define([
    'jquery',
    'ko',
    'underscore',
    'Magento_Ui/js/form/element/abstract',
    'FortyFour_ShippingAddressRestriction/js/action/region-service',
], function ($, ko, _, Element, regionService) {
    'use strict';

    return Element.extend({

        allowedRegions: ko.observableArray([]),

        initialize: function () {
            var self = this;

            this._super();
        },

        makeRequestToGetRegionsByCity: function (cityName) {
            return regionService.getRegionsByCity(cityName);
        },

        bindCityChange: function () {
            var self = this;

            setTimeout(function() {
                $('#co-shipping-form select[name="city"]').on('change', function () {
                    self.makeRequestToGetRegionsByCity($(this).val())
                        .then(function (response) {
                            self.allowedRegions(response);
                        })
                });

                self.populationInitialRegions();
            }, 500);
        },

        populationInitialRegions: function () {
            var self = this,
                $city = $('#co-shipping-form select[name="city"]');

            if (!$city.val()) {
                return;
            }

            this.makeRequestToGetRegionsByCity($city.val())
                .then(function (response) {
                    self.allowedRegions(response);
                })
        }
    });
});
