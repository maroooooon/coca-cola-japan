define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function($, modal){

    $.widget('mage.coke_cancel_order_show_modal', {
        options: {
            modalSelector: '',
            closeModalSelector: ''
        },

        /**
         *
         * @private
         */
        _create: function() {
            this._bind();
        },

        /**
         *
         * @private
         */
        _bind: function() {
            this._on({
                click: this.showModal
            })
        },

        /**
         *
         * @returns {string}
         */
        getModalSelector: function () {
            return this.options.modalSelector ? this.options.modalSelector : '#cancel-order-modal-content';
        },

        /**
         *
         * @returns {string}
         */
        getCloseModalSelector: function () {
            return this.options.closeModalSelector
                ? this.options.closeModalSelector
                : '#cancel-order-modal-content .close-modal';
        },

        /**
         *
         * @private
         */
        _bindCloseModal: function () {
            var self = this;
            $(this.getCloseModalSelector()).on('click', function () {
                $(self.getModalSelector()).modal('closeModal');
            });
        },

        /**
         *
         */
        showModal: function () {
            this._bindCloseModal();

            modal(
                {
                    type: 'popup',
                    modalClass: 'cancel-order-popup popup',
                    responsive: false,
                    buttons: []
                },

                $(this.getModalSelector())
            );
            $(this.getModalSelector()).modal('openModal');
        },
    });

    return $.mage.coke_cancel_order_show_modal;
});
