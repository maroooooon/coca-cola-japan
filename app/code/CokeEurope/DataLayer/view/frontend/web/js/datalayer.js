define([
    "jquery",
    "Magento_Customer/js/customer-data",
    "Magento_Checkout/js/model/step-navigator",
], function ($, customerData, stepNavigator) {
    "use strict";

    $.widget("mage.coke_eu_datalayer", {
        /**
         * @private
         */
        options: {
            selectors: {
                product: ".product-card",
            },
        },
        _create: function () {
            var self = this;
            self._handleCartEvents();
            self._handleClickEvents();
            self._handleImpressions();
            if ($("body").hasClass("checkout-index-index")) {
                self._handleCheckoutSteps();
            }
        },
        _getCookie: function (name) {
            var v = document.cookie.match("(^|;) ?" + name + "=([^;]*)(;|$)");
            return v ? v[2] : null;
        },
        _setCookie: function (name, value, days) {
            var d = new Date();
            d.setTime(d.getTime() + 24 * 60 * 60 * 1000 * days);
            document.cookie =
                name + "=" + value + ";path=/;expires=" + d.toGMTString();
        },
        _deleteCookie: function (name) {
            var self = this;
            self._setCookie(name, "", -1);
        },
        _getList: function (product) {
            var self = this,
                list = "Catalog Page";

            if ($(product).parents(".widget").length) {
                list = "Catalog Widget";
            }
            if ($("body").hasClass("catalogsearch-result-index")) {
                list = "Search Results";
            }
            return list;
        },
        _handleCartEvents: function () {
            var self = this,
                DataLayer = customerData.get("datalayer");

            DataLayer.subscribe(
                function (data) {
                    // Fire addToCart event and delete the cookie
                    if (data.add_to_cart) {
                        var addEvent = JSON.parse(data.add_to_cart);
                        self._pushEvent(addEvent);
                        self._deleteCookie("dl_cart_item_added");
                    }
                    // Fire removeFromCart event and delete the cookie
                    if (data.remove_from_cart) {
                        var removeEvent = JSON.parse(data.remove_from_cart);
                        self._pushEvent(removeEvent);
                        self._deleteCookie("dl_cart_item_removed");
                    }
                }.bind(this)
            );
        },
        _handleClickEvents: function () {
            var self = this,
                product = self.options.selectors.product;
            $(product).on("click", self._handleProductClick.bind(this));
        },
        _handleProductClick: function (e) {
            var self = this,
                card = e.currentTarget,
                data = JSON.parse(card.dataset.datalayer),
                list = self._getList(card),
                clickEvent = {
                    event: "productClick",
                    ecommerce: {
                        click: {
                            actionField: { list },
                            products: [
                                {
                                    id: data.id,
                                    name: data.name,
                                    price: data.price,
                                    category: data.category,
                                    position: data.position,
                                    currencyCode: data.currencyCode,
                                },
                            ],
                        },
                    },
                };
            self._pushEvent(clickEvent);
        },
        _handleImpressions: function () {
            var self = this,
                impressions = [],
                cards = $(self.options.selectors.product);

            $.each(cards, function (i, product) {
                if (product.dataset.datalayer) {
                    var data = JSON.parse(product.dataset.datalayer),
                        list = self._getList(product);
                    data.list = list;
                    // Remove currencyCode from impressions
                    delete data["currencyCode"];
                    impressions.push(data);
                }
            });

            if (impressions.length) {
                return self._pushEvent({
                    event: "productImpression",
                    ecommerce: {
                        impressions,
                    },
                });
            }
        },
        _handleCheckoutSteps: function () {
            var self = this,
                currentStep = stepNavigator.getActiveItemIndex() + 1,
                products = self.options.checkoutProducts,
                stepEvent = {
                    event: "checkout",
                    ecommerce: {
                        currencyCode: self.options.currencyCode,
                        checkout: {
                            actionField: { step: currentStep },
                            products,
                        },
                    },
                };
            console.log("handleCheckoutSteps: ", self);
            $(window).on("hashchange", function () {
                currentStep = stepNavigator.getActiveItemIndex() + 1;
                stepEvent.ecommerce.checkout.actionField.step = currentStep;
                self._pushEvent(stepEvent);
            });
        },
        _pushEvent: function (event) {
            /* Google docs says to use this to clear the previous ecommerce object 
				but it creates an empty event so im excluding for now
				window.dataLayer.push({ ecommerce: null });
			*/
            return window.dataLayer.push(event);
        },
    });
    return $.mage.coke_eu_datalayer;
});
