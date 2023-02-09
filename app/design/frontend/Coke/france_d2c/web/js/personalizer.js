define(['jquery'], function ($) {
    'use strict';
        return {
            debounce: function(fn, ms = 0) {
                let timeout;
                return function(...args) {
                  clearTimeout(timeout);
                  timeout = setTimeout(() => fn.apply(this, args), ms);
                };          
            },
            clearPreview: function(canvas) {
                var context = canvas.getContext("2d");
                return context.clearRect(0, 0, canvas.width, canvas.height);
            },
            updateCanPreview: function(canvas) {
                var context = canvas.getContext("2d"),
                    line1 = $('.line-0'),
                    line2 = $('.line-1'),
                    line3 = $('.line-2'),
                    line4 = $('.line-3');

                /* Clear Canvas on invalid  */
                if(line1.hasClass('invalid') || line2.hasClass('invalid') || line3.hasClass('invalid') || line4.hasClass('invalid')) {
                    $('#product-addtocart-button').attr('disabled', true);
                    return context.clearRect(0, 0, canvas.width, canvas.height);
                } else {
                    $('#product-addtocart-button').attr('disabled', false);
                }
                /* Set Font Style */
                context.fillStyle = "#000";
                context.textAlign = "left";
                context.textBaseline = "middle";
                context.font = "38px Covered By Your Grace, sans-serif";

                /* Can with Longer Pre-Phrase */
                if(document.body.classList.contains('product-can-plus-jamais-sans')) {
                    /* One Line */
                    if (line1.hasClass('valid') && !$(line2).is(':visible')) {
                        var phrase1 = line1.find('input')[0].value;
                        if(phrase1) context.fillText(phrase1, 245, 340, 220);
                    }
                    /* Two Lines */
                    if (line1.hasClass('valid') && $(line2).is(':visible')) {
                        var phrase1 = line1.find('input')[0].value;
                        var phrase2 = line2.find('input')[0].value;
                        if(phrase1) context.fillText(phrase1, 245, 315, 220);
                        if(phrase2) context.fillText(phrase2, 245, 375, 220);
                    }
                } else {
                    /* One Line */
                    if (line1.hasClass('valid') && !$(line2).is(':visible')) {
                        var phrase1 = line1.find('input')[0].value;
                        if(phrase1)context.fillText(phrase1, 245, 300, 220);
                    }
                    /* Two Lines */
                    if (line1.hasClass('valid') && $(line2).is(':visible')) {
                        var phrase1 = line1.find('input')[0].value;
                        var phrase2 = line2.find('input')[0].value;
                        if(phrase1) context.fillText(phrase1, 245, 275, 220);
                        if(phrase2) context.fillText(phrase2, 245, 335, 220);
                    }
                }

                /* Adjust context & add from & to names if applicable */
                context.save();
                context.translate(150, 200);
                context.rotate(-Math.PI / 20);
                context.textAlign = "right";
                context.font = "28px Covered By Your Grace, sans-serif";
                if(line3.hasClass('valid')) {
                    var fromLine = line3.find('input')[0].value;
                    if(fromLine) context.fillText('De ' + fromLine, 250, 350, 220);
                }
                if(line4.hasClass('valid')){
                    var toLine = line4.find('input')[0].value;
                    if(toLine) context.fillText('À ' + toLine, 250, 380, 220);
                }
                context.restore();
            },
            updateBottlePreview: function(canvas) {
                var context = canvas.getContext("2d"),
                    line1 = $('.line-0'),
                    line2 = $('.line-1');

                context.fillStyle = "#FFF";
                context.textAlign = "center";
                context.textBaseline = "middle";
                context.font = "28px You, sans-serif";

                /* Clear Canvas on invalid  */
                if(line1.hasClass('invalid') || line2.hasClass('invalid')) {
                    $('#product-addtocart-button').attr('disabled', true);
                    return context.clearRect(0, 0, canvas.width, canvas.height);
                } else {
                    $('#product-addtocart-button').attr('disabled', false);
                }

                /* One Line */
                if (line1.hasClass('valid') && !line2.hasClass('valid')) {
                    var phrase1 = line1.find('input')[0].value;
                    if(phrase1) context.fillText(phrase1, 350, 330, 160);
                }
                /* Two Lines */
                if (line1.hasClass('valid') && line2.hasClass('valid')) {
                    var phrase1 = line1.find('input')[0].value;
                    var phrase2 = line2.find('input')[0].value;
                    if(phrase1) context.fillText(phrase1, 350, 330, 160);
                    if(phrase2) context.fillText(phrase2, 350, 360, 160);
                }
            }
        };
    });