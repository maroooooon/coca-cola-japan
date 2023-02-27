define(['jquery'], function ($) {
    'use strict';

    var menuWidgetMixin = {
        _create: function () {
            this.activeMenu = this.element;

            // Flag used to prevent firing of the click handler
            // as the event bubbles up through nested menus
            this.mouseHandled = false;
            this.lastMousePosition = { x: null, y: null };
            this.element
                .uniqueId()
                .attr( {
                    role: this.options.role,
                    tabIndex: 0
                } );

            this._addClass( "ui-menu", "ui-widget ui-widget-content" );
            this._on( {

                // Prevent focus from sticking to links inside menu after clicking
                // them (focus should always stay on UL during navigation).
                "mousedown .ui-menu-item": function( event ) {
                    event.preventDefault();

                    this._activateItem( event );
                },
                "click .ui-menu-item": function( event ) {
                    var target = $( event.target );
                    var active = $( $.ui.safeActiveElement( this.document[ 0 ] ) );
                    if ( !this.mouseHandled && target.not( ".ui-state-disabled" ).length ) {
                        this.select( event );

                        // Only set the mouseHandled flag if the event will bubble, see #9469.
                        if ( !event.isPropagationStopped() ) {
                            this.mouseHandled = true;
                        }

                        // Open submenu on click
                        if ( target.has( ".ui-menu" ).length ) {
                            this.expand( event );
                        } else if ( !this.element.is( ":focus" ) &&
                            active.closest( ".ui-menu" ).length ) {

                            // Redirect focus to the menu
                            this.element.trigger( "focus", [ true ] );

                            // If the active item is on the top level, let it stay active.
                            // Otherwise, blur the active item since it is no longer visible.
                            if ( this.active && this.active.parents( ".ui-menu" ).length === 1 ) {
                                clearTimeout( this.timer );
                            }
                        }
                    }
                },
                "mouseenter .ui-menu-item": "_activateItem",
                // "mousemove .ui-menu-item": "_activateItem", //Function active current submenu when move to other parent item.
                mouseleave: "collapseAll",
                "mouseleave .ui-menu": "collapseAll",
                focus: function( event, keepActiveItem ) {

                    // If there's already an active item, keep it active
                    // If not, activate the first item
                    var item = this.active || this._menuItems().first();

                    if ( !keepActiveItem ) {
                        this.focus( event, item );
                    }
                },
                blur: function( event ) {
                    this._delay( function() {
                        var notContained = !$.contains(
                            this.element[ 0 ],
                            $.ui.safeActiveElement( this.document[ 0 ] )
                        );
                        if ( notContained ) {
                            this.collapseAll( event );
                        }
                    } );
                },
                keydown: "_keydown"
            } );

            this.refresh();

            // Clicks outside of a menu collapse any open menus
            this._on( this.document, {
                click: function( event ) {
                    if ( this._closeOnDocumentClick( event ) ) {
                        this.collapseAll( event, true );
                    }

                    // Reset the mouseHandled flag
                    this.mouseHandled = false;
                }
            } );
        }
    };

    return function (targetWidget) {
        $.widget('ui.menu', targetWidget, menuWidgetMixin);
        return $.mage.modal;
    };
});
