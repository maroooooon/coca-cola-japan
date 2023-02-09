define([
    'jquery',
    'underscore',
    'mage/template',
    'awSarp2SubscriptionOptionStorage',
    'Aheadworks_Sarp2/js/product/config/provider',
], function (
    $,
    _,
    mageTemplate,
    sarpStorage,
    sarpConfigProvider
) {
    'use strict';

    return function (awSarp2SubscriptionDetails){
        $.widget('mage.awSarp2SubscriptionDetails', awSarp2SubscriptionDetails, {

            options: {
                performUpdateAfterInit: true
            },

            _create: function () {
                this._super();
                setTimeout(
                    function()
                    {
                        $("[data-default='selected']").trigger('click');
                    }, 250);

            },
        });

        return $.mage.awSarp2SubscriptionDetails;
    };

});
