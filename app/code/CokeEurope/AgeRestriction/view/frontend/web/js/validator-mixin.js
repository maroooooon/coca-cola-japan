define(['jquery', 'moment'], function ($, moment) {
	'use strict';

	return function (validator) {
		var minimumAge = window.checkoutConfig.minimumAge;
		validator.addRule(
			'validate-age',
			function (value) {
				if (!value) {
					return false;
				}
				var age = moment().diff(moment(value, 'DD/MM/YYYY'), 'years');
				return age >= minimumAge;
			},
			$.mage.__(
				'You must be at least ' + minimumAge + ' years old to order.'
			)
		);

		return validator;
	};
});
