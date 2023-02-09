define([
    'jquery',
    'mage/translate',
    'uiComponent',
    'ko',
    'FortyFour_AgeRestriction/js/action/validate-min-age'
], function ($, $t, Component, ko, validateMinAge) {
    'use strict';

    return Component.extend({
        defaults: {
            //template: 'FortyFour_AgeRestriction/verify-age'
        },

        birthday: ko.observable(''),
        isMinAgeNotMet: ko.observable(''),
        redirectUrlText: '',
        redirectUrl: '',
        successfulRedirectUrl: '',
        useCalendar: false,

        initialize: function (config) {
            var self = this;
            this._super();

            this.setConfigData(config);
        },

        setConfigData: function (config) {
            this.isMinAgeNotMet(config.is_min_age_not_met);
        },

        setBirthday: function (event, target) {
            this.birthday(target.target.value)
        },

        validateMinimumAge: function () {
            var self = this;

            validateMinAge(self.birthday(), self.successfulRedirectUrl)
                .then(function(response) {
                    // window.location.reload();
                    window.location.replace(response);
                })
        },

        /**
         *
         * @returns {string}
         */
        getRedirectString: function () {
            return $t('Visit %1')
                .replace('%1', this.redirectUrlText)
        },

        /**
         *
         */
        actionConfirm: function () {
            var self = this;
            validateMinAge('01/01/1979', self.successfulRedirectUrl)
                .then(function(response) {
                    // window.location.reload();
                    window.location.replace(response);
                })
        },

        /**
         *
         */
        actionDeny: function () {
            var self = this;
            validateMinAge(self.getToday(), self.successfulRedirectUrl)
                .then(function(response) {
                    // window.location.reload();
                    window.location.replace(response);
                })
        },

        /**
         *
         * @returns {string}
         */
        getToday: function () {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();

            return dd + '/' + mm + '/' + yyyy;
        }
    });
});
