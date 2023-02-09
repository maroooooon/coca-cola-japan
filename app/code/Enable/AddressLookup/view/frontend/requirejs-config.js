var config = {
	map: {
		'*': {
			enableAddressHelpers: 'Enable_AddressLookup/js/address-helpers',
		},
	},
	config: {
		mixins: {
			'mage/validation': {
				'Enable_AddressLookup/js/mixin/account-validation-mixin': true,
			},
			'Magento_Ui/js/lib/validation/validator': {
				'Enable_AddressLookup/js/mixin/checkout-validation-mixin': true,
			},
		},
	},
};
