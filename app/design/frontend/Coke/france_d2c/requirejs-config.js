var config = {
    map: {
        "*": {
            pdp: "js/pdp",
            cans: "js/cans",
            megaMenu: "js/megamenu",
            validator: "js/validator",
            personalizer: "js/personalizer",
            charCounter: "js/char-counter",
            customCan: "js/custom-can",
            customBottle: "js/custom-bottle",
            productPreview: "js/product-preview",
        },
    },
    shim: {
        'slick': {
            deps: ['jquery']
        },
    },
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'js/swatch-renderer-mixin': true
            }
        }
    }
};