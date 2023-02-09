define([
    'jquery',
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.addToWishlist', widget, {
            options: {
                bundleInfo: 'div.control [name^=bundle_option]',
                configurableInfo: '.super-attribute-select',
                groupedInfo: '#super-product-table input',
                downloadableInfo: '#downloadable-links-list input',
                customOptionsInfo: '.product-custom-option',
                ppCustomOptionsInfo: '.personalized-product-options',
                qtyInfo: '#qty',
                actionElement: '.towishlist',
                productListWrapper: '.product-item-info',
                productPageWrapper: '.product-info-main',
                personalizedCustomOptions: '#personalized_product_options input'
            },
            _bind: function () {
                var options = this.options,
                    dataUpdateFunc = '_updateWishlistData',
                    validateProductQty = '_validateWishlistQty',
                    changeCustomOption = 'change ' + options.customOptionsInfo,
                    changePpCustomOption = 'change ' + options.ppCustomOptionsInfo,
                    changeQty = 'change ' + options.qtyInfo,
                    updateWishlist = 'click ' + options.actionElement,
                    events = {},
                    key;

                if ('productType' in options) {
                    if (typeof options.productType === 'string') {
                        options.productType = [options.productType];
                    }
                } else {
                    options.productType = [];
                }

                events[changeCustomOption] = dataUpdateFunc;
                events[changePpCustomOption] = dataUpdateFunc;
                events[changeQty] = dataUpdateFunc;
                events[updateWishlist] = validateProductQty;

                for (key in options.productType) {
                    if (options.productType.hasOwnProperty(key) && options.productType[key] + 'Info' in options) {
                        events['change ' + options[options.productType[key] + 'Info']] = dataUpdateFunc;
                    }
                }
                this._on(events);
            },
            _updateAddToWishlistButton: function (dataToAdd, event) {
                var self = this,
                    buttons = this._getAddToWishlistButton(event);

                buttons.each(function (index, element) {
                    var params = $(element).data('post');

                    if (!params) {
                        params = {
                            data: {},
                        };
                    }

                    params.data = $.extend({}, params.data, dataToAdd, {
                        qty: $(self.options.qtyInfo).val(),
                    });

                    var customizations = $('#personalized_product_options input');
                    customizations.each(function (ind, customOption) {
                        if (customOption.name.includes('options')) {
                            params.data[customOption.name] = customOption.value;
                        }
                    });

                    $(element).data('post', params);
                });
            },
            _getAddToWishlistButton: function (event) {
                return $(this.options.actionElement);
            },
        });
        return $.mage.addToWishlist;
    };
});