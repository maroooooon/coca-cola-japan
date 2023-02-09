define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    return function (config, element) {
        var $wrapper = $(element),
            $qtyInput = $wrapper.find(config.inputSelector || 'input.qty'),
            $decrementBtn = $wrapper.find(config.decrementSelector || '[data-trigger=decrement]'),
            $incrementBtn = $wrapper.find(config.incrementSelector || '[data-trigger=increment]');

        $decrementBtn.on('click', function () {
            var current = parseInt($qtyInput.val()) || 1;
            if (current > 1){
                $qtyInput.val(parseInt(--current) || 1);
            }
        });

        $incrementBtn.on('click', function () {
            var current = parseInt($qtyInput.val()) || 1;
            var max =  $qtyInput.attr("max") || 999;
            console.log(max);
            if (current < max) {
                $qtyInput.val(parseInt(++current) || 1);
            }
        });
    }
});
