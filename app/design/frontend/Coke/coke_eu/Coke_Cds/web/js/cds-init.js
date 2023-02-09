define([], function() {
    if(window.cdsMode === 'Prod'){
        window.cdsConfig.environment = window.cdsConfig;
    } else{
        window.cdsConfig.environment = window.cdsConfig;
    }

    window.cdsConfig.locale = window.cdsConfig.locale || window.navigator.language;

    return cds.init(window.cdsConfig);
});
