define(['jquery'], function ($) {
    'use strict';

    let whitelistValueStatus = '';

    return {

        debounce: function(fn, ms = 0) {
            var timeout;
            return function(...args) {
              clearTimeout(timeout);
              timeout = setTimeout(() => fn.apply(this, args), ms);
            };
        },
        validateInput: function(event) {
            var container = $(event.target).closest('.whitelist-input'),
                message = $(container).closest('.phrase').find('.message'),
                self = this;

            if (event.target.value) {
                $(container).removeClass('whitelist-pending');

                $.ajax({
                    url: '/white_list/ajax/jpvalidate',
                    dataType: 'json',
                    data: {value: event.target.value, typeId: event.target.dataset.type},
                    success: function (data) {

                        if (data) {
                            if (data.status === "success") {
                                $(container).removeClass('invalid');
                                $(container).addClass('valid');
                                $(message).html('');
                                whitelistValueStatus = data.whitelist_value_status;
                                if (!whitelistValueStatus) {
                                    $(container).addClass('whitelist-pending');
                                }
                            }
                        }
                        $(document).trigger('updatePreview');
                    },
                    error: function (err) {
                        if (err.status === 400) {
                            let response = err.responseJSON;
                            if (
                                response.status === "denied" ||
                                response.status === "illegal_character" ||
                                response.status === "not_found" ||
                                response.status === "error"
                            ) {
                                $(message).html(response.error);
                                $(container).removeClass('valid');
                                $(container).addClass('invalid');
                            }
                            if (data.status === "not_found" && event.target.dataset.allow_pending) {
                                // future pending phrases and names here
                            }
                        } else {
                            console.error(err);
                            $(container).removeClass('valid');
                            $(container).addClass('invalid');

                            $(message).show();
                        }
                        $(document).trigger('updatePreview');
                    }
                });
            } else {
                let keepError = 0;
                $('.whitelist-input').each(function() {
                    if ($(container).attr('id') === $(this).attr('id')) {
                        $(this).removeClass('invalid');
                        $(this).removeClass('valid');
                        if($(this).hasClass('ui-autocomplete-input')) {
                            $(this).closest('.phrase').find('.ui-helper-hidden-accessible').remove();
                        }
                    } else {
                        if ($(this).val() === '' || $(this).val() === undefined) {
                            $(this).removeClass('invalid');
                            $(this).removeClass('valid');
                        } else if($(this).hasClass('invalid')) {
                            keepError = 1;
                        }
                    }
                });
                if (keepError === 0) {
                    $(message).html('');
                }
                $(document).trigger('updatePreview');
            }
        },
        validateKeys: function (event) {
            var key = event.which,
                invalidKeys = [13,35,36,37,38,39,40,41,42,43,44,45,46,47,58,59,60,61,62,63,91,93,94,123,124,125,126];
                //console.log('[key pressed] ', key);
            if (invalidKeys.includes(key) || key === 32 && event.target.selectionStart === 0) {
                //console.log('[invalid key] ', key);
                event.preventDefault();
            }
        },
        removeWhitespace: function (event) {
            setTimeout(function() {
                $(event.target).val(function(i, val) {
                    return val.trimStart();
                })
            })
        },
        removeSpecialChars: function(event) {
            setTimeout(function() {
                $(event.target).val(function(i, val) {
                  return val.replace(/[#$%^*()+=[';,./{}|:<>?~]/g, '');
                })
            })
        },
        getApprovalStatus: function () {
            return whitelistValueStatus;
        },
	};
});
