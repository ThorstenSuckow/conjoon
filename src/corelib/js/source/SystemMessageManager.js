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
                'com-conjoon-msgbox-critical'
            ]);
        });

        Ext.MessageBox.CRITICAL         = 'ext-mb-error';
        Ext.MessageBox.MISSING_RESPONSE = 'ext-mb-error';

        // sets the default to com-conjoon-msgbox-error if no additional class was
        // specified
        dlg.on('show', function(){
            if (dlg.el.dom.className.indexOf('com-conjoon-msgbox') == -1) {
                dlg.el.addClass('com-conjoon-msgbox-error');
                Ext.MessageBox.setIcon(Ext.MessageBox.ERROR);
            }
        });

        if (_context == 'iphone') {
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