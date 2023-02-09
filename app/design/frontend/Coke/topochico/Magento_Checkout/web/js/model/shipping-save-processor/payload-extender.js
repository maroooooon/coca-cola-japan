define([], function () {
    'use strict';

    return function (payload) {
        payload.addressInformation['extension_attributes'] = {};

        if(window.checkoutConfig.newsletter_subscribe) {
            payload.addressInformation['extension_attributes']['newsletter_subscribe'] = window.checkoutConfig.email;
        }

        return payload;
    };
});
