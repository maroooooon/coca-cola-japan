define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('custombundle.layered_navigation',{
        filters: [],
        bottleSize: '',
        filterDrillDown: '',

        _create: function() {
            this._bindBottleSizeClick();
            this._bindFilterClick();
            this._bindClearFiltersClick();
        },

        /**
         *
         * @returns {string}
         * @private
         */
        _getBottleSize: function () {
            return this.bottleSize;
        },

        /**
         *
         * @param bottleSize
         * @private
         */
        _setBottleSize: function (bottleSize) {
            this.bottleSize = bottleSize;
        },

        /**
         *
         * @returns {[]}
         * @private
         */
        _getFilters: function () {
            return this.filters;
        },

        /**
         *
         * @param filterValue
         * @private
         */
        _setFilters: function (filterValue = null) {
            if (!filterValue) {
                this.filters = [];
            }

            this.filters.push(filterValue);
        },

        /**
         *
         * @private
         */
        _bindBottleSizeClick: function () {
            var self = this;
            $('.custom-bundle-step1 .bottle-size').on('click', function () {
                self._setBottleSize($(this).data('bottle-size'));

            });
        },

        /**
         *
         * @private
         */
        _bindFilterClick: function () {
            var self = this;

            $('.filter-options-item .item').on('click', function () {
                let filterValue = $(this).attr('data-filter-value'),
                    filterCheckbox = $(this).children('.checkbox');

                if (filterCheckbox.is(":checked")) {
                    let filters = self._getFilters();
                    filterCheckbox.prop("checked", false);

                    if (filters.indexOf(filterValue) >= 0) {
                        filters.splice(filters.indexOf(filterValue), 1);
                    }

                } else {
                    self._setFilters(filterValue);
                    filterCheckbox.prop('checked', true);
                }

                self._applyFilters();
            });
        },

        /**
         *
         * @private
         */
        _bindClearFiltersClick: function () {
            var self = this;
            $('.filter-clear, .bottle-size, .reset-selection').on('click', function () {
                self._clearFilters();
            });
        },

        /**
         *
         * @private
         */
        _applyFilters: function () {
            if (this._getFilters().length) {
                $('[data-bottle-size-group="'+ this._getBottleSize() +'"]').hide();

                if (this.options.filterDrillDown) {
                    $('.'+ this._getFilters().join('.') +'[data-bottle-size-group="'+ this._getBottleSize() +'"]').show();
                } else {
                    this._getFilters().forEach((filterValue) => {
                        $('.'+ filterValue +'[data-bottle-size-group="'+ this._getBottleSize() +'"]').show();
                    });
                }

            } else {
                $('[data-bottle-size-group="'+ this._getBottleSize() +'"]').show();
            }
        },

        /**
         *
         * @private
         */
        _clearFilters: function () {
            this.filters = [];
            $('.filter-options-content .checkbox').prop('checked', false);
            this._applyFilters();
        }
    });

    return $.custombundle.layered_navigation;
});
