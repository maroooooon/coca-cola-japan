define([
    'jquery',
    'Magento_Checkout/js/model/full-screen-loader',
    'mage/storage',
    'mage/url'
], function ($, fullScreenLoader, storage, urlBuilder) {
    'use strict';

    var regionsByCityEndpoint = 'rest/V1/shipping-address-restriction/regions';

    return {
        getRegionsByCityUrl: function () {
            return urlBuilder.build(regionsByCityEndpoint);
        },

        /**
         *
         * @param cityName
         * @returns {*}
         */
        getRegionsByCity: function (cityName) {
            var payload = {
                'city': cityName
            };

            fullScreenLoader.startLoader();

            return storage.post(
                this.getRegionsByCityUrl(),
                JSON.stringify(payload)
            ).fail(
                function (response) {
                    console.log('error', response);
                }
            ).success(
                function (response) {
                    return response;
                }
            ).always(
                function () {
                    fullScreenLoader.stopLoader();
                }
            );
        }
    };
});

