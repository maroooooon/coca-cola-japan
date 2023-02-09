var config = {
	map: {
		'*': {
			productPreview: 'CokeEurope_PersonalizedProduct/js/product-preview',
			catalogToolbar: 'CokeEurope_PersonalizedProduct/js/catalog-toolbar',
		},
	},
	config: {
		mixins: {
			'Magento_Swatches/js/swatch-renderer': {
				'CokeEurope_PersonalizedProduct/js/mixin/swatch-renderer-mixin': true,
			},
			'mage/validation': {
				'CokeEurope_PersonalizedProduct/js/mixin/validation-mixin': true,
			},
		},
	},
};
