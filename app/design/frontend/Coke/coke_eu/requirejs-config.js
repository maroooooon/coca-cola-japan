var config = {
	paths: {
		slick: 'js/slick.min',
		quantityControl: 'js/qty-control',
	},
	shim: {
		slick: {
			deps: ['jquery'],
		},
	},
	deps: ['js/element/banner-carousel', 'js/element/product-carousel'],
	map: {
		'*': {
			menu: 'js/menu-custom',
		},
	},
};
