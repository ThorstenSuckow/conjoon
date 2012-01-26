/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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

Ext.namespace('com.conjoon.groupware.workbench');

/**
 *
 * @class com.conjoon.groupware.workbench.AboutDialog
 * @singleton
 */
com.conjoon.groupware.workbench.AboutDialog = function() {

    var _dialog = null;

    var _createDialog = function()
    {
        var version = com.conjoon.groupware.Registry.get('/base/conjoon/version');
        var name    = com.conjoon.groupware.Registry.get('/base/conjoon/name');
        var edition = com.conjoon.groupware.Registry.get('/base/conjoon/edition');

        return new Ext.Window({
            title     : com.conjoon.Gettext.gettext("About"),
            closable  : true,
            resizable : false,
            modal     : true,
            height    : 400,
            width     : 300,
            cls       : 'com-conjoon-groupware-workbench-AboutDialog',
            listeners : {
                destroy : function() {
                    _dialog = null;
                }
            },
            layout : 'fit',
            items  : [
                new Ext.BoxComponent({
                    autoEl : {
                        tag  : 'div',
                        html : '<div class="info"><span class="bold">'+name+"</span><br />"+edition+" <br />"+version +
                               '<div class="about">'+
                                 '(c) 2007-2010 <a href="http://www.conjoon.org" target="_blank">conjoon.org'+'</a><br />'+
                                 String.format(
                                    com.conjoon.Gettext.gettext("conjoon uses libraries which are intellectual property of their respective owners.<br />Fore more information, visit <a target='_blank' href='{0}'>{0}</a>."),
                                    'http://www.conjoon.org/projects'
                                 )+
                               '</div>'+
                               '</div>'

                    }
                })
            ]
        });



    };

    return {

        show : function()
        {
            if (!_dialog) {
                _dialog = _createDialog();
            }

            _dialog.show();
        }


    };


}();