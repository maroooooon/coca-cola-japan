define([
	'jquery',
	'Magento_Ui/js/lib/view/utils/dom-observer',
	'uiComponent',
	'googlePlacesApi',
	'jquery/validate',
], function ($, domObserver, Component) {
	'use strict';
	return Component.extend({
		initialize: function () {
			this._super();
			var self = this;
			self.autocompleteInputs = '#street_1, input[name="street[0]"]';
			self.config = window.autocompleteConfig;
			domObserver.get(self.autocompleteInputs, function (input) {
				self.initAutocomplete(input);
			});

			// Change postcode to uppercase
			domObserver.get('input[name="postcode"]', function (input) {
				$(input).keyup(function () {
					this.value = this.value.toLocaleUpperCase();
				});
			});

			$(document).on(
				'useAddressSuggestion',
				self.useSuggestion.bind(this)
			);
		},
		initAutocomplete: function (input) {
			var self = this,
				country = self.config.country;

			self.form = $(input).closest('form');
			self.autocomplete = new google.maps.places.Autocomplete(input, {
				componentRestrictions: { country },
				fields: ['address_components', 'formatted_address'],
				types: ['address'],
			});
			self.autocomplete.addListener(
				'place_changed',
				self.handlePlaceChanged.bind(this)
			);
		},
		handlePlaceChanged: function () {
			var self = this,
				place = self.autocomplete.getPlace(),
				address = self.convertPlaceToAddress(place);

			return self.fillInputs(address);
		},
		convertPlaceToAddress: function (place) {
			var address = {},
				types_to_convert = {
					country: 'country',
					street_number: 'street[0]',
					route: 'street[0]',
					locality: 'city',
					postal_town: 'city',
					postal_code: 'postcode',
					postal_code_prefix: 'postcode',
					administrative_area_level_1: 'region',
				};

			place.address_components.map(function (component) {
				var part = null,
					value = component.long_name || component.short_name;
				component.types.forEach((type) => {
					if (type in types_to_convert) {
						part = types_to_convert[type];
						if (address[part] && address[part] !== value) {
							return (address[part] += ' ' + value);
						} else {
							return (address[part] = value);
						}
					}
				});
			});

			return address;
		},
		fillInputs: function (address) {
			var self = this;
			$.each(address, function (key, value) {
				if (key === 'postcode') {
					value = value.toLocaleUpperCase();
				}
				if (key.includes('street') && $('#street_1').length) {
					if (key === 'street[0]') return $('#street_1').val(value);
					if (key === 'street[1]') return $('#street_2').val(value);
				}
				var input = $(self.form).find('input[name="' + key + '"]');
				if (input) return input.val(value).trigger('change');
			});
		},
		useSuggestion: function (event) {
			var self = this;
			self.fillInputs(event.detail);
			if ($('#co-shipping-form').length) {
				return $('#co-shipping-form').valid();
			}
			return $.validator.validateSingleElement('input[name^=street]');
		},
	});
});
