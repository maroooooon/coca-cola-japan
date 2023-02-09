define(['jquery'], function ($) {
	'use strict';

	return {
		// Split the phrase into multiple lines based on max width
		drawLabel: function (canvas, phrase) {
			var text = '';
			var lines = [];
			var lineToTest = '';
			var ctx = canvas.getContext('2d');
			var config = canvas.dataset || {};
			var x = parseFloat(config.x);
			var y = parseFloat(config.y);
			var lineHeight = parseInt(config.fontSize);
			var words = phrase.split(' ');
			// Set the canvas size
			canvas.width = 585;
			canvas.height = 585;
			canvas.style.width = canvas.width + 'px';
			canvas.style.height = canvas.height + 'px';

			// Set the canvas context
			ctx.fillStyle = config.color || 'white';
			ctx.textAlign = 'center';
			ctx.textBaseline = 'middle';
			ctx.font = config.fontSize + 'px ' + config.fontFamily;

			// Loop through the words and create lines based on max width
			for (var i = 0; i < words.length; i++) {
				lineToTest += words[i];
				let measured = ctx.measureText(lineToTest);
				if (measured.width > parseFloat(config.width) && i > 0) {
					lines.push({ text, x, y });
					text = words[i] + ' ';
					lineToTest = words[i] + ' ';
					y += lineHeight;
				} else {
					text += words[i] + ' ';
				}
				if (i === words.length - 1) lines.push({ text, x, y });
			}
			// Adjust Y Axis if phrase has more than 2 lines
			if (lines.length > 2) {
				lines = lines.map((line) => ({ ...line, y: line.y - 5 }));
			}
			// Draw each line on the canvas
			return lines.forEach(function ({ text, x, y }) {
				ctx.fillText(text, x, y);
			});
		},

		showPreview: function () {
			var canvas = document.getElementById('preview_canvas');
			if (!canvas) return;
			var fields = document.querySelectorAll('[data-enable-moderation]');
			var phrase = fields[0].value.trim();
			var name = fields[1].value.trim();
			// Add the name to the phrase
			if (name) phrase += ' ' + name;
			// Draw the label
			this.drawLabel(canvas, phrase);
		},
		clearPreview: function () {
			var canvas = document.getElementById('preview_canvas');
			var context = canvas.getContext('2d');
			return context.clearRect(0, 0, canvas.width, canvas.height);
		},
		showPending: function () {
			var pending = document.getElementById('pending_approval');
			return (pending.style.display = 'block');
		},
		hidePending: function () {
			var pending = document.getElementById('pending_approval');
			return (pending.style.display = 'none');
		},
		showLoader: function () {
			var loader = document.getElementById('pp_loader');
			return (loader.style.display = 'flex');
		},
		hideLoader: function () {
			var loader = document.getElementById('pp_loader');
			return (loader.style.display = 'none');
		},
	};
});
