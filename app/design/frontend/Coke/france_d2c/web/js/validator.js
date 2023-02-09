define(['jquery'], function ($) { 
    'use strict';
    return {
        debounce: function(fn, ms = 0) {
            var timeout;
            return function(...args) {
              clearTimeout(timeout);
              timeout = setTimeout(() => fn.apply(this, args), ms);
            };          
        },
        validateInput: function(event) {
            var container = $(event.target).closest('.phrase'),
                message = $(container).find('.message');

            //console.log('[validating phrase] ', event.target.value);
            $.ajax({
                url: '/white_list/ajax/validate',
                dataType: 'json',
                data: { phrase: event.target.value },
                success: function(data) {
                    if (data) {
                        //console.log('[phrase is valid]');
                        $(container).removeClass('invalid'),
                        $(container).addClass('valid'),
                        $(message).html(''),
                        $(document).trigger('updatePreview');
                    } else {
                        //console.log('[phrase is invalid]');
                        $(container).removeClass('valid'),
                        $(container).addClass('invalid'),
                        $(message).html("<p>L'aperçu n'est pas disponible. Le produit ne peut pas être ajouté au panier.</p>"),
                        $(document).trigger('updatePreview');
                    }
                },
                error: function(err) {
                    console.error(err);
                    $(container).removeClass('valid'),
                    $(container).addClass('invalid'),
                    $(message).show();
                }
            });
        },
        validateKeys: function (event) {
            var key = event.which,
                invalidKeys = [35,36,37,38,39,40,41,42,43,44,45,46,47,58,59,60,61,62,63,91,93,94,123,124,125,126];
                //console.log('[key pressed] ', key);
            if (invalidKeys.includes(key) || key === 32 && event.target.selectionStart === 0) {
                //console.log('[invalid key] ', key);
                event.preventDefault();
            }
        },
        removeWhitespace: function (event) {
            setTimeout(function() {
                $(event.target).val(function(i, val) {
                    return val.trim();
                })
            })
        },
        removeSpecialChars: function(event) {
            setTimeout(function() {
                $(event.target).val(function(i, val) {
                  return val.replace(/[#$%^&*()+=[';,./{}|:<>?~]/g, '');
                })
            })
        },
	};
});