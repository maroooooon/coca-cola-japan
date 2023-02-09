/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Smile ElasticSuite to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ElasticsuiteCore
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2020 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

/*jshint browser:true jquery:true*/
/*global alert*/

define([
    'ko',
    'jquery',
    'underscore',
    'mage/template',
    'Magento_Catalog/js/price-utils',
    'Magento_Ui/js/lib/knockout/template/loader',
    'mage/url',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'Magento_Search/js/form-mini'
], function (ko, $, _, mageTemplate, priceUtil, templateLoader, url) {
    'use strict';

    return function (widget) {

        $.widget('smileEs.quickSearch', widget, {
            /**
             * Executes when the value of the search input field changes. Executes a GET request
             * to populate a suggestion list based on entered text. Handles click (select), hover,
             * and mouseout events on the populated suggestion list dropdown.
             *
             * Overriden to :
             *  - move rendering of elements in a subfunction.
             *  - manage redirection when clicking a result having an href attribute.
             *
             * @private
             */
            _onPropertyChange: _.debounce(function () {
                var searchField = this.element,
                    clonePosition = {
                        position: 'absolute',
                        // Removed to fix display issues
                        // left: searchField.offset().left,
                        // top: searchField.offset().top + searchField.outerHeight(),
                        width: searchField.outerWidth()
                    },
                    value = this.element.val();

                this.submitBtn.disabled = this._isEmpty(value);

                if (value.trim().length >= parseInt(this.options.minSearchLength, 10)) {
                    this.searchForm.addClass('processing');
                    this.currentRequest = $.ajax({
                        method: "GET",
                        url: this.options.url,
                        data:{q: value},
                        // This function will ensure proper killing of the last Ajax call.
                        // In order to prevent requests of an old request to pop up later and replace results.
                        beforeSend: function() { if (this.currentRequest !== null) { this.currentRequest.abort(); }}.bind(this),
                        success: $.proxy(function (data) {
                            var self = this;
                            var lastElement = false;
                            var content = this._getResultWrapper();
                            var sectionDropdown = this._getSectionHeader();
                            $.each(data, function(index, element) {

                                if (!lastElement || (lastElement && lastElement.type !== element.type)) {
                                    sectionDropdown = this._getSectionHeader(element.type, data);
                                }

                                var elementHtml = this._renderItem(element, index);

                                sectionDropdown.append(elementHtml);

                                if (!lastElement || (lastElement && lastElement.type !== element.type)) {
                                    content.append(sectionDropdown);
                                }

                                lastElement = element;
                            }.bind(this));
                            if(lastElement){
                                let btnText = $.mage.__('View All Results');
                                let searchQuery = url.build('catalogsearch/result/?q='+ $('#search').val());
                                content.append('<dl class="autocomplete-list search-view-all"><dd><a class="action primary btn-white-outline view-all" href="'+searchQuery+'">' + btnText + '</a></a></dd></dl>');
                            }
                            this.responseList.indexList = this.autoComplete.html(content)
                                .css(clonePosition)
                                .show()
                                .find(this.options.responseFieldElements + ':visible');

                            this._resetResponseList(false);
                            this.element.removeAttr('aria-activedescendant');

                            if (this.responseList.indexList.length) {
                                this._updateAriaHasPopup(true);
                            } else {
                                this._updateAriaHasPopup(false);
                            }

                            this.responseList.indexList
                                .on('click vclick', function (e) {
                                    self.responseList.selected = $(this);
                                    if (self.responseList.selected.attr("href")) {
                                        window.location.href = self.responseList.selected.attr("href");
                                        e.stopPropagation();
                                        return false;
                                    }
                                    self.searchForm.trigger('submit');
                                })
                                .on('mouseenter', function (e) {
                                    self.responseList.indexList.removeClass(self.options.selectClass);
                                    $(this).addClass(self.options.selectClass);
                                    self.responseList.selected = $(e.target);
                                    self.element.attr('aria-activedescendant', $(e.target).attr('id'));
                                })
                                .on('mouseleave', function (e) {
                                    $(this).removeClass(self.options.selectClass);
                                    self._resetResponseList(false);
                                })
                                .on('mouseout', function () {
                                    if (!self._getLastElement() && self._getLastElement().hasClass(self.options.selectClass)) {
                                        $(this).removeClass(self.options.selectClass);
                                        self._resetResponseList(false);
                                    }
                                });
                        },this),
                        complete : $.proxy(function () {
                            this.searchForm.removeClass('processing');
                        }, this)
                    });
                } else {
                    this._resetResponseList(true);
                    this.autoComplete.hide();
                    this._updateAriaHasPopup(false);
                    this.element.removeAttr('aria-activedescendant');
                }
            }, 250),

        });

        return $.smileEs.quickSearch;
    }
});
