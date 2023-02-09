define([
    'jquery',
    'Magento_Checkout/js/region-updater'
], function ($) {
    'use strict';

    $.widget('mage.customerAddressRegionUpdater', $.mage.regionUpdater, {

        /**
         * Remove sort
         *
         * Update dropdown list based on the country selected
         *
         * @param {String} country - 2 uppercase letter for country code
         * @private
         */
        _updateRegion: function (country) {
            // Clear validation error messages
            var regionList = $(this.options.regionListId),
                regionInput = $(this.options.regionInputId),
                postcode = $(this.options.postcodeId),
                label = regionList.parent().siblings('label'),
                container = regionList.parents('div.field'),
                regionsEntries,
                regionId,
                regionData;

            this._clearError();
            this._checkRegionRequired(country);

            // Populate state/province dropdown list if available or use input box
            if (this.options.regionJson[country]) {
                this._removeSelectOptions(regionList);
                regionsEntries = _.pairs(this.options.regionJson[country]);
                $.each(regionsEntries, $.proxy(function (key, value) {
                    regionId = value[0];
                    regionData = value[1];
                    this._renderSelectOption(regionList, regionId, regionData);
                }, this));

                if (this.currentRegionOption) {
                    regionList.val(this.currentRegionOption);
                }

                if (this.setOption) {
                    regionList.find('option').filter(function () {
                        return this.text === regionInput.val();
                    }).attr('selected', true);
                }

                if (this.options.isRegionRequired) {
                    regionList.addClass('required-entry').removeAttr('disabled');
                    container.addClass('required').show();
                } else {
                    regionList.removeClass('required-entry validate-select').removeAttr('data-validate');
                    container.removeClass('required');

                    if (!this.options.optionalRegionAllowed) { //eslint-disable-line max-depth
                        regionList.hide();
                        container.hide();
                    } else {
                        regionList.removeAttr('disabled').show();
                    }
                }

                regionList.show();
                regionInput.hide();
                label.attr('for', regionList.attr('id'));
            } else {
                this._removeSelectOptions(regionList);

                if (this.options.isRegionRequired) {
                    regionInput.addClass('required-entry').removeAttr('disabled');
                    container.addClass('required').show();
                } else {
                    if (!this.options.optionalRegionAllowed) { //eslint-disable-line max-depth
                        regionInput.attr('disabled', 'disabled');
                        container.hide();
                    }
                    container.removeClass('required');
                    regionInput.removeClass('required-entry');
                }

                regionList.removeClass('required-entry').prop('disabled', 'disabled').hide();
                regionInput.show();
                label.attr('for', regionInput.attr('id'));
            }

            // If country is in optionalzip list, make postcode input not required
            if (this.options.isZipRequired) {
                $.inArray(country, this.options.countriesWithOptionalZip) >= 0 ?
                    postcode.removeClass('required-entry').closest('.field').removeClass('required') :
                    postcode.addClass('required-entry').closest('.field').addClass('required');
            }

            // Add defaultvalue attribute to state/province select element
            regionList.attr('defaultvalue', this.options.defaultRegion);
        }
    });

    return $.mage.customerAddressRegionUpdater;
});
