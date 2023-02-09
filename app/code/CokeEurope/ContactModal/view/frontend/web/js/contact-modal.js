/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define(['jquery', 'Magento_Ui/js/modal/modal'], function ($, modal) {
	'use strict';

	$.widget('coke_europe.contactModal', {
		options: {
			trigger: '.contact-modal-trigger',
			modal: {
				type: 'popup',
				responsive: false,
				buttons: false,
			},
		},
		/**
		 * Initializes Contact Modal
		 *
		 * @private
		 */
		_create: function () {
			var self = this,
				urlParams = new URL(document.location).searchParams;

			// Create modal with options
			modal(self.options.modal, self.element);

			// Show modal if urlParams contains contact
			if (urlParams.get('contact')) {
				self.element.modal('openModal');
			}

			// Show modal on contact modal trigger click
			$(self.options.trigger).on('click', self.triggerModal.bind(this));
		},
		triggerModal: function (e) {
			var self = this;
			e.preventDefault();
			self.element.modal('openModal');
		},
	});

	return $.coke_europe.contactModal;
});
