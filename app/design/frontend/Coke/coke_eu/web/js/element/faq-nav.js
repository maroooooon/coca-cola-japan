require([
    'jquery'
], function ($) {
    $(function() {
        $(".faq-nav .item a").click(function(e) {
            e.preventDefault();
            $(".faq-nav .item a").removeClass('active');
            $(this).addClass('active');
            let target = $(this).attr("href");
            let headerHeight = $('.page-header').height();
            if($(target)){
                $('html,body').animate({scrollTop: $(target).offset().top - headerHeight - 20},'slow');
            }
        });
    });
});
