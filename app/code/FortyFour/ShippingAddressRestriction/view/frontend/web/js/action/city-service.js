define([
    'jquery',
    'Magento_Checkout/js/model/full-screen-loader',
    'mage/storage',
    'mage/url'
], function ($, fullScreenLoader, storage, urlBuilder) {
    'use strict';

    var citiesEndpoint = 'rest/V1/shipping-address-restriction/cities';

    return {
        getCitiesUrl: function () {
            return urlBuilder.build(citiesEndpoint);
        },

        /**
         *
         * @returns {*}
         */
        getCities: function () {
            fullScreenLoader.startLoader();

            return storage.get(
                this.getCitiesUrl()
            ).fail(
                function (response) {
                    console.log('error', response);
                }
            ).success(
                function (response) {
                    console.log('success', response);
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

