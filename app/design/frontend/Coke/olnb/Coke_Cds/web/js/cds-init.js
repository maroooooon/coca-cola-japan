define([], function() {

    if (typeof cds.getEnvironment  !== "undefined") {
        if (window.cdsMode === 'Prod') {
            window.cdsConfig.environment = window.cdsConfig.environment || cds.getEnvironment().Prod;
        } else {
            window.cdsConfig.environment = window.cdsConfig.environment || cds.getEnvironment().Stage;
        }
    }

    window.cdsConfig.locale = window.cdsConfig.locale || window.navigator.language;

    return cds.init(window.cdsConfig);
});
