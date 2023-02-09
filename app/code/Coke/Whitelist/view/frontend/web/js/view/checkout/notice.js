define([
    'ko',
    'jquery',
    'uiComponent'
], function (ko, $, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Coke_Whitelist/checkout/notice'
        },

        whitelistStatusPending: ko.observable(false),

        initialize: function () {
            this._super();
            this.setObservables();
        },

        setObservables: function() {
            if (window.checkoutConfig.whitelist && window.checkoutConfig.whitelist.whitelist_status_pending) {
                this.whitelistStatusPending(window.checkoutConfig.whitelist.whitelist_status_pending);
            }
        }
    });
});
