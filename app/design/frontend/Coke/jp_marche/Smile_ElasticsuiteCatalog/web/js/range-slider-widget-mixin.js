define([
    'jquery',
    'mage/template'
], function ($, mageTemplate) {
    'use strict';

    return function (widget) {
        $.widget('smileEs.rangeSlider', widget, {
            /**
             * Show clear price when price filter is active
             * @private
             */
            _create : function () {
                this._super();

                if (window.location.href.match(/price=(-?[0-9]+)/)) {
                    this.element.find('.filter-actions').show();
                    this.element.find('.filter-clear').attr('href', this.getClearPriceUrl());
                }
            },

            /**
             * Get current URL without price param
             * @returns {void|*|string}
             */
            getClearPriceUrl: function(){
                var range = {
                    from : this.from * (1 / this.rate),
                    to   : this.to * (1 / this.rate)
                },
                    url = mageTemplate(this.options.urlTemplate)(range);

                return url.replace(
                    new RegExp('(&?\\??price=(-?[0-9]+--?[0-9]+)$)|(price=(-?[0-9]+--?[0-9]+)&?)')
                    , ''
                );
            },

            _refreshDisplay: function() {
                this.count = this._getItemCount();

                if (this.element.find('[data-role=from-label]')) {
                    this.element.find('[data-role=from-label]').html(this._formatLabel(this.from));
                }

                if (this.element.find('[data-role=to-label]')) {
                    this.element.find('[data-role=to-label]').html(this._formatLabel(this.to - this.options.maxLabelOffset));
                }

                if (this.element.find('[data-role=message-box]')) {
                    var messageTemplate = this.options.messageTemplates[this.count > 0 ? (this.count > 1 ? 'displayCount' : 'displayOne' ) : 'displayEmpty'];
                    var messageTemplate = $.mage.__(messageTemplate);
                    var message = mageTemplate(messageTemplate)(this);
                    this.element.find('[data-role=message-box]').html(message);

                    if (this.count > 0) {
                        this.element.find('[data-role=message-box]').removeClass('empty');
                    } else {
                        this.element.find('[data-role=message-box]').addClass('empty');
                    }

                }
            },
        });

        return $.smileEs.rangeSlider;
    };
});
