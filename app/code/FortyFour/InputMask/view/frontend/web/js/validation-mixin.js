define(['jquery'], function($) {
    'use strict';

    return function() {

        $.validator.addMethod(
            'validate-postcode-complete',
            function (value) {
                var substring = '_';
                return value.indexOf(substring) == -1;
            },
            $.mage.__('This is a required field.')
        );
    }
});
