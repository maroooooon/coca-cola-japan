define(["jquery"], function ($) {
    "use strict";
    return function (config, element) {
        var faq = $(element).find(".faq-item-question");
        $(faq).on("click", function () {
            $(this).closest(".faq-item").toggleClass("active");
        });
    };
});
