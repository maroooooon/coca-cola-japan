define(['jquery'], function ($) {
	'use strict';
	$.widget('mage.coke_eu_datalayer_product', {
		/**
		 * @private
		 */
		_create: function () {
			var self = this;
			if (self.options.productDetail) {
				window.dataLayer.push(self.options.productDetail);
			}
		},
	});
	return $.mage.coke_eu_datalayer_product;
});
