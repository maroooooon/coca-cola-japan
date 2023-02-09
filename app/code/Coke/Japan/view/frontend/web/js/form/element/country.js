define([
    'underscore',
    'Magento_Ui/js/form/element/select',
], function (_, Component) {
    'use strict';

    return Component.extend({

        initialize: function () {
            this._super();

            // if there is an initial value and there is only one option to select
            if (this.initialValue && (this.initialOptions.length <= 1)) {
                this.visible(0);
            }

            return this;
        }
    });
});
