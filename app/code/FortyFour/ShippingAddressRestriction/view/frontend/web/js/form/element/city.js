define([
    'jquery',
    'ko',
    'underscore',
    'Magento_Ui/js/form/element/abstract',
], function ($, ko, _, Element) {
    'use strict';

    return Element.extend({

        allowedCities: ko.observableArray([]),

        initialize: function () {
            this._super();
            this.setObservables();
        },

        setObservables: function() {
            if (window.checkoutConfig.shipping_address_restriction) {
                this.allowedCities(window.checkoutConfig.shipping_address_restriction.city);
            }
        }
    });
});
