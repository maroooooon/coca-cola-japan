define(['jquery'],
    function ($) {
    'use strict';

    return function () {
        $.validator.addMethod(
            'input-text',
            function (value) {
                return value.length <= 255;
            },
            $.mage.__('Please enter less than 255 characters.')
        );

        $.validator.addMethod(
            'validate-symbol',
            function (value) {
                return new RegExp(/[!@#$%^&*()_+\-={}|[\]\\:";'<>?,.\/]/, 'i').test(value);
            },
            $.mage.__('Passwords must contain a symbol.')
        );

        $.validator.addMethod(
            'validate-customer-password',
            function (v, elm) {
                var validator = this,
                    counter = 0,
                    passwordMinLength = $(elm).data('password-min-length'),
                    passwordMinCharacterSets = $(elm).data('password-min-character-sets'),
                    pass = v.trim(),
                    result = pass.length >= passwordMinLength;

                if (pass === '') {
                    validator.passwordErrorMessage = $.mage.__('This is a required field.');

                    return false;
                }

                if (result === false) {
                    validator.passwordErrorMessage = $.mage.__('Minimum length of this field must be equal or greater than %1 symbols. Leading and trailing spaces will be ignored.').replace('%1', passwordMinLength); //eslint-disable-line max-len

                    return result;
                }

                if (pass.match(/\d+/)) {
                    counter++;
                }

                if (pass.match(/[a-z]+/)) {
                    counter++;
                }

                if (pass.match(/[A-Z]+/)) {
                    counter++;
                }

                if (pass.match(/[^a-zA-Z0-9]+/)) {
                    counter++;
                }

                if (counter < passwordMinCharacterSets) {
                    result = false;
                    validator.passwordErrorMessage = $.mage.__('Minimum of different classes of characters in password is %1. Classes of characters: Lower Case, Upper Case, Digits, Special Characters.').replace('%1', passwordMinCharacterSets); //eslint-disable-line max-len
                }

                return result;
            }, function () {
                return this.passwordErrorMessage;
            }
        );
    }
});
