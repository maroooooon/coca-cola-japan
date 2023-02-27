var config = {
    config: {
        mixins: {
            'jquery/ui-modules/widgets/menu': {
                'Magento_Theme/js/menu-mixin': true
            }
        }
    },
    map: {
        "*": {
            "menu": "Magento_Theme/js/menu-custom"
        }
    }
};
