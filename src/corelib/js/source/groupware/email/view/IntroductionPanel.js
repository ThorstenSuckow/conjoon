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

Ext.namespace('com.conjoon.groupware.email.view');

/**
 * An introcution panel to be shown when the root folder of a account
 * is clicked. It presenst the user a list of tasks he can execute associated
 * to the email account(s) which is (are) represented by the currently selected
 * root folder.
 *
 * @class com.conjoon.groupware.email.view.IntroductionPanel
 * @extends Ext.BoxComponent
 */
com.conjoon.groupware.email.view.IntroductionPanel = Ext.extend(Ext.BoxComponent, {

    /**
     * @type {HtmlElement} headEl
     */
    headEl : null,

    /**
     * @type {HtmlElement} nameEl
     */
    nameEl :null,

    /**
     * @type {HtmlElement} accountContextEl
     */
    accountContextEl :null,

    /**
     * @type {HtmlElement} readEmailLink
     */
    readEmailLink : null,

    /**
     * @type {HtmlElement} emailListEl
     */
    emailListEl : null,

    /**
     * @type {HtmlElement} accountListEl
     */
    accountListEl : null,

    /**
     * @type {HtmlElement} miscListEl
     */
    miscListEl : null,

    /**
     * @type {HtmlElement} warningListEl
     */
    warningListEl : null,

    /**
     * @type {Ext.tree.TreeNode} lastClkNode Stores the node for which this
     * panel was shown.
     */
    lastClkNode : null,

    autoEl : {
        tag : 'div',
        cls : 'com-conjoon-groupware-email-view-IntroductionPanel',
        cn  : [{
            tag : 'div',
            cls : 'head',
            cn  : [{
                tag : 'div',
                cls : 'names',
                cn  : [{
                    tag  : 'span'
                }, {
                    tag : 'span'
                }]
            }, {
                tag : 'div',
                cn  : [{
                    tag  : 'span',
                    cls  : 'quest',
                    html : com.conjoon.Gettext.gettext("What would you like to do?")
                }]
            }]
        }, {
            tag : 'div',
            cls : 'list',
            cn  : [{
                tag : 'ul',
                cn  : [
                // EMAIL
                {
                    tag : 'li',
                    cls : 'optionlist',
                    cn  : [{
                        tag  : 'h2',
                        html : com.conjoon.Gettext.gettext("Email")
                    }, {
                        tag : 'ul',
                        cn  : [{
                            tag : 'li',
                            cls : 'reademail',
                            cn  : [{
                                tag  : 'a',
                                href : '#',
                                html : com.conjoon.Gettext.gettext("Read Emails")
                            }]
                        },{
                            tag : 'li',
                            cls : 'writeemail',
                            cn  : [{
                                tag     : 'a',
                                href    : '#',
                                onclick : 'com.conjoon.groupware.email.EmailEditorManager.createEditor()',
                                html    : com.conjoon.Gettext.gettext("Compose new Email")
                            }]
                        }]
                    }]
                },
                // ACCOUNT
                {
                    tag : 'li',
                    cls : 'optionlist',
                    cn  : [{
                        tag  : 'h2',
                        html : com.conjoon.Gettext.gettext("Accounts")
                    }, {
                        tag : 'ul',
                        cn  : [{
                            tag : 'li',
                            cls : 'accountnew',
                            cn  : [{
                                tag     : 'a',
                                href    : '#',
                                onclick : '(new com.conjoon.groupware.email.EmailAccountWizard()).show()',
                                html    : com.conjoon.Gettext.gettext("New Account")
                            }]
                        },{
                            tag : 'li',
                            cls : 'accountsettings',
                            cn  : [{
                                tag     : 'a',
                                href    : '#',
                                onclick : '(new com.conjoon.groupware.email.EmailAccountDialog()).show()',
                                html    : com.conjoon.Gettext.gettext("Account settings")
                            }]
                        }]
                    }]
                },
                // 	MISCELLANEOUS
                {
                    tag : 'li',
                    cls : 'optionlist',
                    cn  : [{
                        tag  : 'h2',
                        html : com.conjoon.Gettext.gettext("Miscellaneous")
                    }, {
                        tag : 'ul',
                        cn  : [{
                            tag : 'li',
                            cls : 'mailsearch',
                            cn  : [{
                                tag  : 'a',
                                href : '#',
                                html : com.conjoon.Gettext.gettext("Search Emails")
                            }]
                        },{
                            tag : 'li',
                            cls : 'filternew',
                            cn  : [{
                                tag  : 'a',
                                href : '#',
                                html : com.conjoon.Gettext.gettext("Create filter")
                            }]
                        }]
                    }]
                },
                // 	WARNING
                {
                    tag : 'li',
                    cls : 'optionlist',
                    cn  : [{
                        tag  : 'h2',
                        cls  : 'warning',
                        html : com.conjoon.Gettext.gettext("No Email account found")
                    }, {
                        tag : 'ul',
                        cn  : [{
                            tag : 'li',
                            cls : 'warning',
                            cn  : [{
                                tag     : 'a',
                                href    : '#',
                                onclick : '(new com.conjoon.groupware.email.EmailAccountWizard()).show()',
                                html    : com.conjoon.Gettext.gettext("There is currently no Email account configured. In order to send and retrieve emails, you need to add an Email account first. You can do so by clicking here.")
                            }]
                        }]
                    }]
                }]
            }]
        }]
    },

    afterRender : function()
    {
        com.conjoon.groupware.email.view.IntroductionPanel.superclass.afterRender.call(this);

        this.headEl           = this.el.dom.firstChild;
        this.nameEl           = this.headEl.firstChild.firstChild;
        this.accountContextEl = this.nameEl.nextSibling;

        this.emailListEl   = this.headEl.nextSibling.firstChild.firstChild;
        this.accountListEl = this.emailListEl.nextSibling;
        this.miscListEl    = this.accountListEl.nextSibling;
        this.warningListEl = this.miscListEl.nextSibling;

        this.showElements();

        this.readEmailLink = this.headEl.nextSibling.firstChild.firstChild.firstChild.nextSibling.firstChild.firstChild;
        this.readEmailLink.style.display = 'none';
        Ext.fly(this.readEmailLink).on('click', this._onReadEmailClick, this);

        this.nameEl.innerHTML = com.conjoon.groupware.Registry.get('/base/conjoon/name') +
                                " " +
                                com.conjoon.Gettext.gettext("Email");

        var store = com.conjoon.groupware.email.AccountStore.getInstance();
        this.mon(store, 'add',    this._onAccountStoreAdd, this);
        this.mon(store, 'remove', this._onAccountStoreRemove, this);
    },


    /**
     * Hides or shows elements in this component depending on the current state
     * of the com.conjoon.groupware.email.AccountStore.
     *
     */
    showElements : function()
    {
        var length = com.conjoon.groupware.email.AccountStore.getInstance().getRange().length;

        if (length > 0) {
            this.emailListEl.style.display   = '';
            this.accountListEl.style.display = '';
            this.miscListEl.style.display    = '';
            this.warningListEl.style.display = 'none';
        } else {
            this.emailListEl.style.display   = 'none';
            this.accountListEl.style.display = 'none';
            this.miscListEl.style.display    = 'none';
            this.warningListEl.style.display = '';
        }
    },

// -------- listeners

    /**
     * Called when a record in the email's account store gets added.
     *
     * @param {Ext.data.Store} store
     * @param {Array} records
     * @param {Number} index
     */
    _onAccountStoreAdd : function(store, records, index)
    {
        this.showElements();
    },

    /**
     * Called when a record in the email's account store gets removed.
     *
     * @param {Ext.data.Store} store
     * @param {Ext.data.Record} record
     * @param {Number} index
     *
     */
    _onAccountStoreRemove : function(store, record, index)
    {
        this.showElements();
    },

    /**
     * Destructor for this component.
     *
     */
    onDestroy : function()
    {
        Ext.fly(this.readEmailLink).un('click', this._onReadEmailClick, this);
    },

    /**
     * Called when the "read email link" was clicked.
     *
     */
    _onReadEmailClick : function()
    {
        if (!this.lastClkNode) {
            return;
        }

        this.lastClkNode.expand(false, false, function(parentNode){
            var inb = parentNode.findChild('type' , 'inbox');

            if (inb) {
                parentNode.getOwnerTree().getSelectionModel().select(inb);
            }

        });
    },

    /**
     * Called when a node selection changes in the tree panel representing
     * available folders.
     *
     * @param {Ext.tree.DefaultSelectionModel} selectionModel
     * @param {Ext.tree.TreeNode} node
     */
    _onNodeSelectionChange : function(selectionModel, node)
    {
        if (!node) {
            this.readEmailLink.style.display = 'none';
            this.lastClkNode = null;
            this.accountContextEl.innerHTML = '';
            return;
        }

        var attrType = node.attributes.type;

        if (attrType != 'accounts_root' && attrType != 'root') {
            this.lastClkNode = null;
            return;
        }

        this.lastClkNode = node;
        this.readEmailLink.style.display = '';
        this.accountContextEl.innerHTML = ' - ' +
                                          node.attributes.text;

    }

});