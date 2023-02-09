define([
    'jquery',
    'mage/translate',
    'underscore'
], function ($, $t, _, ) {
    'use strict';

    return function (original) {
        $.widget('mage.configurable', original, {
            _getOptionLabel: function (option) {
                return option.initialLabel;
            },
        });
        return $.mage.configurable;
    };
});
