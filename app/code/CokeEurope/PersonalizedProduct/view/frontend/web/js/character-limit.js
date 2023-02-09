/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define(["jquery"], function ($) {
    "use strict";

    $.widget("coke_europe.characterLimit", {
        /**
         * Initializes character limit
         *
         * @private
         */
        _create: function () {
            var self = this;
            self.input = self.element.find("input, textarea");
            self.count = self.element.find(".count");
            self.container = self.element.find(".character-limit");
            self.input.on("change keyup paste", self.handleChange.bind(this));
        },

        /**
         * Update character count on input change
         */
        handleChange: function () {
            var self = this,
                length = self.input.val().length;
            if (length > self.options.limit) self.container.addClass("invalid");
            else self.container.removeClass("invalid");
            return self.count.text(length || 0);
        },
    });

    return $.coke_europe.characterLimit;
});
