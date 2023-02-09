define(['jquery', 'enableAddressHelpers'], function ($, addressHelpers) {
	'use strict';

	return function (validation) {
		$.validator.addMethod(
			'validate-address',
			function () {
				var form = document.getElementById('form-validate');
				if (!form) {
					return true;
				}
				return addressHelpers.getAddressValidation(form);
			},
			$.mage.__('Please enter a valid address.')
		);

		return validation;
	};
});
