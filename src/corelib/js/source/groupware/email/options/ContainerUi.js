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

Ext.namespace('com.conjoon.groupware.email.options');

/**
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 *
 * @class com.conjoon.groupware.email.options.ContainerUi
 * @extends com.conjoon.cudgets.ui.SettingsContainerUi
 */

com.conjoon.groupware.email.options.ContainerUi = Ext.extend(
    com.conjoon.cudgets.settings.ui.DefaultContainerUi, {

    emptyText : "",

    /**
     * Inits the layout of the container.
     * gets called from the initComponent's "initComponent()" method.
     *
     * @param {Ext.Container} container The container this ui will manage.
     */
    init : function(container)
    {
        if (this.container) {
            return;
        }

        com.conjoon.groupware.email.options.ContainerUi
        .superclass.init.call(this, container);
    },

    /**
     * Returns an array with {Ext.FormPanel}s used to edit the
     * currently selected record.
     *
     * @return {Array}
     */
    buildFormCards : function()
    {
        return [
            new com.conjoon.groupware.email.options.ReadingOptionsCard({
                settingsContainer    : this.container,
                enableStartEditEvent : true
            })
        ]
    },

    buildIntroductionCard : function()
    {
        return new Ext.BoxComponent({
            autoEl : {
                tag  : 'div',
                cn   : [{
                    tag  : 'div',
                    html : com.conjoon.Gettext.gettext("Email Options"),
                    cls  : 'headerLabel'
                },{
                    tag  : 'div',
                    cls  : 'com-conjoon-margin-t-10',
                    html : com.conjoon.Gettext.gettext("Please choose an option from the list to edit its settings.")
                }]
            }
        });
    },

    buildAddEntryButton : function() {
        return null;
    },

    buildRemoveEntryButton : function() {
        return null;
    }

});
