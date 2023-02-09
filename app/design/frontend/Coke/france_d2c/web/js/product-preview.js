define([
    "jquery",
    "validator",
    "personalizer",
    "mage/gallery/gallery"
], function ($, validator, personalizer) {
    "use strict";
    $.widget("mage.coke_product_preview", {

        _create: function () {
            var self = this;
            self.inputs = self.element.find('.phraseInput');
            self.canvas = self.element.find('canvas')[0];
            self.gallery = self.element.find('[data-gallery-role=gallery-placeholder]');
            self._bind();
            self._checkForPrefilledPhrases();
        },
        _bind: function () {
            var self = this;
            self.inputs.on('keypress', validator.validateKeys);
            self.inputs.on('paste', validator.removeSpecialChars);
            self.inputs.on("change keyup paste", validator.debounce(validator.removeWhitespace, 1000));
            self.inputs.on("input", validator.debounce(validator.validateInput, 2000));
            self.inputs.on("blur", validator.validateInput);
            $(document).on('updatePreview', self._updatePreview.bind(this)); 
        },
        _updatePreview: function() {
            var self = this,
                canvas = self.canvas,
                sku = self.options.sku;
            personalizer.clearPreview(canvas);
            if (self.options.canSkus.includes(sku)) {
                personalizer.updateCanPreview(canvas);
            } else {
                personalizer.updateBottlePreview(canvas);
            }
        },
        _checkForPrefilledPhrases: function () {
            var self = this;
            $.each(self.inputs, function( i, el ) {
               if(el.value) $(el).trigger('blur');
            });
        }
    });

    return $.mage.coke_product_preview;
});
