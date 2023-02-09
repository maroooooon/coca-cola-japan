define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';
    return function (originalWidget) {
        $.widget('mage.SwatchRenderer',
            originalWidget,
            {
                _RenderControls: function () {
                    this._super();
                    let swatchLength = $('.swatch-attribute').length; // if brand swatch attribute
                    if (swatchLength > 0) {
                        $('.swatch-attribute .swatch-option:first-of-type').trigger('click');
                    }
                }
            }
        )
        return $.mage.SwatchRenderer;
    };
});
