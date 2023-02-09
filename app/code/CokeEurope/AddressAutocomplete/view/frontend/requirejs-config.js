var config = {
	map: {
		'*': {
			addressValidator:
				'CokeEurope_AddressAutocomplete/js/address-validator',
		},
	},
	config: {
		mixins: {
			'mage/validation': {
				'CokeEurope_AddressAutocomplete/js/mixin/account-validation-mixin': true,
			},
			'Magento_Ui/js/lib/validation/validator': {
				'CokeEurope_AddressAutocomplete/js/mixin/checkout-validation-mixin': true,
			}
		},
	},
};
