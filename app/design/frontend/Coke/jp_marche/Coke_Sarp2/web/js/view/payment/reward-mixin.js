define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function($, modal) {
    'use strict';

    var mixin = {


        useRewardPoints: function () {
            if (window.checkoutConfig.isAwSarp2QuoteMixed || window.checkoutConfig.isAwSarp2QuoteSubscription) {
                this._showModal();
            } else {
                this._super();
            }
        },

        _getModalSelector: function () {
            return '#reward-points-confirmation-container';
        },

        /**
         *
         * @private
         */
        _showModal: function () {
            modal(
                {
                    type: 'popup',
                    modalClass: 'reward-points-confirmation-popup popup',
                    responsive: false,
                    buttons: []
                },

                $(this._getModalSelector())
            );
            $(this._getModalSelector()).modal('openModal');
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
