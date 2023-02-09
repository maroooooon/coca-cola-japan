define(["jquery"], function ($) { 
    "use strict";
    $.widget("mage.coke_char_counter", {
        _create: function () {
            this.element.find('input').on('input change keyup paste', this.setCharCount.bind(this));
            this.element.find('input').trigger('input');
        },
        setCharCount: function (event) {
            return this.element.find('.count').text(event.target.value.length || 0);
        }
    });
    return $.mage.coke_char_counter;
});
