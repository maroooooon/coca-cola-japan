define(['jquery', 'addressValidator', 'jquery/validate'], function (
	$,
	addressValidator
) {
	'use strict';

	return function (validator) {
		validator.addRule(
			'validate-address',
			function () {
				var config = window.autocompleteConfig,
					form = document.getElementById('co-shipping-form');

				if (!config.validate_address || !form) {
					return true;
				}

				return addressValidator.validateAddress(form);
			},
			$.mage.__('Please enter a valid address.')
		);

		$.validator.addMethod(
			'validate-postcode',
			function (value) {
				var config = window.autocompleteConfig,
					isValid = false;

				// Skip if validate postcode is not enabled in system config
				if (!config || !config.validate_postcode) {
					return true;
				}

				// Check each pattern for a match
				$.each(config.postcode_pattern, function (i, pattern) {
					if (new RegExp(pattern).test(value)) {
						isValid = true;
					}
				});

				return isValid;
			},
			$.mage.__('Please enter a valid postal code.')
		);

		return validator;
	};
});
