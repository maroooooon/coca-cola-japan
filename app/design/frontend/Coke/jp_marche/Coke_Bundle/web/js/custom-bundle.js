define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    $.widget('mage.customBundle', {
        options: {
            optionConfig: null,
            count: 0,
            minimumRequired: 2,
            maxAllowed: 3,
            step2Product: '.product-selection',
            step3Product: '.bundle-options-wrapper .field.choice',
            skuSelectElement: '[data-option-name="SKU"] select',
            qtyRequired: 6,
            currentQty: 0,
            qtyCounterElement: '.custom-bundle-product-count',
            bottleSizeGroup: 0,
            step3SubHeading: '.step-sub-header',
            orderBlock: '#bundleSummary',
            selectionQty: 2,
            qtyIncrement: 2
        },

        /**
         * Creates widget
         * @private
         */
        _create: function () {
            let self = this;

            // self._forceMinimum();
            // setInterval(function(){
            //     self._forceMinimum();
            //     //$(self.options.orderBlock).show();
            //     },
            //     3000);

            $('.product-selection').on('click', function () {
                self._productSelectionTrigger(this);
                self._resetQty();
                self._forceQty(this, self.options.qtyRequired);
                self._forceMinimum();
                self._setQtyCounter(self.options.qtyRequired, self.options.currentQty);
                $(self.options.step3SubHeading).show();
            });

            $('.reset-selection').on('click', function () {
                self._resetProductSelection();
                self._resetQty();
                self._forceMinimum();
                self._setQtyCounter(self.options.qtyRequired, self.options.currentQty);
            });

            $('.bottle-size').on('click', function () {
                self._setBottleSize(this);
                self._resetQty();
                self._resetProductSelection();

                if($('.bottle-size.selected').length && $('.pack-size.selected').length) {
                    self._showStep2(true);
                    self._setSkuOption('.pack-size.selected', '.bottle-size.selected');
                    $('.custom-bundle-step3-nosubscription').hide();
                    $(self.options.orderBlock).show();
                } else {
                    self._showStep2(false);
                    $(self.options.orderBlock).hide();
                    $('.custom-bundle-step3-nosubscription').show();
                }
            });

            $('.pack-size').on('click', function () {
                self._setPackSize(this);
                self._resetQty();

                if($('.bottle-size.selected').length && $('.pack-size.selected').length) {
                    self._showStep2(true);
                    self._setQtyCounter(self.options.qtyRequired, self.options.currentQty);
                    self._setSkuOption('.pack-size.selected', '.bottle-size.selected');
                    $('.custom-bundle-step3-nosubscription').hide();
                    $(self.options.orderBlock).show();
                    self._forceQty(this, self.options.qtyRequired);
                    self._forceMinimum();
                } else {
                    self._showStep2(false);
                    $(self.options.orderBlock).hide();
                    $('.custom-bundle-step3-nosubscription').show();
                }
            });

            $(document).on('input keyup', '.field.choice.active [data-type="checkbox-qty"]', function() {
                self._forceQty(this, self.options.qtyRequired);
                self._forceMinimum();
            });

            $('.qty').on('keydown', function (e) {
                if(!((e.keyCode > 95 && e.keyCode < 106) || (e.keyCode > 47 && e.keyCode < 58) || e.keyCode === 8 || e.keyCode > 36 && e.keyCode < 41)) {
                    return false;
                }
            });

            $('.qty').on('blur', function (e) {
                if($(this).val() === 0  || $(this).val() === '0'){
                    $(this).val(self.options.selectionQty);
                    self._forceQty(this, self.options.qtyRequired);
                    self._forceMinimum();
                    $(this).trigger('change');
                }
            });

            $('.qty-increment, .qty-decrement').on('click', function () {
                self._qtyControl(this, self.options.qtyRequired, self.options.currentQty);
                self._forceQty($(this).parent().find('.qty'), self.options.qtyRequired);
                self._forceMinimum();
                $(this).parent().find('.qty').addClass('clicked').trigger('change');
            });

            $('input[id^="bundle-option"]').change(function(){
                if($('input[id^="bundle-option"]').is(':checked')){
                    $(this).parent().addClass('active').show();
                    $(this).parent().find('.qty').attr('disabled', false).removeClass('qty-disabled');
                }
            });

            // stop form from submitting if enter is pressed on qty fields
            $('#product_addtocart_form').on('keyup keypress', function(e) {
                var keyCode = e.keyCode || e.which;
                if (keyCode === 13) {
                    e.preventDefault();
                    return false;
                }
            });

            //Set values on load for editing from cart/minicart
            //self._setDefaultValuesOnEditLoad();

            //Scroll to top on add to cart
            $('[data-block="minicart"]').on('contentLoading', function (event) {
                $('html, body').animate({scrollTop:0}, 1000);
                $('[data-block="minicart"]').on('contentUpdated', function ()  {
                    //$('html, body').animate({scrollTop:0}, 'slow');
                });
            });

        },

        /**
         * Trigger for product selection
         * @param element
         * @private
         */
        _productSelectionTrigger: function (element) {
            let targetId = $(element).data('option-selection-id');
            let targetElem = $('[data-option-choice="'+ targetId +'"]');

            if(this.options.count < this.options.maxAllowed) {
                if(!$(element).hasClass('active')){
                    this.options.count += 1;
                    $(element).addClass('count'+ this.options.count);
                }

                $(element).addClass('active');
                targetElem.addClass('active').show();
                if(!targetElem.find('input[id^="bundle-option"]').is(':checked')){
                    targetElem.find('input[id^="bundle-option"]').trigger('click');
                } else {
                    targetElem.find('.qty').attr('disabled', false).removeClass('qty-disabled');
                }
            }

            $(this.options.qtyCounterElement).show();
            $('.custom-bundle-messaging').show();
        },

        /**
         * Resets the selected products in Step 2
         * @private
         */
        _resetProductSelection: function () {
            $(this.options.step2Product).removeClass('active').removeClass('count1 count2 count3');
            $(this.options.step3Product+'.active').find('input[id^="bundle-option"]').trigger('click');
            $(this.options.step3Product).removeClass('active').hide();
            $(this.options.qtyCounterElement).hide();
            $('.custom-bundle-messaging').hide();
            $(this.options.step3SubHeading).hide();
            this.options.count = 0;
        },

        /**
         * Disabled ATC until minimum products selected and QTY equals pack size
         * @private
         */
        _forceMinimum: function () {
            if(this.options.count < this.options.minimumRequired){
                $('.action.primary.tocart').prop("disabled",true);
            } else{
                if(this.options.currentQty === this.options.qtyRequired) {
                    $('.action.primary.tocart').prop("disabled",false);
                } else {
                    $('.action.primary.tocart').prop("disabled",true);
                }
            }
        },

        /**
         * Sets bottle size
         * @param element
         * @private
         */
        _setBottleSize: function (element) {
            let size = $(element).data('bottle-size');
            if(!$(element).hasClass('selected')){
                switch (size) {
                    case 1:
                        this.options.bottleSizeGroup = 1;
                        $(this.options.step2Product + '[data-bottle-size-group="1"]' ).show();
                        $(this.options.step2Product + '[data-bottle-size-group="2"]' ).hide();
                        break;
                    case 2:
                        this.options.bottleSizeGroup = 2;
                        $(this.options.step2Product + '[data-bottle-size-group="1"]' ).hide();
                        $(this.options.step2Product + '[data-bottle-size-group="2"]' ).show();
                        break;
                    default:
                        console.log('No option selected.');
                }

                $('.bottle-size').removeClass('selected');
                $(element).addClass('selected');
            }
        },

        /**
         * Sets pack size
         * @param element
         * @private
         */
        _setPackSize: function (element) {
            let size = $(element).data('pack-size');
            if(!$(element).hasClass('selected')){
                $('.pack-size').removeClass('selected');
                $(element).addClass('selected');
                $(this.options.skuSelectElement).trigger('change');
            }

        },

        /**
         * Set the hidden SKU select option
         * @param sizeElement
         * @param bottleElement
         * @private
         */
        _setSkuOption: function(sizeElement, bottleElement){
            let size = $(sizeElement).data('pack-size');
            let bottle = $(bottleElement).data('bottle-size');
            if(size && bottle){
                let check = bottle + '-' +size;
                switch (check) {
                    case '1-6':
                        $(this.options.skuSelectElement + ' option:eq(1)').attr('selected', 'selected');
                        this.options.qtyRequired = 6;
                        break;
                    case '1-12':
                        $(this.options.skuSelectElement + ' option:eq(2)').attr('selected', 'selected');
                        this.options.qtyRequired = 12;
                        break;
                    case '1-24':
                        $(this.options.skuSelectElement + ' option:eq(3)').attr('selected', 'selected');
                        this.options.qtyRequired = 24;
                        break;
                    case '2-6':
                        $(this.options.skuSelectElement + ' option:eq(4)').attr('selected', 'selected');
                        this.options.qtyRequired = 6;
                        break;
                    case '2-12':
                        $(this.options.skuSelectElement + ' option:eq(5)').attr('selected', 'selected');
                        this.options.qtyRequired = 12;
                        break;
                    case '2-24':
                        $(this.options.skuSelectElement + ' option:eq(6)').attr('selected', 'selected');
                        this.options.qtyRequired = 24;
                        break;
                    default:
                        console.log('No option selected. Defaulting to first option.');
                }
            }
        },

        /**
         * Forces QTY by seting max on qty input and not allowing to enter more than pack size
         * @param element
         * @param max
         * @private
         */
        _forceQty: function (element, max) {
            let sum = 0;
            $('.field.choice.active [data-type="checkbox-qty"]').each(function() {
                sum += parseInt($(this).val());
            });

            let thisSum = $(element).val();
            let sumOthers = parseInt(sum) - (thisSum);
            let limitMax = parseInt(max) - (sumOthers);

            $(element).attr("max", limitMax);
            if($(element).val() > limitMax){
                $(element).val(limitMax)
            }

            this.options.currentQty = sum;

            this._setQtyCounter(max, this.options.currentQty);
        },

        /**
         * Resets qty inputs to 1
         * @private
         */
        _resetQty: function () {
            var self = this;
            $('.field.choice.active [data-type="checkbox-qty"]').each(function() {
                $(this).val(self.options.selectionQty).trigger('change');
            });
            this.options.currentQty = 0;
        },

        /**
         * Controls for +/- on product qty
         * @param element
         * @param max
         * @param currentSum
         * @private
         */
        _qtyControl: function (element, max,  currentSum) {
            var self = this;
            var $qtyInput = $(element).parent().find('input.qty');
            var current = parseInt($qtyInput.val()) || self.options.selectionQty;

            if($(element).data('trigger') === 'decrement'){
                if (current > 1){
                    $qtyInput.val(parseInt(current - self.options.qtyIncrement) || self.options.selectionQty);
                }
            }else{
                var maxQty =  $qtyInput.attr("max") || 999;

                if(currentSum !==  max) {
                    if (current < maxQty) {
                        $qtyInput.val(parseInt(current + self.options.qtyIncrement) || self.options.selectionQty);
                    }
                }
            }
        },

        /**
         * Set text message for current QTY
         * @param limit
         * @private
         */
        _setQtyCounter: function (limit) {
            let limitElement = $(this.options.qtyCounterElement).find('.limit');
            let currentElement = $(this.options.qtyCounterElement).find('.current');

            let current = 0;
            $('.field.choice.active [data-type="checkbox-qty"]').each(function() {
                current += parseInt($(this).val());
            });

            limitElement.html(limit);
            currentElement.html(current);
        },

        /**
         * Shows Step 2
         * @param state
         * @private
         */
        _showStep2: function (state) {
            if(state === true){
                $('.custom-bundle-step2-noproduct').hide();
                $('.custom-bundle-step2').show();
            } else {
                $('.custom-bundle-step2').hide();
                $('.custom-bundle-step2-noproduct').show();
            }
        },

        /**
         * Used for editing from cart/mincart.
         * @private
         */
        _setDefaultValuesOnEditLoad: function () {
            if($('body').hasClass('checkout-cart-configure')){
                let optionConfig = this.options.optionConfig;

                let skuValue = $(this.options.skuSelectElement)[0].selectedIndex;
                let bottleSize = $('[data-checked="checked"]').data('bottle-size-group');

                switch (skuValue) {
                    case 1:
                        $('[data-pack-size="6"]').click();
                        break;
                    case 2:
                        $('[data-pack-size="12"]').click();
                        break;
                    case 3:
                        $('[data-pack-size="24"]').click();
                        break;
                    default:
                }

                if(bottleSize){
                    if(bottleSize === 1){
                        $('[data-bottle-size="1"]').click();
                    } else if(bottleSize === 2){
                        $('[data-bottle-size="2"]').click();
                    }
                }

                $('[data-checked="checked"]').each(function () {
                    this.click();
                });

                let sum = 0;
                $('.field.choice.active [data-type="checkbox-qty"]').each(function() {
                    sum += parseInt($(this).val());
                });
                this.options.currentQty = sum;

                this._forceMinimum();
            }
        }
    });

    return $.mage.customBundle;
});
