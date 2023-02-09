define([
    'jquery',
    'matchMedia',
    'domReady!'
], function ($, mediaCheck) {
    'use strict';

    $(function() {
        bindHeroScrollDown();
    });

    mediaCheck({
        media: '(min-width: 768px)',
        // Switch to Desktop Version
        entry: function () {
            homepageHero();
        },
        // Switch to Mobile Version
        exit: function () {
        }
    });


   function homepageHero() {
        if($('.hero').length){
            let headerHeight = $('.page-header').height();
            let vhHeight = 'calc(100vh - ' + headerHeight +'px)';
            $('.hero').css('min-height', vhHeight);
        }
   }
   
   function bindHeroScrollDown() {
       $('.hero .scroll-arrow').click(function (e) {
           let elm = $('.hero');
           $('html, body').animate({
               scrollTop: elm.offset().top + elm.height()
           }, 'slow');
       });
   }

});
