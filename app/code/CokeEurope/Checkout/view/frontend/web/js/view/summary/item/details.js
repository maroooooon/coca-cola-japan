define(
    [
        'uiComponent'
    ],
    function (Component) {
        "use strict";
        var quoteItemData = window.checkoutConfig.quoteItemData;
        return Component.extend({
            defaults: {
                template: 'CokeEurope_Checkout/summary/item/details'
            },
            quoteItemData: quoteItemData,
            /* This function is used to get the name of the item. */
            getValue: function(quoteItem) {
                return quoteItem.name;
            },
            /* This function is used to check if the item is pending approval. */
            isPendingApproval: function(quoteItem) {
                var item = this.getItem(quoteItem.item_id);
                if(item.pending_approval === "1"){
                    return item.pending_approval;
                }else{
                    return '';
                }
            },
            /* A function that loops through the quoteItemData array and returns the element that matches the item_id. */
            getItem: function(item_id) {
                var itemElement = null;
                _.each(this.quoteItemData, function(element, index) {
                    if (element.item_id == item_id) {
                        itemElement = element;
                    }
                });
                return itemElement;
            }
        });
    }
);
