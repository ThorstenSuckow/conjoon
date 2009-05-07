/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
 * licensing@conjoon.org
 *
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

Ext.namespace('com.conjoon.groupware.util');

/**
 * This control takes care of managing an Ext.FlashComponent and renders it
 * on top of the page at the coordinates where it usually would be a child
 * component of the container it would have been added.
 * It registers various listeners to give the user the feeling a container
 * controls the Ext.FlashComponent, though this control takes care of sizing,
 * hiding and rendering the Ext.FlashComponent.
 * Its intended use is with Mozilla Firefox, since it's known to have some issues with
 * Flash-Movies that are deeply nested within the dom tree (hiding/showing in an
 * Ext powered environment would restart the movie or detach the Flash-object from
 * the DOM tree).
 * However, this control can also be used with other browser and its recommended
 * if the flashComponent is part of heavy DOM operations.
 *
 * @class com.conjoon.groupware.util.FlashControl
 * @singleton
 */
com.conjoon.groupware.util.FlashControl = function() {

    /**
     * @type {Ext.Panel} _panel The panel to which the Ext.Flashcomponent
     * would have been usually added
     */
    var _panel          = null;

    /**
     * @type {Ext.FlashComponent} _flashComponent The flash component that is controlled
     * by this class.
     */
    var _flashComponent = null;

    /**
     * Gets called by register() and attaches the listeners to the _panel
     * and its parent container to show/hide/render the flashComponent accordingly.
     *
     */
    var _installListeners = function() {

        // takes care of resizing and positioning the flashComponent
        _panel.on('afterlayout', function() {
            _flashComponent.setSize({
                height : _panel.body.getHeight(),
                width  : _panel.body.getWidth()
            });

            _flashComponent.setPosition(_panel.body.getX(), _panel.body.getY());

            if (!_flashComponent.rendered) {
                _flashComponent.render(document.body);
                _flashComponent.el.setStyle({position: 'absolute'});
            }
        });

        // attaches various listeners to the panels owner component
        _panel.on('render', function() {
            _panel.ownerCt.on('beforehide', function() {
                _flashComponent.hide();
            });

            _panel.ownerCt.on('beforecollapse', function() {
                _flashComponent.hide();
            });

            _panel.ownerCt.on('expand', function() {
                _flashComponent.show();
            });

            _panel.on('activate', function() {
                _flashComponent.show();
            });

            _panel.on('deactivate', function() {
                _flashComponent.hide();
            });
        });

    };


    return {

        /**
         * Any flashComponent that should be controlled by this class must be registered
         * along with its parent panel _before_ they get rendered.
         *
         *
         * @param {Ext.FlashComponent} flashComponent
         * @param {Ext.Panel} panel The panel to which the flashComponents layout properties
         * should be synchronized with
         */
        register : function(flashComponent, panel)
        {
            _panel          = panel;
            _flashComponent = flashComponent;

            _installListeners();
        }
    };


}();