define(['jquery'], function ($) {
    'use strict';

    return function (SwatchRenderer) {
        $.widget('mage.SwatchRenderer', $['mage']['SwatchRenderer'], {
            _init: function () {
                this._super();
            },
            /**
             * Render controls
             *
             * @private
             */
            _RenderControls: function () {
                var $widget = this,
                    container = this.element,
                    classes = this.options.classes,
                    chooseText = this.options.jsonConfig.chooseText,
                    showTooltip = this.options.showTooltip;

                $widget.optionsMap = {};

                $.each(this.options.jsonConfig.attributes, function () {
                    var item = this,
                        controlLabelId = 'option-label-' + item.code + '-' + item.id,
                        options = $widget._RenderSwatchOptions(item, controlLabelId),
                        select = $widget._RenderSwatchSelect(item, chooseText),
                        input = $widget._RenderFormInput(item),
                        listLabel = '',
                        label = '';

                    // Show only swatch controls
                    if ($widget.options.onlySwatches && !$widget.options.jsonSwatchConfig.hasOwnProperty(item.id)) {
                        return;
                    }

                    if ($widget.options.enableControlLabel) {
                        label +=
                            '<span id="' + controlLabelId + '" class="' + classes.attributeLabelClass + '">' +
                            $('<i></i>').text(item.label).html() +
                            '</span>' +
                            '<span class="' + classes.attributeSelectedOptionLabelClass + '"></span>';
                    }

                    if ($widget.inProductList) {
                        $widget.productForm.append(input);
                        input = '';
                        listLabel = 'aria-label="' + $('<i></i>').text(item.label).html() + '"';
                    } else {
                        listLabel = 'aria-labelledby="' + controlLabelId + '"';
                    }

                    // Create new control
                    container.append(
                        '<div class="' + classes.attributeClass + ' ' + item.code + '" ' +
                             'data-attribute-code="' + item.code + '" ' +
                             'data-attribute-id="' + item.id + '">' +
                            label +
                            '<div aria-activedescendant="" ' +
                                 'tabindex="0" ' +
                                 'aria-invalid="false" ' +
                                 'aria-required="true" ' +
                                 'role="listbox" ' + listLabel +
                                 'class="' + classes.attributeOptionsWrapper + ' clearfix">' +
                                options + select +
                            '</div>' + input +
                        '</div>'
                    );

                    $widget.optionsMap[item.id] = {};

                    // Aggregate options array to hash (key => value)
                    $.each(item.options, function () {
                        if (this.products.length > 0) {
                            $widget.optionsMap[item.id][this.id] = {
                                price: parseInt(
                                    $widget.options.jsonConfig.optionPrices[this.products[0]].finalPrice.amount,
                                    10
                                ),
                                products: this.products
                            };
                        }
                    });
                });

                if (showTooltip === 1) {
                    // Connect Tooltip
                    container
                        .find('[option-type="1"], [option-type="2"], [option-type="0"], [option-type="3"]')
                        .SwatchRendererTooltip();
                }

                // Hide all elements below more button
                $('.' + classes.moreButton).nextAll().hide();

                // Handle events like click or change
                $widget._EventListener();

                // Rewind options
                $widget._Rewind(container);

                //Emulate click on all swatches from Request
                $widget._EmulateSelected($.parseQuery());
                $widget._EmulateSelected($widget._getSelectedAttributes());

                /* Select first swatch */
                var swatchLength = $('.swatch-option.image').length;
                if (swatchLength == 1) {
                    if ($('.swatch-attribute').hasClass("brand_swatch")) {
                        $('.swatch-option').first().trigger('click');
                    }
                }
            },
            /**
         * Update [gallery-placeholder] or [product-image-photo]
         * @param {Array} images
         * @param {jQuery} context
         * @param {Boolean} isInProductView
         */
        updateBaseImage: function (images, context, isInProductView) {
            var justAnImage = images[0],
                initialImages = this.options.mediaGalleryInitial,
                imagesToUpdate,
                gallery = context.find(this.options.mediaGallerySelector).data('gallery'),
                isInitial;

            if (isInProductView) {
                imagesToUpdate = images.length ? this._setImageType($.extend(true, [], images)) : [];
                isInitial = _.isEqual(imagesToUpdate, initialImages);

                if (this.options.gallerySwitchStrategy === 'prepend' && !isInitial) {
                    imagesToUpdate = imagesToUpdate.concat(initialImages);
                }

                imagesToUpdate = this._setImageIndex(imagesToUpdate);

                if (!_.isUndefined(gallery)) {
                    gallery.updateData(imagesToUpdate);
                } else {
                    context.find(this.options.mediaGallerySelector).on('gallery:loaded', function (loadedGallery) {
                        loadedGallery = context.find(this.options.mediaGallerySelector).data('gallery');
                        loadedGallery.updateData(imagesToUpdate);
                    }.bind(this));
                }
                /*
                if (gallery) {
                    gallery.first();
                }
                */
            } else if (justAnImage && justAnImage.img) {
                context.find('.product-image-photo').attr('src', justAnImage.img);
            }
            },
            /*
            updateBaseImage: function (images, context, isInProductView) {
                var firstImage = images[0],
                    productImage = context.find('#productImage'),
                    productImageSrc = productImage.attr('src'),
                    imagesToUpdate;

                if (isInProductView) {
                    imagesToUpdate = images.length ? this._setImageType($.extend(true, [], images)) : [];
                    console.log('[product img src] ', productImageSrc);
                    console.log('[product img upd] ', imagesToUpdate[0].img);
                    console.log('[images] ', imagesToUpdate);
                    if(imagesToUpdate[0].img !== productImage.attr('src')){
                        context.find('#productImage').attr('src', imagesToUpdate[0].img);
                        console.log('[changing image src]');
                    }
                }
            },
            */
        });
        return $['mage']['SwatchRenderer'];
    };
});
