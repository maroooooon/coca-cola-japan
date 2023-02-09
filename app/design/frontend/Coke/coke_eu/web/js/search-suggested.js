define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    $.widget('mage.searchSuggested', {
        options: {
            input: '#search',
            suggestedContainer: '#search-suggested',
            activeClass: 'active'
        },

        /**
         * Creates widget
         * @private
         */
        _create: function () {
            let self = this;

            self._bindEvents();
        },

        /**
         * @private
         */
        _bindEvents: function () {
            let self = this;

            //element events to show/hide suggestion container
            $(document).on('input focus', self.options.input, function() {
                if($(self.options.input).val() !== ''){
                    $(self.options.suggestedContainer).hide();
                    $(self.options.suggestedContainer).addClass(self.options.activeClass);
                }else{
                    if(!$(self.options.suggestedContainer).hasClass(self.options.activeClass)){
                        //hide to prevent overlapping
                        $('#search_autocomplete').hide();
                        $(self.options.suggestedContainer).delay(200).fadeIn(0);
                    } else{
                        $(self.options.suggestedContainer).hide();
                    }
                }
            });

            //Hide when clicking anywhere except input or flyout
            $(document).on('click', function(event) {
                if(!$(event.target).closest(self.options.input +', ' + self.options.suggestedContainer).length) {
                    $('#search-suggested').hide();
                    $(self.options.suggestedContainer).removeClass(self.options.activeClass);
                }
            });

            // Hide completely if view all is clicked
            $(document).on('click','.search-view-all .view-all', function(event) {
                $('#search-suggested').addClass('hidden');
            });
        }
    });

    return $.mage.searchSuggested;
});
