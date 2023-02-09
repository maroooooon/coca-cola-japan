define(['jquery', 'ko', 'uiComponent'], function ($, ko, Component) {
	'use strict';
	return Component.extend({
		defaults: {
			template: 'CokeEurope_AddressAutocomplete/address-suggestions',
		},
		isVisible: ko.observable(false),
		suggestions: ko.observableArray(),
		initialize: function () {
			var self = this;
			this._super();
			$(document).on(
				'addressSuggestions',
				self.handleSuggestions.bind(this)
			);
			$(document).on(
				'clearSuggestions',
				self.clearSuggestions.bind(this)
			);
		},
		handleSuggestions: function (data) {
			var self = this;
			self.suggestions.removeAll();
			if (data.detail.length) {
				data.detail.forEach(function (suggestion, i) {
					self.suggestions.push(suggestion);
				});
				return self.isVisible(true);
			}
			return self.isVisible(false);
		},
		clearSuggestions: function (data) {
			var self = this;
			self.suggestions.removeAll();
			return self.isVisible(false);
		},
		useSuggestion: function (address) {
			var self = this;
			self.suggestions.removeAll();
			self.isVisible(false);
			const event = new CustomEvent('useAddressSuggestion', {
				detail: address,
			});
			return document.dispatchEvent(event);
		},
	});
});
