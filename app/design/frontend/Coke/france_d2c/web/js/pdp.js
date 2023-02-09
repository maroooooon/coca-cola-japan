define(["jquery"], function ($) {
    "use strict";

    $.widget("mage.coke_france_pdp_scripts", {
        selectors: {
            addLine: "#addLine",
            addToFrom: "#addToFrom",
            lineTwo: "#line-2",
            quantity: "#qty",
            increaseQty: "#qtyUp",
            decreaseQty: "#qtyDown",
            showPreview: "#showPreview",
        },
        _create: function () {
            var self = this;
            self._bind();
        },
        _bind: function () {
            var self = this;
            $(self.selectors.addLine).on("click", self._showAnotherLine.bind(this));
            $(self.selectors.addToFrom).on("click", self._showToFrom.bind(this));
            $(self.selectors.increaseQty).on("click", self._increaseQty.bind(this));
            $(self.selectors.decreaseQty).on("click", self._decreaseQty.bind(this));
            $(self.selectors.quantity).on("change", self._handleQtyChange.bind(this));
        },
        _increaseQty: function (event) {
            var self = this,
            currentValue =  Number($(self.selectors.quantity).val());
            $(self.selectors.quantity).val(currentValue + 1).change();
        },
        _decreaseQty: function (event) {
            var self = this,
            currentValue =  Number($(self.selectors.quantity).val());
            $(self.selectors.quantity).val(currentValue - 1).change();
        },
        _handleQtyChange: function (event) {
            var self = this,
            currentValue =  Number(event.target.value);
            if(currentValue <= 1) {
                $(self.selectors.decreaseQty).prop('disabled', true);
            } else {
                $(self.selectors.decreaseQty).prop('disabled', false);
            }
        },
        _showAnotherLine: function (event) {
            var self = this,
            line2 = $(".phrase.line-1");
            if (line2.length) {
                $(self.selectors.addLine).prop('disabled', true);
                $(line2).show();
            }
        },
        _showToFrom: function (event) {
            var self = this,
            to = $(".phrase.line-2"),
            from = $(".phrase.line-3");
            if (to.length && from.length) {
                $(self.selectors.addToFrom).prop('disabled', true);
                $(to).show(),
                $(from).show();
            }
        },
    });

    return $.mage.coke_france_pdp_scripts;
});
