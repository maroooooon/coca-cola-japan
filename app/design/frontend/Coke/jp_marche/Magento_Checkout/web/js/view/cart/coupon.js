define([
    'jquery',
    'ko',
    'underscore',
    'uiComponent',
    'domReady!',
], function (
    $,
    ko,
    _,
    Component,
) {
    'use strict';

    return function (config, element) {
        var cookieText = _.unique($.cookieStorage.get('mage-messages'), 'text')
        var pageHeader = $('.page-header').height();
        var target = $('.cart-login-container');
        var decisionText = config.couponMessage;

        if(cookieText.length > 0){
            if(cookieText && cookieText[0].text === decisionText){
                $('.coupon-validate-message').show();
            }
        }

        $('#scroll-login-section').on('click', function () {
            var position = target.offset().top;
            $('body,html').animate({scrollTop: position - pageHeader});
        });
    };
});