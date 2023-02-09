define([
    "mage/utils/wrapper",
], function (wrapper) {
    "use strict";

    return function (target) {
        target.formAddressDataToQuoteAddress = wrapper.wrapSuper(
            target.formAddressDataToQuoteAddress,
            function (formData) {
                /* Checking if the formData is empty. If it is empty, it will return the formData. */
                /* This happens when there are no addresses in the account */
                if (!formData) {
                    return this._super(formData);
                }

                /* This is checking if the flat field is empty. If it is not empty, it will add the flat field to the
                street[0] field. */
                var flat = formData.custom_attributes["apartment-flat-field"];
                if (flat && formData.street[0].indexOf(flat) < 0) {
                    formData.street[0] = flat + " " + formData.street[0];
                }

                /* This is setting the custom attribute to null to avoid it appearing elsewhere */
                formData.custom_attributes["apartment-flat-field"] = null;

                return this._super(formData);
            }
        );

        return target;
    };
});
