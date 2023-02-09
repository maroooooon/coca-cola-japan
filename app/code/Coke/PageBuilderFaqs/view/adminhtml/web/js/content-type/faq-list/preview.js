define([
    "jquery",
    "mage/translate",
    "Magento_PageBuilder/js/events",
    "Magento_PageBuilder/js/content-type/preview-collection",
    "Magento_PageBuilder/js/content-type-factory",
    "Magento_PageBuilder/js/config",
    "Magento_PageBuilder/js/content-type-menu/option",
], function (
    $,
    $t,
    events,
    PreviewCollection,
    createContentType,
    pageBuilderConfig,
    option
) {
    "use strict";

    /**
     * @param parent
     * @param config
     * @param stageId
     * @constructor
     */
    function Preview(parent, config, stageId) {
        PreviewCollection.call(this, parent, config, stageId);
    }

    Preview.prototype = Object.create(PreviewCollection.prototype);

    /**
     * Root element
     */
    Preview.prototype.element = null;

    /**
     * Bind events to add empty FAQ Item
     */
    Preview.prototype.bindEvents = function bindEvents() {
        var self = this;

        PreviewCollection.prototype.bindEvents.call(this);

        events.on("faq_list:dropAfter", function (args) {
            if (
                args.id === self.contentType.id &&
                self.contentType.children().length === 0
            ) {
                self.addFaqItem();
            }
        });

        events.on("faq_item:renderAfter", (args) => {
            $(args.element).addClass("faq-item");
            var faq = $(args.element).find(".faq-item-question");
            $(faq).on("click", function () {
                $(args.element).toggleClass("active");
            });
        });
    };

    /**
     * Add FAQ item
     */
    Preview.prototype.addFaqItem = function () {
        var self = this;
        createContentType(
            pageBuilderConfig.getContentTypeConfig("faq_item"),
            this.contentType,
            this.contentType.stageId,
            {
                question:
                    $t("Question ") + (self.contentType.children().length + 1),
                answer:
                    $t("Answer ") + (self.contentType.children().length + 1),
            }
        ).then(function (container) {
            self.contentType.addChild(container);
        });
    };

    /**
     * Return content menu options
     *
     * @returns {object}
     */
    Preview.prototype.retrieveOptions = function () {
        var self = this;
        var options = PreviewCollection.prototype.retrieveOptions.call(this);

        options.add = new option({
            preview: this,
            icon: "<i class='icon-pagebuilder-add'></i>",
            title: "Add",
            action: self.addFaqItem,
            classes: ["add-child"],
            sort: 10,
        });
        return options;
    };

    /**
     * Set root element
     *
     * @returns {void}
     */
    Preview.prototype.afterRender = function (element) {
        this.element = element;
    };

    /**
     * Check if content type is container
     *
     * @returns {boolean}
     */
    Preview.prototype.isContainer = function () {
        return true;
    };

    return Preview;
});
