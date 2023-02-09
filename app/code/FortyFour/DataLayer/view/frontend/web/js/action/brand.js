define([
    'jquery'
], function ($) {
    return function (config, element) {
        var $element = $(element);

        $element.on('click', function() {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                'Brand': config.brand,
                'ProductName': config.product_name
            });
        });
    }
});
