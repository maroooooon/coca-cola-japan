define([
	'Magento_Checkout/js/model/payment-service',
	'Magento_Checkout/js/action/select-payment-method',
], function (paymentService, selectPaymentMethod) {
	'use strict';

	return function (checkoutDataResolver) {
		checkoutDataResolver.resolvePaymentMethod = function () {
			var methods = paymentService.getAvailablePaymentMethods();
			// If there is only one payment method, select that method.
			if (methods.length === 1) {
				selectPaymentMethod(methods[0]);
			}
			// If there are two methods (stripe) select second method.
			if (methods.length === 2) {
				selectPaymentMethod(methods[1]);
			}
		};
		return checkoutDataResolver;
	};
});
