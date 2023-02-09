define(['jquery', 'mage/translate'], function ($, $t) {
    'use strict';

        let selectedBottleLabelSwatch = '.personalized_bottle_label .swatch-option.selected';

        return {
            debounce: function(fn, ms = 0) {
                let timeout;
                return function(...args) {
                  clearTimeout(timeout);
                  timeout = setTimeout(() => fn.apply(this, args), ms);
                };
            },
            clearPreview: function(canvas) {
                var context = canvas.getContext("2d");
                return context.clearRect(0, 0, canvas.width, canvas.height);
            },
            updateBottlePreview: function(canvas, label_pos_offset, label_max_width) {
                const self = this;
                const context = canvas.getContext("2d");
                const whitelist_inputs = $('.whitelist-input');
                const required_inputs = $('.whitelist-input.required');
                const validated_inputs = $('.whitelist-input.valid');
                const invalidated_inputs = $('.whitelist-input.required.invalid');
                const isJapanCharRegex = /[\u3000-\u303f\u3040-\u309f\u30a0-\u30ff\uff00-\uff9f\u4e00-\u9faf\u3400-\u4dbf]/;

                const japanFont = "900 13.5pt hiragino_kaku_gothic, youregular, sans-serif";
                const englishFont = "400 16.5pt youregular, sans-serif";

                context.fillStyle = "#FFF";
                context.textAlign = "center";
                context.textBaseline = "middle";
                context.font = japanFont;

                /* Clear Canvas on invalid  */
                if(invalidated_inputs.length === 0 && required_inputs.length <= validated_inputs.length && validated_inputs.length > 0 || invalidated_inputs.length === 0 && required_inputs.length === 0 && validated_inputs.length > 0) {
                    $('#product-addtocart-button').attr('disabled', false);
                } else {
                    $('#product-addtocart-button').attr('disabled', true);
                    return context.clearRect(0, 0, canvas.width, canvas.height);
                }

                if (invalidated_inputs.length === 0 && required_inputs.length <= validated_inputs.length) {
                    let h = 0;
                    // line count = filled in inputs
                    let lineCount = whitelist_inputs.filter(function () {
                        return $(this).val().length > 0;
                    }).length;

                    label_pos_offset = self._adjustLabelPosition(label_pos_offset, lineCount);

                    whitelist_inputs.each(function (key) {
                        if (this.value && this.classList.contains('valid')) {
                            var isJapan = this.value.match(isJapanCharRegex);
                            context.font = isJapan ? japanFont : englishFont;

                            if (lineCount === 1) {
                                // If there is only one line, vertically center it
                                context.fillText($(this).val(), 350, parseInt(label_pos_offset) + 15, parseInt(label_max_width));
                            } else {
                                if ($(this).val().length >= 8 && key > 0) {
                                    context.fillText($(this).val(), 350, parseInt(label_pos_offset) + h + 5, parseInt(label_max_width));
                                } else {
                                    context.fillText($(this).val(), 350, parseInt(label_pos_offset) + h + 9.25, parseInt(label_max_width));
                                }

                                h = h + 22;
                            }
                        }
                    });
                }
            },
            _adjustLabelPosition: function(label_pos_offset, lineCount = 1) {
                let label_name = $(selectedBottleLabelSwatch).attr('aria-label');
                if (label_name === 'Classic') {
                    if (lineCount === 2) {
                        label_pos_offset -= 17;
                    } else {
                        label_pos_offset -= 7;
                    }
                }

                return label_pos_offset;
            },
        };
    });
