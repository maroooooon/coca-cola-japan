define([
    'jquery',
    'uiComponent',
    'Magento_Reward/js/action/set-use-reward-points'
], function ($, Component, setUseRewardPointsAction) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Coke_Sarp2/reward/points/confirmation'
        },

        initialize: function () {
            this._super();
        },

        useRewardPoints: function () {
            setUseRewardPointsAction();
            this.closeModal();
        },

        closeModal: function () {
            $(this._getModalSelector()).modal('closeModal');
        },

        _getModalSelector: function () {
            return '#reward-points-confirmation-container';
        }
    });
});
