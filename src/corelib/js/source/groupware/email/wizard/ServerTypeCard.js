/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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

Ext.namespace('com.conjoon.groupware.email.wizard');

/**
 *
 *
 * @class com.conjoon.groupware.email.wizard.ServerTypeCard
 * @extends Ext.ux.Wiz.Card
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.groupware.email.wizard.ServerTypeCard = Ext.extend(Ext.ux.Wiz.Card, {

    radioPopField  : null,
    radioImapField : null,

    initComponent : function()
    {
        this.monitorValid = true;

        this.baseCls    = 'x-small-editor';
        this.labelWidth = 80;

        this.title = com.conjoon.Gettext.gettext("Server type");
        this.defaults = {
            labelStyle : 'width:80px;font-size:11px',
            anchor     : '100%'
        };

        this.radioPopField = new Ext.form.Radio({
            boxLabel   : com.conjoon.Gettext.gettext("POP"),
            name       : 'protocol',
            checked    : true,
            inputValue : 'POP3'
        });

        this.radioImapField = new Ext.form.Radio({
            boxLabel   : com.conjoon.Gettext.gettext("IMAP"),
            name       : 'protocol',
            inputValue : 'IMAP'
        });

        this.items = [
            new com.conjoon.groupware.util.FormIntro({
                style     : 'margin:10px 0 5px 0;',
                labelText : com.conjoon.Gettext.gettext("Server type"),
                text      : com.conjoon.Gettext.gettext("Specify the protocol used by the server this account works with. Please contact your email account's administrator if you are unsure which protocol to use.")
            }),
            this.radioPopField,
            this.radioImapField
        ];

        com.conjoon.groupware.email.wizard.ServerTypeCard.superclass.initComponent.call(this);
    }
});
