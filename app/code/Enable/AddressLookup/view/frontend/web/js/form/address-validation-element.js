define([
	'Magento_Ui/js/form/element/abstract',
	'Magento_Ui/js/lib/validation/validator',
], function (Abstract, validator) {
	'use strict';

	return Abstract.extend({
		validate: function () {
			var self = this,
				value = self.value(),
				result = validator(
					self.validation,
					value,
					self.validationParams
				),
				message = result.message,
				isValid = result.passed;

			if (!isValid) {
				self.source.set('params.invalid', true);
			}

			return {
				valid: isValid,
				target: self,
			};
		},
	});
});
