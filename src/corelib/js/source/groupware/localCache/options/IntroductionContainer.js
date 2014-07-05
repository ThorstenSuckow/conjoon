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

Ext.namespace('com.conjoon.groupware.localCache.options');

/**
 * IntroductionContainer showing common information about Local Cache.
 *
 * @class com.conjoon.groupware.localCache.options.IntroductionContainer
 * @extends Ext.Container
 */
com.conjoon.groupware.localCache.options.IntroductionContainer = Ext.extend(Ext.Container, {

    /**
     * @cfg {com.conjoon.groupware.localCache.options.SettingsContainer}
     * settingsContainer The settingsContainer that holds this card as one of
     * it child components.
     */
    settingsContainer : null,

    /**
     * @type {com.conjoon.groupware.localCache.options.ui.DefaultIntroductionContainerUi}
     * The ui that manges this container. Defaults to
     * {com.conjoon.groupware.localCache.options.ui.DefaultIntroductionContainerUi}
     */
    ui : null,

    /**
     * @type {com.conjoon.groupware.util.FormIntro} formIntro The formIntro
     * for this container
     */
    formIntro : null,

    /**
     * @type {Ext.BoxComponent} availableComponent
     * The component which indicates which type of application cache is
     * available.
     */
    availableComponent : null,

// -------- Ext.Window

    /**
     * Inits this component.
     *
     */
    initComponent : function()
    {
        if (!this.ui) {
            this.ui = new com.conjoon.groupware.localCache.options.ui
                          .DefaultIntroductionContainerUi();
        }

        this.ui.init(this);

        com.conjoon.groupware.localCache.options.IntroductionContainer
        .superclass.initComponent.call(this);
    },

// -------- api

    /**
     * Returns the form intro for this container.
     *
     * @return {com.conjoon.groupware.util.FormIntro}
     */
    getFormIntro : function()
    {
        if (!this.formIntro) {
            this.formIntro = this.ui.buildFormIntro();
        }

        return this.formIntro;
    },

    /**
     * Returns the element that shows the information that Local Cache is
     * available on the client.
     *
     * @return {Ext.BoxComponent}
     */
    getAvailableComponent : function()
    {
        if (!this.availableComponent) {

            var type = com.conjoon.cudgets.localCache.Api.getCacheType();

            this.availableComponent = this.ui.buildAvailableComponent(type);
        }

        return this.availableComponent;
    }
});

