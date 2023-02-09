define([
    'jquery',
    'underscore'
], function ($, _) {
    return function (config, element) {
        var $element = $(element),
            selectors = {
                shareSitePopup: '.share-site-link-container .share-site-popup'
            };

        $element.on('click', function() {
            var tempInput = document.createElement("input");
            tempInput.style = "position: absolute; left: -1000px; top: -1000px";
            tempInput.value = config.baseUrl;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);

            $(selectors.shareSitePopup).addClass('active');
            setTimeout(function () {
                $(selectors.shareSitePopup).removeClass('active');
            }, 4000)
        });
    }
});
