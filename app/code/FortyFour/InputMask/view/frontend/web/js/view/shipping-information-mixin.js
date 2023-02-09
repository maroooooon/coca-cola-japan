define(['jquery', 'ko', 'underscore', 'inputmask'],
    function ($, ko, _) {
    'use strict';

    return function (Component) {
        return Component.extend({

            initialize: function(){
                this._super();

                if (window.checkoutConfig.input_mask_config) {
                    var inputMaskConfig = window.checkoutConfig.input_mask_config;

                    if (inputMaskConfig.postcode) {
                        var regexPostcodeInputMask = {
                            regex: String.raw`${inputMaskConfig.postcode}`
                        }

                        setTimeout(function () {
                            $('input[name="postcode"]').inputmask(regexPostcodeInputMask);
                        }, 500)
                    }

                    if (inputMaskConfig.telephone) {
                        var regexTelephoneInputMask = {
                            regex: String.raw`${inputMaskConfig.telephone}`
                        }

                        setTimeout(function () {
                            $('input[name="telephone"]').inputmask(regexTelephoneInputMask);
                        }, 500)
                    }
                }
            },
        });
    };
});
