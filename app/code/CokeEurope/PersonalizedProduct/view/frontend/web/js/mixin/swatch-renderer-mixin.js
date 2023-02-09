define(['jquery', 'productPreview', 'mage/translate'], function (
	$,
	productPreview
) {
	'use strict';

	return function (widget) {
		$.widget('mage.SwatchRenderer', widget, {
			/**
			 * Rebuild container
			 * Modified to include custom functions for dynamic attributes
			 * @private
			 */
			_Rebuild: function () {
				var self = this;
				self._super();
				self._updateLabelAttributes();
				self._updateDynamicValidation();
				self._updateDynamicAttributes();
			},
			/**
			 * Update Personalized Product Dynamic Attributes
			 *
			 * @private
			 */
			_updateDynamicAttributes: function () {
				var self = this,
					productId = self.getProduct(),
					attributes = self.options.jsonConfig.dynamicAttributes;

				if (!productId || !attributes) {
					return;
				}
				$.each(attributes, function (code, attr) {
					var value = attr[productId] || attr['default'];
					if (!value) {
						return;
					}
					$('[data-attribute-content=' + code + ']')
						.find('.attribute-content')
						.html(value);
				});
			},
			/**
			 * Update Personalized Product Dynamic Label Validation
			 *
			 * @private
			 */
			_updateDynamicValidation: function () {
				var self = this,
					productId = self.getProduct(),
					attributes = self.options.jsonConfig.labelAttributes,
					limit = attributes?.characterLimit[productId];
				if (!productId || !limit) {
					return;
				}
				if (regex in attributes) {
					if (productId in attributes.regex) {
						var regex = attributes.regex[productId];
					}
				}
				var inputs = $('input[data-enable-moderation]');
				var message = $.mage.__(
					'Your label exceeds the limit of ' + limit + ' characters.'
				);

				$.each(inputs, function () {
					if (regex) {
						$(this).attr('data-regex', regex);
					}
				});
				localStorage.setItem('character-limit', limit);
				localStorage.setItem('character-limit-msg', message);
			},
			/**
			 * Update Personalized Product Label Attributes
			 *
			 * @private
			 */
			_updateLabelAttributes: function () {
				var self = this,
					productId = self.getProduct(),
					$canvas = $('#preview_canvas'),
					attributes = self.options.jsonConfig.labelAttributes,
					canShowPreview = localStorage.getItem('canShowPreview');

				if (!productId || !attributes) {
					return;
				}
				productPreview.clearPreview();
				$canvas.attr({
					'data-x': attributes.x[productId] || '',
					'data-y': attributes.y[productId] || '',
					'data-width': attributes.width[productId] || '',
					'data-color': attributes.color[productId] || '',
					'data-font-size': attributes.fontSize[productId] || '',
					'data-font-family': attributes.fontFamily[productId] || '',
				});
				if (canShowPreview === 'true') {
					return productPreview.showPreview();
				}
			},
		});
		return $.mage.SwatchRenderer;
	};
});
