/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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

Ext.namespace('com.conjoon.groupware.email.options');

/**
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 *
 * @class com.conjoon.groupware.email.options.ReadingOptionsCard
 * @extends com.conjoon.cudgets.SettingsCard
 */
com.conjoon.groupware.email.options.ReadingOptionsCard = Ext.extend(com.conjoon.cudgets.settings.Card, {

    /**
     * @param {@String} RENDER_HTML
     */
    RENDER_HTML : 'html',

    /**
     * @param {@String} RENDER_PLAIN
     */
    RENDER_PLAIN : 'plain',

    /**
     * @cfg {com.conjoon.cudgets.SettingsContainer} settingsContainer The
     * settingsContainer that controls this card.
     */
    settingsContainer : null,

    /**
     * @type {Ext.form.Checkbox} externalResourcesField field for specifying if
     * external resources are allowed
     */
    externalResourcesAllowedField : null,

    /**
     * @type {Ext.form.Radio} renderAsPlainField field for specifying if content should
     * be rendered as plain text
     * behavior.
     */
    renderAsPlainField : null,

    /**
     * @type {Ext.form.Radio} renderAsHtmlField field for specifying if content should
     * be rendered as plain text
     * behavior.
     */
    renderAsHtmlField : null,


    initComponent : function()
    {
        var me = this;

        Ext.apply(this, {
            baseCls    : 'x-small-editor',
            defaults   : {
                labelStyle : 'font-size:11px',
                anchor     : '100%'
            },
            bodyStyle  : 'margin:10px 0 20px 0px;padding:10px;background:none',
            title : com.conjoon.Gettext.gettext("Format settings"),
            items : [
                new Ext.form.FieldSet({
                    defaults : {
                        labelStyle : 'font-size:11px',
                        anchor     : '100%',
                        hideLabel : true
                    },
                    title      : com.conjoon.Gettext.gettext("Preferred Content Type"),
                    labelAlign : 'top',
                    margins : '0 0 15 0',
                    items      : [
                        new Ext.form.RadioGroup({
                            listeners : {
                                change : function(radioGroup, selectedField) {
                                    me.onPreferredContentTypeChange(selectedField.inputValue);
                                }
                            },
                            items : [
                                me.getRenderAsPlainField(),
                                me.getRenderAsHtmlField()
                            ]
                    })]
                }),
                new Ext.form.FieldSet({
                    defaults : {
                        labelStyle : 'font-size:11px',
                        anchor     : '100%',
                        hideLabel  : true
                    },
                    title      : com.conjoon.Gettext.gettext("External Resources"),
                    labelAlign : 'top',
                    items      : [
                        me.getExternalResourcesAllowedField()
                    ]
                })
            ]
        });

        com.conjoon.groupware.email.options.ReadingOptionsCard.superclass.initComponent.call(this);
    },


    /**
     * Returns the checkbox for specifying if external resources are allowed
     *
     * @return {Ext.form.Checkbox}
     */
    getExternalResourcesAllowedField : function() {
        var me = this;

        if (!me.externalResourcesAllowedField) {
            me.externalResourcesAllowedField = new Ext.form.Checkbox({
                name : 'isExternalResourcesAllowed',
                boxLabel : 'allow external resources',
                inputValue : true
            });
        }

        return me.externalResourcesAllowedField;
    },

    /**
     * Returns the radio field for specifying if content should be rendered plain
     *
     * @return {Ext.form.Radio}
     */
    getRenderAsPlainField : function() {
        var me = this;

        if (!me.renderAsPlainField) {
            me.renderAsPlainField = new Ext.form.Radio({
                name : 'render_as',
                boxLabel : 'render as plain text',
                inputValue : 'plain'
            });
        }

        return me.renderAsPlainField;
    },

    /**
     * Returns the radio field for specifying if content should be rendered as html
     *
     * @return {Ext.form.Radio}
     */
    getRenderAsHtmlField : function() {
        var me = this;

        if (!me.renderAsHtmlField) {
            me.renderAsHtmlField = new Ext.form.Radio({
                name : 'render_as',
                boxLabel : 'render as html',
                inputValue : 'html'
            });
        }

        return me.renderAsHtmlField;
    },

    /**
     * Listener for the change event of selecting preferred content type.
     *
     * @param record
     *
     * @throws exception if the passed string is unknown
     */
    onPreferredContentTypeChange : function(contentType) {
        var me = this;

        if (contentType != me.RENDER_HTML && contentType != me.RENDER_PLAIN) {
            throw("unknown preferred content type \"" + contentType + "\"");
        }

        me.getExternalResourcesAllowedField().setDisabled(contentType == me.RENDER_PLAIN);

    },

    /**
     * @inheritdoc
     */
    setRecord : function(record)
    {
        var me = this;

        // do not trigger startedit!
        me.getRenderAsPlainField().suspendEvents();
        me.getRenderAsHtmlField().suspendEvents();
        me.getExternalResourcesAllowedField().suspendEvents();

        if (record.get('preferredFormat').toLowerCase() ==
            me.RENDER_PLAIN.toLowerCase()) {
            me.getRenderAsPlainField().setValue(true);
            me.getExternalResourcesAllowedField().setDisabled(true);
        } else {
            me.getRenderAsHtmlField().setValue(true);
        }

        if (!me.getExternalResourcesAllowedField().disabled) {
            me.getExternalResourcesAllowedField().setValue(record.get('allowExternals'));
        }

        me.getRenderAsPlainField().resumeEvents();
        me.getRenderAsHtmlField().resumeEvents();
        me.getExternalResourcesAllowedField().resumeEvents();
    },

    /**
     * @inheritdoc
     */
    writeRecord : function(record)
    {
        var me = this,
            preferredFormat =  me.getRenderAsPlainField().getValue()
                               ? me.RENDER_PLAIN.toLowerCase()
                               : me.RENDER_HTML.toLowerCase(),
            allowExternals = !me.getExternalResourcesAllowedField().disabled
                             ? me.getExternalResourcesAllowedField().getValue()
                             : false;

        record.set('preferredFormat', preferredFormat);
        record.set('allowExternals', allowExternals);
    }



});
