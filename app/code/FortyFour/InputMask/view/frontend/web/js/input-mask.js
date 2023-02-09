define([
    "jquery",
    "inputmask"
], function ($) {
    "use strict";

    $.widget("mage.region_input_mask", {

        _create: function () {
            this.applyInputMask();
        },

        applyInputMask: function () {
            $(this.options.selector).inputmask({regex: String.raw`${this.options.input_mask}`});
        }
    });
    return $.mage.region_input_mask;
});
