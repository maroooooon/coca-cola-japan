define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';
    return function (originalWidget) {
        $.widget('mage.productValidate',
            originalWidget,
            {
                _create: function () {
                    this._super();
                    if($('body').hasClass('product-personalized-bottle')) {
                        $(this.options.addToCartButtonSelector).attr('disabled', true);
                    }
                }
            }
        )
        return $.mage.productValidate;
    };
});
