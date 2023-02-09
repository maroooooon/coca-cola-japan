define(['jquery'], function ($) {
	'use strict';
	$.widget('mage.coke_eu_datalayer_purchase', {
		/**
		 * @private
		 */
		_create: function () {
			var self = this;
			console.log(
				'datalayer purchase create: ',
				self.options.purchaseEvent
			);
			if (self.options.purchaseEvent) {
				window.dataLayer.push(self.options.purchaseEvent);
			}
		},
	});
	return $.mage.coke_eu_datalayer_purchase;
});
