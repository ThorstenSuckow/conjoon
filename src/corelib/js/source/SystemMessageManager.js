/**
 * conjoon
 * (c) 2007-2015 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

Ext.namespace('com.conjoon');

/**
 * This class manages the rendering of message dialogs (Ext.MessageBox) based on the
 * device conjoon is used with.
 * The context for this manager should be read out of the registry
 * {@com.conjoon.util.Registry} and set with "setContext" upon application startup.
 *
 * @class com.conjoon.SystemMessageManager
 * @singleton
 */
com.conjoon.SystemMessageManager = function() {

    var _context = 'default';

    var _initDone = false;

    var _init = function()
    {
        var dlg = Ext.MessageBox.getDialog();
        dlg.on('hide', function(){
            dlg.el.removeClass([
                'com-conjoon-msgbox-warning',
                'com-conjoon-msgbox-prompt',
                'com-conjoon-msgbox-question',
                'com-conjoon-msgbox-info',
                'com-conjoon-msgbox-error',
                'com-conjoon-msgbox-critical',
                'com-conjoon-msgbox-wait'
            ]);
        });

        Ext.MessageBox.CRITICAL         = 'ext-mb-error';
        Ext.MessageBox.MISSING_RESPONSE = 'ext-mb-error';

        // sets the default to com-conjoon-msgbox-error if no additional class was
        // specified, and adjusts the class of the used progressbar to the cls as
        // defined by the conjoon project
        dlg.on('show', function(){
            if (dlg.el.dom.className.indexOf('com-conjoon-msgbox') == -1) {
                dlg.el.addClass('com-conjoon-msgbox-error');
            }

            var el = Ext.DomQuery.selectNode('div[class*=x-progress-wrap]', dlg.el.dom);
            if (el) {
                Ext.fly(el).addClass('com-conjoon-groupware-ProgressBar');
            }
        });

        // this will recompute the position on the screen based on the heigth/width
        // properties as specified in the css
        if (_context != 'iphone') {
            dlg.on('show', function(){
                var xy = dlg.el.getAlignToXY(document.body, 'c-c');
                var pos = dlg.el.translatePoints(xy[0], xy[1]);

                dlg.el.setLeftTop(pos.left, pos.top);
            });
        } else {
            // we will set a default height for the iphone
            // to 200 px for each Ext.MessageBox
            dlg.height = 200;
        }
    };

    return {



        /**
         * Sets the application context for the SystemMessageManager.
         * This is usually called upon application startup and only once during
         * application lifetime.
         *
         * @param {String} context
         */
        setContext : function(context)
        {
            if (!_initDone) {
                _init();
                _initDone = true;
            }

            _context = context;
        },

        /**
         * Shows a prompt dialog.
         *
         *
         */
        prompt : function(message, options)
        {
            var msg = Ext.MessageBox;

            var c = {};

            Ext.apply(c, message);

            c.msg = c.text;
            delete c.text;

            Ext.apply(c, {
                prompt : true,
                buttons : msg.OKCANCEL,
                icon    : msg.QUESTION,
                cls     :'com-conjoon-msgbox-prompt',
                width   : 375
            });

            Ext.apply(c, options);
            this.show(c);
        },

        /**
         * Shows a dialog with an progress bar.
         * Update the progress bar's state via "updateProgress()".
         *
         * @param {com.conjoon.SystemMessage} message
         * @param {Object} options
         */
        progress : function(message, options)
        {
            var msg = Ext.MessageBox;

            var c = {};

            Ext.apply(c, message);

            c.msg = c.text;
            delete c.text;

            Ext.apply(c, {
                buttons   : false,
                cls       : 'com-conjoon-msgbox-wait',
                progress  : true,
                draggable : false,
                progress  : true,
                closable  : false,
                minWidth  : 300
            });

            Ext.apply(c, options);
            this.show(c);
        },

        /**
         * Updates the progress bar if it is currently shown and of type
         * "progress".
         *
         * @param {Number} value
         * @param {String} progressText
         * @param {String} msg optional, if provided will override the
         * dialogs message text
         */
        updateProgress : function(value, progressText, msg)
        {
            Ext.MessageBox.updateProgress(value, progressText, msg);
        },

        /**
         * Shows a dialog with an infinite loading progress bar.
         *
         * @param {com.conjoon.SystemMessage} message
         * @param {Object} options
         */
        wait : function(message, options)
        {
            var msg = Ext.MessageBox;

            var c = {};

            Ext.apply(c, message);

            c.msg = c.text;
            delete c.text;

            Ext.apply(c, {
                buttons   : false,
                cls       : 'com-conjoon-msgbox-wait',
                wait      : true,
                draggable : false,
                progress  : true,
                closable  : false,
                width     : 300
            });

            Ext.apply(c, options);
            this.show(c);
        },

        /**
         * Shows a confirm dialog.
         *
         * @param {com.conjoon.SystemMessage} message
         * @param {Object} options
         */
        confirm : function(message, options)
        {
            var msg = Ext.MessageBox;

            var c = {};

            Ext.apply(c, message);

            c.msg = c.text;
            delete c.text;

            Ext.apply(c, {
                buttons : msg.YESNO,
                icon    : msg.QUESTION,
                cls     : 'com-conjoon-msgbox-question',
                width   : 400
            });

            Ext.apply(c, options);
            this.show(c);
        },

        /**
         * Hides any open dialog.
         */
        hide : function()
        {
            Ext.MessageBox.hide();
        },

        /**
         * Shows a dialog representing an information.
         *
         * @param {com.conjoon.SystemMessage} message
         * @param {Object} options
         */
        info : function(message, options)
        {
            var msg = Ext.MessageBox;

            var c = {};

            Ext.apply(c, message);

            c.msg = c.text;
            delete c.text;

            Ext.apply(c, {
                buttons : msg.OK,
                icon    : msg.INFO,
                cls     : 'com-conjoon-msgbox-info',
                width   : 375
            });

            Ext.apply(c, options);
            this.show(c);
        },

        /**
         * Shows a dialog indicating a warning
         *
         * @param message
         * @param options
         */
        warn : function(message, options) {

            var msg = Ext.MessageBox;

            var c = {};

            Ext.apply(c, message);

            c.msg = c.text;
            delete c.text;

            Ext.apply(c, {
                buttons : msg.OK,
                icon    : msg.WARNING,
                cls     : 'com-conjoon-msgbox-warning',
                width   : 375
            });

            Ext.apply(c, options);
            this.show(c);
        },

        /**
         * Shows a dialog indicating an error happened.
         *
         * @param {com.conjoon.SystemMessage} message
         * @param {Object} options
         */
        error : function(message, options)
        {
            var msg = Ext.MessageBox;

            var c = {};

            Ext.apply(c, message);

            c.msg = c.text;
            delete c.text;

            Ext.apply(c, {
                buttons : msg.OK,
                icon    : msg.ERROR,
                cls     : 'com-conjoon-msgbox-error',
                width   : 375
            });

            Ext.apply(c, options);
            this.show(c);
        },

        /**
         * Shows a system message using {Ext.Msg} taking the
         * context of the SystemMessageManager into account.
         *
         * @param {Object} config A configuration object to pass to
         * Ext.Msg.show()
         */
        show : function(config)
        {
            switch (_context) {
                case 'iphone':
                    Ext.apply(config, {
                        animEl : document
                    });
                break;

                default:
                break;
            }

            Ext.Msg.show(config);
        }
    };
}();

Ext.namespace('conjoon');
conjoon.SystemMessage = com.conjoon.SystemMessageManager;
