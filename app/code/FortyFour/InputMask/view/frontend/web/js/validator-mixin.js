define([
    'jquery'
], function ($) {
    'use strict';

    return function (validator) {
        validator.addRule(
            'validate-postcode-complete',
            function (value) {
                var substring = '_';
                return value.indexOf(substring) == -1;
            },
            $.mage.__('This is a required field.')
        );

        validator.addRule(
            'validate-telephone-complete',
            function (value) {
                var substring = '_';
                return value.indexOf(substring) == -1;
            },
            $.mage.__('This is a required field.')
        );

        return validator;
    };
});
