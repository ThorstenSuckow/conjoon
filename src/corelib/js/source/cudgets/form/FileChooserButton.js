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
    isMultipleFilesSupported : Ext.emptyFn

});