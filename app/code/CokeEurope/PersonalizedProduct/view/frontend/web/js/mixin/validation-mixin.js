define(['jquery'], function ($) {
	'use strict';
	return function () {
		$.validator.addMethod(
			'validate-character-limit',
			function () {
				var count = 0,
					limit = localStorage.getItem('character-limit'),
					inputs = $('input[data-enable-moderation]');

				$.each(inputs, function () {
					count += $(this).val().length;
				});

				return count <= parseInt(limit);
			},
			function () {
				var message = $.mage.__(
						'Your label exceeds the limit of characters.'
					),
					localMessage = localStorage.getItem('character-limit-msg');
				return localMessage ? localMessage : message;
			}
		);

		$.validator.addMethod(
			'validate-label-regex',
			function (value, element) {
				var regex = $(element).data('regex');

				if (!value || !regex) {
					return true;
				}

				return new RegExp(regex).test(value);
			},
			$.mage.__('Your label contains invalid characters')
		);
	};
});
