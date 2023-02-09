define([
    "jquery",
    "validator",
    "personalizer",
    "mage/gallery/gallery"
], function ($, validator, personalizer) {
    "use strict";
    $.widget("mage.coke_product_preview", {
        selectors: {
            bottleLabelSwatch: '.personalized_bottle_label .swatch-option'
        },

        _create: function () {
            var self = this;
            self.inputs = self.element.find('.whitelist-input');
            self.canvas = self.element.find('canvas')[0];
            self.gallery = self.element.find('[data-gallery-role=gallery-placeholder]');
            self.previewButton = self.element.find('.preview');
            self._bind();
            self._checkForPrefilledPhrases();
        },
        _bind: function () {
            var self = this;
            self.inputs.on('paste', validator.removeSpecialChars);
            self.inputs.on("change keyup paste", validator.debounce(validator.removeWhitespace, 500));
            self.inputs.on("input", validator.debounce(validator.validateInput, 500));
            self.inputs.on("blur", validator.validateInput);
            self.previewButton.on("click", self._checkForPrefilledPhrases.bind(this));
            $(document).on("click",self.selectors.bottleLabelSwatch, () => { self._updatePreview() });
            $(document).on('updatePreview', self._updatePreview.bind(this));
        },
        _updatePreview: function() {
            var self = this,
                canvas = self.canvas,
                $pendingOverlay = $('#pending-overlay'),
                sku = self.options.sku,
                label_pos_offset = self.options.label_pos_offset,
                label_max_width = self.options.label_max_width ? self.options.label_max_width : 220;
            personalizer.clearPreview(canvas);
            personalizer.updateBottlePreview(canvas, label_pos_offset, label_max_width);

            if (($('.whitelist-pending').length > 0) && self._validateInputsNotEmpty(self.inputs)) {
                $pendingOverlay.addClass('active');
            } else {
                $pendingOverlay.removeClass('active');
            }

        },
        _checkForPrefilledPhrases: function () {
            var self = this;
            $.each(self.inputs, function( i, el ) {
                el.value = el.value.trimEnd();
               if(el.value) $(el).trigger('blur');
            });
        },
        _validateInputsNotEmpty: function (inputs) {
            let isValid = false;
            $.each(inputs, function() {
                if ($(this).val().length) {
                    isValid = true;
                }
            });
            return isValid;
        }
    });

    return $.mage.coke_product_preview;
});
