define(["jquery", "ko", "enableAddressHelpers", "uiComponent"], function (
    $,
    ko,
    addressHelpers,
    Component
) {
    "use strict";
    return Component.extend({
        defaults: {
            template: "Enable_AddressLookup/address/autocomplete",
        },
        isLoading: ko.observable(false),
        isVisible: ko.observable(false),
        autocompleteItems: ko.observableArray([]),
        initialize: function () {
            var self = this;
            this._super();
            // Hide autocomplete results on outside click
            $(document).mouseup(function (e) {
                var container = $(".enable-address-autocomplete");
                if (
                    !container.is(e.target) &&
                    container.has(e.target).length === 0
                ) {
                    return self.isVisible(false);
                }
            });
        },
        handleInput: function (input, e) {
            var self = this,
                value = e.currentTarget.value || e.target.value;
            // Don't do anything if there is no value or the value is too small
            if (!value || value.length < 3) {
                return;
            }
            // Clear existing autocomplete items
            if (self.autocompleteItems().length) {
                self.autocompleteItems([]);
            }
            self.getAutocompleteResults(value);
        },
        handleFocus: function () {
            var self = this;
            return self.isVisible(true);
        },
        useAutocomplete: function (address) {
            var self = this;
            addressHelpers.fillFormWithAddress(address);
            self.autocompleteItems([]);
            return $('input[name="address_autocomplete"]').val("");
        },
        getAutocompleteResults: _.debounce(function (value) {
            var self = this,
                suggestions = addressHelpers.getAutocompleteSuggestions(value);
            self.isVisible(true);
            self.isLoading(true);
            return suggestions.then(function (results) {
                if (Array.isArray(results)) {
                    self.autocompleteItems(results);
                } else {
                    self.autocompleteItems([]);
                }
                self.isLoading(false);
            });
        }, 500),
    });
});
