var config = {
	map: {
		'*': {
			'Magento_Checkout/js/model/shipping-save-processor/default':
				'Coke_Delivery/js/model/shipping-save-processor/default',
		},
	},
	config: {
		mixins: {
			'Magento_Checkout/js/view/shipping': {
				'Magento_Checkout/js/mixin/shipping-mixin': true,
			},
			'Magento_Checkout/js/model/checkout-data-resolver': {
				'Magento_Checkout/js/mixin/checkout-data-resolver-mixin': true,
			},
		},
	},
};
