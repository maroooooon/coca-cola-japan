define([
    'mage/utils/wrapper'
], function (wrapper) {
    'use strict';

    return function (target) {

        let formatPrice = target.formatPrice;

        let wrappedFunction = wrapper.wrap(formatPrice, function(originalFunction, amount, format, isShowSign){
            // precision is inherited from the php Magento Plugin
            // This is simpler than overwriting the whole function to always round down,
            //converting the float to a string is a bit redundant, but I want Magento core to do the processing, not this mixin.
            let amountPieces = amount.toString().split('.');
            let preFormattedAmount = amountPieces[0] + ".00";
            return originalFunction(parseFloat(preFormattedAmount), format, isShowSign);
        });

        target.formatPrice = wrappedFunction;
        return target;
    }
});
