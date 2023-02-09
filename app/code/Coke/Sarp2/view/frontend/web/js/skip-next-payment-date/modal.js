define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function($, modal){

    $.widget('mage.coke_sarp2_skip_next_payment_date_modal', {
        options: {
            modalSelector: '',
            closeModalSelector: '',
            submitSelector: '',
            formSelector: '',
        },

        /**
         *
         * @private
         */
        _create: function() {
            this._bind();
            this._bindFormSubmit();
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
            return this.options.modalSelector ? this.options.modalSelector : '#modal-content';
        },

        /**
         *
         * @returns {string}
         */
        getCloseModalSelector: function () {
            return this.options.closeModalSelector
                ? this.options.closeModalSelector
                : '#modal-content .close-modal';
        },

        /**
         *
         * @returns {string}
         */
        getSubmitSelector: function () {
            return this.options.submitSelector
                ? this.options.submitSelector
                : '#modal-content .actions .submit';
        },

        /**
         *
         * @returns {string}
         */
        getFormSelector: function () {
            return this.options.formSelector
                ? this.options.formSelector
                : '#form-validate';
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

        _bindFormSubmit: function () {
            var self = this,
                isProcessing = false;

            if (!isProcessing) {
                $(this.getSubmitSelector()).on('click', function (e) {
                    isProcessing = true;
                    $(self.getFormSelector()).submit()
                });
            }
        },

        /**
         *
         */
        showModal: function () {
            this._bindCloseModal();
            this._bindFormSubmit();

            modal(
                {
                    type: 'popup',
                    modalClass: 'skip-next-payment-date-popup popup',
                    responsive: false,
                    buttons: []
                },

                $(this.getModalSelector())
            );
            $(this.getModalSelector()).modal('openModal');
        },
    });

    return $.mage.coke_sarp2_skip_next_payment_date_modal;
});
