define(['jquery'],
    function($) {
    'use strict';

    return function() {
        $.validator.addMethod(
            'validate-phone-number-europe',
            function(value) {
                let matchesPattern = false;
                let storeCode = $('body').data('store-code');
                let phone_patterns = {
                    'ireland_english': ['^(0)?([1-9]\\d{0,2})\\d{7}$'],
                    'finland_finnish': ['^((04[0-9]{1})(\s?|-?)|050(\s?|-?)|0457(\s?|-?)|[+]?358(\s?|-?)50|0358(\s?|-?)50|00358(\s?|-?)50|[+]?358(\s?|-?)4[0-9]{1}|0358(\s?|-?)4[0-9]{1}|00358(\s?|-?)4[0-9]{1})(\s?|-?)(([0-9]{3,4})(\s|\-)?[0-9]{1,4})$'],
                    'belgium_dutch': ['^(((\\+|00)32[ ]?(?:\\(0\\)[ ]?)?)|0){1}(4(60|[789]\\d)\\/?(\\s?\\d{2}\\.?){2}(\\s?\\d{2})|(\\d\\/?\\s?\\d{3}|\\d{2}\\/?\\s?\\d{2})(\\.?\\s?\\d{2}){2})$'],
                    'belgium_french': ['^(((\\+|00)32[ ]?(?:\\(0\\)[ ]?)?)|0){1}(4(60|[789]\\d)\\/?(\\s?\\d{2}\\.?){2}(\\s?\\d{2})|(\\d\\/?\\s?\\d{3}|\\d{2}\\/?\\s?\\d{2})(\\.?\\s?\\d{2}){2})$'],
                    'france_french': ['^(?:(?:\\+|00)33[\\s.-]{0,3}(?:\\(0\\)[\\s.-]{0,3})?|0)[1-9](?:(?:[\\s.-]?\\d{2}){4}|\\d{2}(?:[\\s.-]?\\d{3}){2})$'],
                    'germany_german': ['^((\\+49)|(0049)|0)(\\(?([\\d \\-\\)\\–\\+\\/\\(]+){6,}\\)?([ .\\-–\\/]?)([\\d]+))$'],
                    'netherlands_dutch': ['^((\\+31)|(0031)|0)(\\(0\\)|)(\\d{1,3})(\\s|\\-|)(\\d{8}|\\d{4}\\s\\d{4}|\\d{2}\\s\\d{2}\\s\\d{2}\\s\\d{2})$'],
                    'northern_ireland_english' : ['^(((\\+44\\s?\\d{4}|\\(?0\\d{4}\\)?)\\s?\\d{3}\\s?\\d{3})|((\\+44\\s?\\d{3}|\\(?0\\d{3}\\)?)\\s?\\d{3}\\s?\\d{4})|((\\+44\\s?\\d{2}|\\(?0\\d{2}\\)?)\\s?\\d{4}\\s?\\d{4}))(\\s?\\#(\\d{4}|\\d{3}))?$', '^(\\+44\\s?7\\d{3}|\\(?07\\d{3}\\)?)\\s?\\d{3}\\s?\\d{3}$', ''],
                    'great_britain_english' : ['^(((\\+44\\s?\\d{4}|\\(?0\\d{4}\\)?)\\s?\\d{3}\\s?\\d{3})|((\\+44\\s?\\d{3}|\\(?0\\d{3}\\)?)\\s?\\d{3}\\s?\\d{4})|((\\+44\\s?\\d{2}|\\(?0\\d{2}\\)?)\\s?\\d{4}\\s?\\d{4}))(\\s?\\#(\\d{4}|\\d{3}))?$', '^(\\+44\\s?7\\d{3}|\\(?07\\d{3}\\)?)\\s?\\d{3}\\s?\\d{3}$', '^((\\(?0\\d{4}\\)?\\s?\\d{3}\\s?\\d{3})|(\\(?0\\d{3}\\)?\\s?\\d{3}\\s?\\d{4})|(\\(?0\\d{2}\\)?\\s?\\d{4}\\s?\\d{4}))(\\s?\\#(\\d{4}|\\d{3}))?$']
                };

                if (!(storeCode in phone_patterns)) {
                    return true;
                }

                $.each(
                    phone_patterns[storeCode],
                    function (index, pattern) {
                        if (new RegExp(pattern).test(value)) {
                            matchesPattern = true;
                        }
                    }
                );
                return matchesPattern;
            },
            $.mage.__('Please enter a valid phone number.')
        );

        $.validator.addMethod(
            'validate-phone-number-europe-simple',
            function(value) {
                let matchesPattern = false;
                let regex = '^\\+?(?:[0-9] ?){6,14}[0-9]$';

                if (new RegExp(regex).test(value)) {
                    matchesPattern = true;
                }
                return matchesPattern;
            },
            $.mage.__('Please enter a valid phone number.')
        );

        $.validator.addMethod(
            'validate-zip-postal-code-europe',
            function(value) {
                let matchesPattern = false;
                let storeCode = $('body').data('store-code');
                let zip_patterns = {
                    'ireland_english': ['^[0-9a-zA-Z]{3} [0-9a-zA-Z]{4}$', '^[0-9a-zA-Z]{7}$'],
                    'finland_finnish': ['^\\d{5}$'],
                    'belgium_dutch': ['^\\d{4}$'],
                    'belgium_french': ['^\\d{4}$'],
                    'france_french': ['^\\d{5}$'],
                    'germany_german': ['^\\d{2}$', '^\\d{4}$', '^\\d{5}$'],
                    'netherlands_dutch': ['^\\d{4}\\s{0,1}[A-Za-z]{2}$', '^[0-9a-zA-Z]{6}$'],
                    'northern_ireland_english': ['^(([A-Z][0-9]{1,2})|(([A-Z][A-HJ-Y][0-9]{1,2})|(([A-Z][0-9][A-Z])|([A-Z][A-HJ-Y][0-9]?[A-Z])))) [0-9][A-Z]{2}$'],
                    'great_britain_english': ['^[A-Z]{1,2}[0-9][A-Z0-9]? ?[0-9][A-Z]{2}$']
                };

                if (!(storeCode in zip_patterns)) {
                    return true;
                }

                $.each(
                    zip_patterns[storeCode],
                    function (index, pattern) {
                        if (new RegExp(pattern).test(value)) {
                            matchesPattern = true;
                        }
                    }
                );
                return matchesPattern;
            },
            $.mage.__('Please enter a valid postal code.')
        );

    }
});
