define([
    'jquery',
    'matchMedia',
    'Magento_Ui/js/modal/modal',
    'domReady!'
],function($, mediaCheck, modal){

    $.widget('mage.modalFilterMobile', {
        _create: function() {
            mediaCheck({
                media: '(min-width: 768px)',
                // Switch to Desktop Version
                entry: function () {

                },
                // Switch to Mobile Version
                exit: function () {
                    let modalOptions = {
                        buttons: [],
                        clickableOverlay: true,
                        modalClass: 'category-filter',
                        responsive: true,
                        title: '絞り込み',
                        type: 'slide'
                    };
                    $('#layered-filter-block').modal(modalOptions);
                    $('.filter-toggle').on('click', function() {
                        $('#layered-filter-block').trigger('openModal');
                    });
                }
            });
        }
    });

    return $.mage.modalFilterMobile;

});
