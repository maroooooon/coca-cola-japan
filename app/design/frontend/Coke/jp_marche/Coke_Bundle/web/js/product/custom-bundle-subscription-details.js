define([
    'underscore',
    'jquery',
    'Magento_Catalog/js/price-utils',
    'Aheadworks_Sarp2/js/product/config/provider',
    'awSarp2SubscriptionDetails'
], function (_, $, priceUtils, sarpConfigProvider) {
    'use strict';

    $.widget('mage.awSarp2SubscriptionDetailsMixin', $.mage.awSarp2SubscriptionDetails, {

        selectors: {
            addToCartForm: '#product_addtocart_form',
            configuredPrice: '.price-configured_price .price-configured_price .price',
            subscriptionDetailsPrice: '.subscription-details .subscription-details-list [data-price-type="finalPrice"]',
            subscriptionOption: '.aw-sarp2-product-subscription-options .aw-sarp2-subscription__options-list .option'
        },
        subscriptionId: '',

        _create: function () {
            this._super();
            this._bindSubscriptionOptionChange();
        },

        onUpdatePriceBoxes: function () {
            this._super();
            this._afterOnUpdatePriceBoxes();
        },

        _afterOnUpdatePriceBoxes: function () {
            let priceBox = $(this.options.priceBoxSelector, this.selectors.addToCartForm),
                subscriptionId = this._getSubscriptionId(),
                percent = sarpConfigProvider.getOptionPlanRegularPercent(subscriptionId),
                price = $('.price', priceBox),
                originalPrice = price.text().replace('¥', '').replace('￥','').replace(',', ''),
                calculatedPrice = this._calculatePrice(originalPrice, percent);

            this._replacePrice(calculatedPrice);
        },

        /**
         *
         * @param price
         * @param percent
         * @returns {number|*}
         * @private
         */
        _calculatePrice: function (price, percent) {
            if (!percent) {
                return price;
            }

            let discountPercent = percent / 100;
            return price * discountPercent;
        },

        _replacePrice: function (price) {
            if (price) {
                $(this.selectors.configuredPrice).text(priceUtils.formatPrice(price));
                $(this.selectors.subscriptionDetailsPrice).text(priceUtils.formatPrice(price));
            }
        },

        _bindSubscriptionOptionChange: function () {
            var self = this;
            $(this.selectors.subscriptionOption).on('change', function () {
                self._setSubscriptionId($(this).val());
            })
        },

        _setSubscriptionId: function (subscriptionId) {
            this.subscriptionId = subscriptionId;
        },

        _getSubscriptionId: function () {
            return this.subscriptionId ? this.subscriptionId : 0;
        }
    });

    return $.mage.awSarp2SubscriptionDetailsMixin;
});
