define(['jquery', 'underscore', 'productPreview'], function (
	$,
	_,
	productPreview
) {
	'use strict';

	$.widget('coke_europe.personalizedProduct', {
		activeStep: 1,
		stepElements: [],
		firstCheck: false,
		events: {
			click: 'click',
			stepChange: 'stepChange',
			swatchInit: 'swatch.initialized',
			enableCheck: 'enableCheck',
			enableStatus: 'enableStatusChange',
		},
		selectors: {
			start: '.btn--start',
			back: '.btn--back',
			next: '.btn--next',
			step: '.progress-step',
			form: '#product_addtocart_form',
			how: '.how-does-it-work',
			howSection: '.section--how-it-works',
			active: '[data-step].active',
			completed: '[data-step].completed',
			bottom: '.product-options-bottom',
			addToCart: '#product-addtocart-button',
			stepTitle: '#step_title',
			stepDescription: '#step_description',
			progress1: '.progress-step--1',
			progress2: '.progress-step--2',
			pending: 'input.enablecheckwarn',
			denied: 'input.enablecheckdeny',
			approved: 'input.enablecheckfine',
			moderated: '.moderated-input-status',
			moderatedInputs: 'input[data-enable-moderation]',
			pendingInput: 'input[name="pending_approval"]',
		},
		_create: function () {
			var self = this;
			self.urlParams = new URLSearchParams(window.location.search);
			self._initEnable();
			self._bind();
		},

		_bind: function () {
			var self = this,
				events = self.events,
				selectors = self.selectors;
			$(document).on(events.swatchInit, self._setupSteps.bind(this));
			$(document).on(events.enableStatus, self._enableStatus.bind(this));
			$(document).on(events.enableCheck, self._enableCheck.bind(this));
			$(self.element).on(events.stepChange, self.changeStep.bind(this));
			$(selectors.start).on(events.click, self.showNextStep.bind(this));
			$(selectors.step).on(events.click, self.showStep.bind(this));
			$(selectors.next).on(events.click, self.showNextStep.bind(this));
			$(selectors.back).on(events.click, self.showPrevStep.bind(this));
			$(selectors.how).on(events.click, self.showHow.bind(this));

			$(selectors.moderatedInputs).on(
				'input change',
				_.debounce(self._handleInputChange, 500)
			);
		},
		// Add data-step to each swatch attribute based on system config
		_setupSteps: function () {
			var self = this,
				urlParams = self.urlParams;
			self.element.find('.brand_swatch').attr('data-step', 1);
			self.element.find('.package_bev_type').attr('data-step', 1);
			self.element.find('.pattern').attr('data-step', 2);
			self.stepElements[1] = self.element.find('[data-step="1"]');
			self.stepElements[2] = self.element.find('[data-step="2"]');
			self.stepElements[3] = self.element.find('[data-step="3"]');
			// Set active step to 3 if required url params are set
			if (
				urlParams.has('brand_swatch') &&
				urlParams.has('package_bev_type') &&
				urlParams.has('pattern') &&
				urlParams.has('prefilled_message')
			) {
				self.activeStep = 3;
			}
			$(self.element).trigger(self.events.stepChange);
		},
		_initEnable: function () {
			var self = this;
			$.getScript(
				self.options.enableScriptSrc,
				function (data, textStatus, jqxhr) {}
			);
		},
		_enableStatus: function (e) {
			var self = this;
			if (e.detail.isReady && !self.firstCheck) {
				EnableProfanityCheck[0].DoCheck('P');
				EnableProfanityCheck[0].DoCheck('F');
				self.firstCheck = true;
			}
		},
		_handleInputChange: function (e) {
			//	EnableProfanityCheck[0].DoCheck('P');
			//	EnableProfanityCheck[0].DoCheck('F');
			$.validator.validateSingleElement(this);
		},
		_enableCheck: function (e) {
			var self = this,
				selectors = self.selectors,
				approved = selectors.approved,
				pending = selectors.pending,
				denied = selectors.denied;

			// Disable add to cart
			$('#product-addtocart-button').prop('disabled', true);

			// Loading
			if (e.detail.perc < 100) {
				$(selectors.pendingInput).val(2);
				return $(selectors.form).attr('data-enable-status', 'loading');
			}

			// Add status icon clean this up if approved
			$(selectors.moderatedInputs).each(function (i, input) {
				if ($(input).hasClass('enablecheckwarn')) {
					$(input)
						.closest('.custom-option-input')
						.removeClass('approved denied')
						.addClass('pending');
				}
				if ($(input).hasClass('enablecheckfine')) {
					$(input)
						.closest('.custom-option-input')
						.removeClass('pending denied')
						.addClass('approved');
				}
				if ($(input).hasClass('enablecheckdeny')) {
					$(input)
						.closest('.custom-option-input')
						.removeClass('pending approved')
						.addClass('denied');
				}
			});

			// Denied
			if (self.element.find(denied).length) {
				$(selectors.pendingInput).val(0);
				$(selectors.form).attr('data-enable-status', 'denied');
				if (self.options.enableModeration) {
					localStorage.setItem('canShowPreview', false);
				}
				$('#product-addtocart-button').prop('disabled', true);
				return productPreview.clearPreview();
			}
			// Pending Approval
			if (self.element.find(pending).length) {
				$(selectors.pendingInput).val(1);
				$(selectors.form).attr('data-enable-status', 'pending');
				$('#product-addtocart-button').prop('disabled', false);

				if (self.options.enableModeration) {
					localStorage.setItem('canShowPreview', false);
					return productPreview.clearPreview();
				} else {
					localStorage.setItem('canShowPreview', true);
					return productPreview.showPreview();
				}
			}
			// Approved
			if (self.element.find(approved).length) {
				$(selectors.pendingInput).val(2);
				$(selectors.form).attr('data-enable-status', 'approved');
				$('#product-addtocart-button').prop('disabled', false);
				if (self.options.enableModeration) {
					localStorage.setItem('canShowPreview', true);
				}
				return productPreview.showPreview();
			}
		},
		changeStep: function () {
			var self = this,
				selectors = self.selectors,
				activeStep = self.activeStep,
				step = self.options.stepsData[self.activeStep];

			$(selectors.active).removeClass('active');
			self.stepElements[activeStep].addClass('active');
			$(selectors.stepTitle).text(step.title || '');
			$(selectors.stepDescription).text(step.description || '');
			$(self.element).find(selectors.completed).removeClass('completed');
			$(self.element).find(selectors.bottom).hide();

			// Scroll to top of page on step change
			$('html, body').animate(
				{
					scrollTop: 0,
				},
				500
			);

			if (activeStep === 2) {
				$(self.element).find(selectors.progress1).addClass('completed');
			}
			if (activeStep === 3) {
				$(self.element).find(selectors.progress1).addClass('completed');
				$(self.element).find(selectors.progress2).addClass('completed');
				$(self.element).find(selectors.bottom).fadeIn();
			}
		},
		// Scroll to How it Works section
		showHow: function () {
			var self = this;
			$('html, body').animate(
				{
					scrollTop: $(self.selectors.howSection).offset().top,
				},
				500
			);
		},
		showStep: function (e) {
			var self = this,
				activeStep = self.activeStep,
				step = parseInt(e.currentTarget.dataset.step);

			// Check if current step is valid before proceed
			if (step > activeStep && !self.validateStep(activeStep)) {
				return;
			}

			// Prevent jumping from step 1 to 3 if step 2 doesn't validate
			if (step === 3 && activeStep === 1) {
				if (self.validateStep(2)) {
					self.activeStep = 3;
				} else {
					self.activeStep = 2;
				}
				return $(self.element).trigger(self.events.stepChange);
			}
			// Proceed to step
			if (step) {
				self.activeStep = step;
				return $(self.element).trigger(self.events.stepChange);
			}
		},
		showNextStep: function () {
			var self = this;
			if (!self.validateStep(self.activeStep)) return;
			self.activeStep = parseInt(self.activeStep + 1);
			return $(self.element).trigger(self.events.stepChange);
		},
		showPrevStep: function () {
			var self = this;
			self.activeStep = parseInt(self.activeStep - 1);
			return $(self.element).trigger(self.events.stepChange);
		},
		validateStep: function (stepNumber) {
			var self = this,
				valid = true,
				inputs = self.stepElements[stepNumber].find('input');

			$(inputs).each(function (i, input) {
				if (!$.validator.validateSingleElement(input)) valid = false;
			});

			return valid;
		},
	});

	return $.coke_europe.personalizedProduct;
});
