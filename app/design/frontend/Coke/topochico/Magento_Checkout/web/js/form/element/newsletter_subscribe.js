
define([
    'jquery',
    'Magento_Ui/js/form/form',
    'ko'
], function($, Component, ko) {
    'use strict';
    return Component.extend({

        checkVal: ko.observable(false),

        initialize: function () {
            this._super();
            return this;
        },

        initObservable: function () {
            let self = this;
            this.checkVal.subscribe(function (newValue) {
                if(newValue){
                    if($('#customer-email')){
                        window.checkoutConfig.email =  $('#customer-email').val();
                    }
                    window.checkoutConfig.newsletter_subscribe = true;
                }else{
                    window.checkoutConfig.email = '';
                    window.checkoutConfig.newsletter_subscribe = false;
                }
            });

            $(document).on('change', '#customer-email', function () {
                if(self.checkVal()){
                    window.checkoutConfig.email =  $('#customer-email').val();
                    window.checkoutConfig.newsletter_subscribe = true;
                }else{
                    window.checkoutConfig.email = '';
                    window.checkoutConfig.newsletter_subscribe = false;
                }
            });

            return this;
        },
    });
});
