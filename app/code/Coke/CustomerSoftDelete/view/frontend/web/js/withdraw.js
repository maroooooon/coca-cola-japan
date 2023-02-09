define([
    'jquery',
    'underscore',
    'mage/translate',
    'Magento_Ui/js/modal/modal'
], function ($, _) {
    'use strict';

    $.widget('mage.customerWithdraw', {
        /**
         * Creates widget
         * @private
         */
        _create: function () {
            let self = this;
            let modalOptions = {
                buttons: [{
                    text: $.mage.__('Cancel'),
                    class: '',
                    click: function () {
                        this.closeModal();
                    }
                }],
                clickableOverlay: true,
                modalClass: 'customer-withdrawal-modal',
                responsive: true,
                title: $.mage.__('退会'),
            };
            $('.withdraw-modal').modal(modalOptions);
            $('.action.withdraw').on('click', function () {
                $('.withdraw-modal').trigger('openModal');
            });

        },
    });

    return $.mage.customerWithdraw;
});
