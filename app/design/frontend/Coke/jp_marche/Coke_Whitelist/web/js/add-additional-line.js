define([
    'jquery',
    'personalizer'
], function($, personalizer){

    $.widget('mage.coke_pb_add_additional_line', {
        options: {
            addAdditionalLineSelector: '',
            targetFieldSelector: '',
            targetInputSelector: '',
            canvasSelector: ''
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
            var self = this;
            $(self.getAdditionalLineSelector()).on('click', self.toggleAdditionalLine.bind(this));
        },

        /**
         *
         */
        toggleAdditionalLine: function () {
            var canvas = $('#maincontent').find('canvas')[0];
            this.element.toggleClass('active');
            $(this.getTargetFieldSelector()).toggleClass('active');
            this.clearInput($(this.getTargetInputSelector()));

            if (!this.element.hasClass('active')) {
                personalizer.updateBottlePreview(canvas, this.options.label_pos_offset, this.options.label_max_width);
            }
        },

        /**
         *
         * @param $element
         */
        clearInput: function ($element) {
            $element.val('');
            $element.text('');
            $element.trigger('blur')
        },

        /**
         *
         * @returns {string}
         */
        getAdditionalLineSelector: function () {
            return this.options.addAdditionalLineSelector
                ? this.options.addAdditionalLineSelector
                : '#add-additional-line';
        },

        /**
         *
         * @returns {string}
         */
        getTargetFieldSelector: function () {
            return this.options.targetFieldSelector
                ? this.options.targetFieldSelector
                : '.phrase.line-1';
        },

        /**
         *
         * @returns {string}
         */
        getTargetInputSelector: function () {
            return this.options.targetInputSelector
                ? this.options.targetInputSelector
                : '.phrase.line-1 .input-text';
        }
    });

    return $.mage.coke_pb_add_additional_line;
});
