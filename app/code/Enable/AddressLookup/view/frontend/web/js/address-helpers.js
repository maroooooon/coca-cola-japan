define(['jquery'], function ($) {
	'use strict';

	return {
		getAutocompleteSuggestions: function (value) {
			var endpoint = '/enableaddress/autocomplete/index?data=';
			return new Promise((resolve, reject) => {
				$.ajax({
					type: 'GET',
					url: endpoint + value,
					contentType: 'application/json',
					success: function (data) {
						resolve(data);
					},
					error: function (error) {
						reject(error);
					},
				});
			});
		},
		getAddressValidation: function (form) {
			var self = this,
				endpoint = '/enableaddress/lookup/index?data=',
				address = JSON.stringify(self.convertFormToAddress(form)),
				isValid = false;
			$.ajax({
				type: 'GET',
				url: endpoint + address,
				contentType: 'application/json',
				async: false,
				success: function (response) {
					var event = new CustomEvent('newAddressSuggestions', {
						detail: response.suggestions || [],
					});
					document.dispatchEvent(event);
					isValid = response.isValid;
				},
			});
			return isValid;
		},
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
							address['street[0]'] = $('#street_1').val();
							address['street[1]'] = $('#street_2').val();
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
		fillFormWithAddress: function (address) {
			var self = this,
				form =
					document.getElementById('co-shipping-form') ||
					document.getElementById('form-validate');
			if (form.length) {
				$.each(address, function (key, value) {
					var input = $(form).find('input[name="' + key + '"]');
					if (key === 'street[0]' && !input.length) {
						$('#street_1').val(value).trigger('change');
					}
					if (key === 'street[1]' && !input.length) {
						$('#street_2').val(value).trigger('change');
					}
					if (input) return input.val(value).trigger('change');
				});
			}
		},
	};
});
