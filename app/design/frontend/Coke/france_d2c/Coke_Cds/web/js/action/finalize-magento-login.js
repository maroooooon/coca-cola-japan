define([
    'jquery',
    'Coke_Cds/js/cds-init',
    'Coke_Cds/js/model/customer',
    'Coke_Cds/js/model/messages'
], function($, cdsInit, customerModel, messageContainer) {
    return function(userData, refererUrl) {
        var payload = {
            token: localStorage.token || $.cookie('cds-current-user-token'),
            device_id: localStorage.deviceId,
            form_key: $.mage.cookies.get('form_key'),
            user: userData
        };

        $('body').trigger('processStart');

        return customerModel.signIn(payload).done(function (res) {
            if (refererUrl) {
                window.location.replace(refererUrl);
            } else {
                window.location.reload();
            }
        }).fail(function (err) {
            $('body').trigger('processStop');
            setTimeout(function(){
                messageContainer.add(err.responseJSON.message, 'error');
            }, 1000);
        });
    }
});
