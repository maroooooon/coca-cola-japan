define([
    'jquery',
    'mage/url',
    'mage/storage'
], function ($, urlBuilder, storage) {
    'use strict';

    return function (date, redirect) {
        let validateMinAgeEndPoint = 'rest/V1/age-restriction/minimum-age/validate';

        $('body').trigger('processStart');

        return storage.post(
            validateMinAgeEndPoint,
            JSON.stringify(preparePayload(date, redirect))
        ).always(
            function () {
                $('body').trigger('processStop');
            }
        );
    }

    function preparePayload(date, redirect) {
        return {
            "date": date,
            "successfulRedirectUrl": redirect
        }
    }
});
