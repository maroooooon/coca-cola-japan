define(['jquery'], function ($) {
	'use strict';

	return function (validator) {
		validator.addRule(
			'validate-zip-postal-code',
			function (value) {
				if (
					!window.checkoutConfig['validations'][
						'zip_postal_code_patterns'
					]
				) {
					return true;
				}

				let matchesPattern = false;
				$.each(
					window.checkoutConfig['validations'][
						'zip_postal_code_patterns'
					][window.checkoutConfig['defaultCountryId']],
					function (index, pattern) {
						if (new RegExp(pattern).test(value)) {
							matchesPattern = true;
						}
					}
				);
				return matchesPattern;
			},
			$.mage.__('Please enter a valid postal code.')
		);

		validator.addRule(
			'validate-phone-number',
			function (value) {
				if (!window.checkoutConfig['validations']['phone_patterns']) {
					return true;
				}

				let matchesPattern = false;
				$.each(
					window.checkoutConfig['validations']['phone_patterns'][
						window.checkoutConfig['defaultCountryId']
					],
					function (index, pattern) {
						if (new RegExp(pattern).test(value)) {
							matchesPattern = true;
						}
					}
				);
				return matchesPattern;
			},
			$.mage.__('Please enter a valid phone number.')
		);

		return validator;
	};
});
