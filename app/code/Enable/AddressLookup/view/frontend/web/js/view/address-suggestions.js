define(['jquery', 'ko', 'enableAddressHelpers', 'uiComponent'], function (
	$,
	ko,
	addressHelpers,
	Component
) {
	'use strict';
	return Component.extend({
		defaults: {
			template: 'Enable_AddressLookup/address/suggestions',
		},
		suggestions: ko.observableArray(),
		initialize: function () {
			var self = this;
			this._super();
			$(document).on(
				'newAddressSuggestions',
				self.addSuggestions.bind(this)
			);
			$(document).on(
				'clearAddressSuggestions',
				self.clearSuggestions.bind(this)
			);
		},
		addSuggestions: function (e) {
			var self = this;
			self.suggestions([]);
			if (e.detail.length) {
				self.suggestions(e.detail);
			}
		},
		clearSuggestions: function (e) {
			var self = this;
			return self.suggestions([]);
		},
		useSuggestion: function (address) {
			var self = this;
			addressHelpers.fillFormWithAddress(address);
			return self.clearSuggestions();
		},
	});
});
