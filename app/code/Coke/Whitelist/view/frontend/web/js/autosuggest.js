define(['jquery', 'mage/storage', 'jquery/ui', 'mage/translate'], function ($, storage) {
    'use strict';

    $.widget("mage.autosuggest", {
        _create: function () {
            var self = this;
            self.input = document.getElementById(self.options.input_id);
            self._bind();
        },
        _bind: function() {
            var self = this;
            $(self.input).autocomplete({
                source: function (request, response) {
                    // this.liveRegion.text($.mage.__('Searching...'));
                    self.getSource(request.term, response);
                },
                position: {
                    my: 'left top+1'
                },
                delay: 100,
                minLength: 2,
                messages: {
                    // noResults: $.mage.__('Your search returned no results.'),
                    noResults: '',
                    results: function(amount) {
                        return '';
                    }
                },
                select: function(e, ui) {
                    e.preventDefault();
                    $(e.target).val(ui.item.value).trigger('change');
                }
            });
        },
        getSource: function(term, response) {
            var params = {
                type_id: this.options.type_id,
                term: term
            };

            var esc = encodeURIComponent;
            var query = Object.keys(params)
                .map(function(k) {return esc(k) + '=' + esc(params[k]);})
                .join('&');

            return storage.get(
                '/white_list/ajax/search?' + query,
            ).fail(
                function (r) {
                    console.log('error', r);
                }
            ).success(
                function (r) {
                    response(r.results.map(function (w) { return w.value; }));
                }
            );
        }
    });
    return $.mage.autosuggest;
});
