define(["jquery"], function ($) {
    "use strict";

    $.widget("mage.coke_megamenu", {
        selectors: {
            dropdown: "#dropdown-menu",
            dropdownLink: "[data-dropdown-link]",
            category: "[data-category]",
        },
        _create: function () {
            var self = this;
            self._bind();
        },
        _bind: function () {
            var self = this;
            /* Show dropdown on mouseover */
            self._on(self.element.find(self.selectors.dropdownLink), {
                mouseover: self._showDropdown,
            });
            /* Hide dropdown on mouseleave */
            $(self.selectors.dropdown).on("mouseleave", self._hideDropdown.bind(this));
            $( ".header-nav a:not([data-dropdown-link])").on("mouseover", self._hideDropdown.bind(this));
            $( "#site-header").on("mouseleave", self._hideDropdown.bind(this));
            /* Show effect on hover */
            self._on(self.element.find(self.selectors.category), {
                mouseover: self._highlightCategory,
            });
        },
        _showDropdown: function (event) {
            var self = this;
            self.element.find('a.active').removeClass('active');
            $(self.selectors.dropdown).show();
            self.element.find(self.selectors.dropdownLink).addClass('active');

        },
        _hideDropdown: function (event) {
            var self = this;
            self.element.find('a.active').removeClass('active');
            $(self.selectors.dropdown).hide();
        },
        _highlightCategory: function (event) {
            var self = this,
             category = event.currentTarget.dataset.category;
            self.element.find('#dropdown-menu a.active').removeClass('active');
            self.element.find('[data-category="'+category+'"]').addClass('active');
        },
    });

    return $.mage.coke_megamenu;
});
