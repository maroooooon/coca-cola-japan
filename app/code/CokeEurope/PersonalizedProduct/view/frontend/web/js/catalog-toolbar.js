define(['jquery'], function ($) {
	return function (config, element) {
		var filters = $(element).find('.toolbar-item-label');
		$(filters).click(function () {
			$(this).closest('.toolbar-item').toggleClass('active');
		});
	};
});
