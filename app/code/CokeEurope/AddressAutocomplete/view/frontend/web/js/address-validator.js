define(['jquery'], function ($) {
	'use strict';

	return {
		convertFormToAddress: function (form) {
			var data = new FormData(form),
				address = {},
				country = document.querySelector('select[name="country_id"]'),
				parts = [
					'street[]',
					'street[0]',
					'street[1]',
					'country',
					'region',
					'city',
					'postcode',
				];
			for (var key of data.keys()) {
				if (parts.includes(key)) {
					if (data.get(key) !== '') {
						if (key === 'street[]') {
							address['street[0]'] = data.get(key);
						} else {
							address[key] = data.get(key);
						}
					}
				}
			}
			if (country) {
				address.country = country.textContent;
			}
			return address;
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
		getGeocodeUrl: function (address) {
			var self = this,
				config = window.autocompleteConfig,
				url = config.api_url,
				key = config.api_key;

			if (address['street[0]']) {
				url += address['street[0]'].replace(/\s/g, '+');
			}
			if (address['street[1]']) {
				url += '+' + address['street[1]'].replace(/\s/g, '+');
			}
			if (address['city']) {
				url += '+' + address['city'].replace(/\s/g, '+');
			}
			if (address['country']) {
				url += '+' + address['country'].replace(/\s/g, '+');
			}
			url += '&key=' + key;

			return url;
		},
		isMatch: function (address, suggested, key) {
			if (!address[key] || !suggested[key]) return false;
			if (key === 'postcode') {
				return address[key].lastIndexOf(suggested[key], 0) === 0;
			}
			return address[key].toLowerCase() === suggested[key].toLowerCase();
		},
		addressMatches: function (address, suggested) {
			var self = this,
				keys = ['street[0]', 'city', 'region', 'country', 'postcode'],
				matches = true;

			keys.forEach((key) => {
				if (!self.isMatch(address, suggested, key)) {
					matches = false;
				}
			});

			return matches;
		},
		validateAddress: function (form) {
			var self = this,
				isValid = false,
				suggestions = [],
				address = self.convertFormToAddress(form),
				geocodeUrl = self.getGeocodeUrl(address);

			// Skip validation if address does not have city, or country
			if (!address.city || !address.country) {
				return true;
			}

			$.ajax({
				type: 'GET',
				url: geocodeUrl,
				async: false,
				success: function (response) {
					if (response.results.length) {
						response.results.forEach(function (place, i) {
							var suggestion = self.convertPlaceToAddress(place);
                            // Ignore any addresses with no streets
                            if (typeof suggestion['street[0]'] === 'undefined') {
                                return;
                            }

							if (self.addressMatches(address, suggestion)) {
								isValid = true;
							}
                            suggestion.formatted = place.formatted_address;
                            suggestions.push(suggestion);
						});
						if (!isValid) {
							var event = new CustomEvent('addressSuggestions', {
								detail: suggestions,
							});
							return document.dispatchEvent(event);
						} else {
							var event = new CustomEvent('clearSuggestions');
							return document.dispatchEvent(event);
						}
					}
				},
			});

			return isValid;
		},
	};
});
