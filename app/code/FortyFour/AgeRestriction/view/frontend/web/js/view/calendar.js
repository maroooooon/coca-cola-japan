define([
    'jquery',
    'mage/translate',
    'mage/calendar'
], function ($, $t) {
    return function (config, element) {
        var $element = $(element);

        $element.calendar({
            changeMonth: true,
            changeYear: true,
            closeText: $t('Close'),
            currentText: $t('Go to Today'),
            dateFormat: "dd/M/Y",
            showButtonPanel: true,
            yearRange: "-120y:c+nn"
        });
    }
});
