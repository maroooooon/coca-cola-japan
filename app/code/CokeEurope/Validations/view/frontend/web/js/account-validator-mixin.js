define(['jquery'], function ($) {
	'use strict';

	return function (validation) {
		$.validator.addMethod(
			'validate-zip-postal-code',
			function (value) {
				var isValid = false,
					form = document.getElementById('form-validate');
				if (!form) {
					return true;
				}
				var POSTCODE_PATTERNS = {
					BE: ['^\\d{4}$'],
					FI: ['^\\d{5}$'],
					FR: ['^\\d{5}$'],
					DE: ['^\\d{2}$', '^\\d{4}$', '^\\d{5}$'],
					IE: ['^[0-9a-zA-Z]{3} [0-9a-zA-Z]{4}$', '^[0-9a-zA-Z]{7}$'],
					NL: ['^\\d{4}\\s{0,1}[A-Za-z]{2}$', '^[0-9a-zA-Z]{6}$'],
					GB: [
						'^(([A-Z]{1,2}[0-9][A-Z0-9]?|ASCN|STHL|TDCU|BBND|[BFS]IQQ|PCRN|TKCA) ?[0-9][A-Z]{2}|BFPO ?[0-9]{1,4}|(KY[0-9]|MSR|VG|AI)[ -]?[0-9]{4}|[A-Z]{2} ?[0-9]{2}|GE ?CX|GIR ?0A{2}|SAN ?TA1)$',
						'^[A-Z]{1,2}[0-9][A-Z0-9]? ?[0-9][A-Z]{2}$',
					],
				};
				var country = $('#country').val();
				$.each(POSTCODE_PATTERNS[country], function (i, pattern) {
					if (new RegExp(pattern).test(value)) {
						isValid = true;
					}
				});
				return isValid;
			},
			$.mage.__('Please enter a valid postal code.')
		);

		return validation;
	};
});
