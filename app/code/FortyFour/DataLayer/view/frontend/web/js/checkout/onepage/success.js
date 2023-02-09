define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('mage.datalayer_checkout_success', {
        _init: function () {
            var self = this;

            if (self.options.purchase_event) {
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push(JSON.parse(self.options.purchase_event));
            }
        }
    });

    return $.mage.datalayer_checkout_success;
});
