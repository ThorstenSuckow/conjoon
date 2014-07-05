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

Ext.namespace('com.conjoon.cudgets.form');

/**
 * This class represents a button which opens up any dialog to choose a file
 * from either the local file system or any other datastorage providing
 * data representing a file.
 * Its an abstract class and therefore has to be implemented according to the
 * type the chosen file represents.
 * Components which rely on fileselection must consider whether this
 * FileChooserButton allows for selecting multiple files (see
 * "isMultipleFilesSupported()"). If multiple files are not supported and the
 * "fileselected" event gets fired by this component, a previous selected file
 * by this button must be invalidated by the listening components and replaced
 * with the newly chosen files.
 * If, however, multiple file selection is supported by a sublass of
 * FileChooserButton, the "fileselected" event triggered means that the newly
 * choosen file may be added to an existing list of files.
 *
 *
 * @class com.conjoon.cudgets.form.FileChooserButton
 * @extends Ext.BoxComponent
 *
 * @abstract
 */
com.conjoon.cudgets.form.FileChooserButton = function(config) {

    var config = config || {};

    Ext.apply(this, config);

    this.addEvents(
        /**
         * Fired whenever a file was choosen using this button. Components
         * listeing to this event must consider the return value of
         * "isMultipleFilesSupported()", as it will tell if the selected file
         * may be added to a list of existing files already chosen by this
         * button, or if any selected file must be replaced with the newly
         * selected file.
         * @event fileselected
         * @param this
         * @param {com.conjoon.cudgets.data.FileRecord}
         * @param {Boolean} isMultipleFilesSupported
         * @param {String} id Some string to identify the selected file with its
         * input file
         */
        'fileselected'
    );

    com.conjoon.cudgets.form.FileChooserButton.superclass.constructor.call(this);
};

Ext.extend(com.conjoon.cudgets.form.FileChooserButton, Ext.BoxComponent, {

    /**
     * Tells whether this button supports selecting multiple files at once.
     *
     * @return {Boolean} true if this button supports selecting multiple
     * files at once, otherwise false.
     *
     * @abstract
     */
    isMultipleFilesSupported : Ext.emptyFn,

    /**
     * Tells whether this button represents selection of local or remote files.
     *
     * @return {Boolean} true if this button supports local file selection,
     * otherwise false.
     *
     * @abstract
     */
    isLocalFileSelectionSupported : Ext.emptyFn,

    /**
     * Resets the button so that it does not reference any selected
     * files anymore.
     */
    reset : Ext.emptyFn,

    /**
     * Creates an instance of {com.conjoon.cudgets.data.Upload} so that
     * remote operations related to moving/uploading using the referenced
     * file(s) are possible.
     *
     * @param {Array} an Array of com.conjoon.cudgets.data.FileRecord
     *
     * @return {com.conjoon.cudgets.data.Upload}
     */
    createUpload : Ext.emptyFn,

    /**
     * Returns either com.conjoon.cudgets.form.FileChooserButton.MODE_LOCAL
     * or com.conjoon.cudgets.form.FileChooserButton.MODE_LOCAL based
     * on the return value of isLocalFileSelectionSupported()
     *
     * @return {Mixed}
     */
    getSupportedSelectionType : function()
    {
        return this.isLocalFileSelectionSupported()
               ? com.conjoon.cudgets.form.FileChooserButton.MODE_LOCAL
               : com.conjoon.cudgets.form.FileChooserButton.MODE_REMOTE;
    }

});

com.conjoon.cudgets.form.FileChooserButton.MODE_LOCAL = 1;
com.conjoon.cudgets.form.FileChooserButton.MODE_REMOTE = 2;