define(['jquery', 'enableAddressHelpers', 'jquery/validate'], function (
	$,
	addressHelpers
) {
	'use strict';

	return function (validator) {
		validator.addRule(
			'validate-address',
			function () {
				var form = document.getElementById('co-shipping-form');
				if (!form) {
					return true;
				}
				return addressHelpers.getAddressValidation(form);
			},
			$.mage.__('Please enter a valid address.')
		);
		return validator;
	};
});
