define(['jquery'],
    function($) {
    'use strict';

    return function() {
        $.validator.addMethod(
            'input-text',
            function(value) {
                return value.length <= 255;
            },
            $.mage.__('Please enter less than 255 characters.')
        );

        $.validator.addMethod(
            'validate-symbol',
            function(value) {
                return new RegExp(/[!@#$%^&*()_+\-={}|[\]\\:";'<>?,.\/]/, 'i').test(value)
            },
            $.mage.__('Passwords must contain a symbol.')
        );
    }
});
