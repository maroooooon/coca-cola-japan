var config = {
    "map": {
        "*": {
            "slick-local" : "js/vendor/slick.min"
        }
    },
    shim: {
        'slick-local': {
            deps: ['jquery']
        },
    },
    deps: [
        "js/slick-carousel"
    ]
};
