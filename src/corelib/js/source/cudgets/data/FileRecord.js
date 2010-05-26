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

Ext.namespace('com.conjoon.cudgets.data');

/**
 * A record representing meta informations for files. This can be used
 * directly anywhere where file operations are needed.
 *
 *
 * @class com.conjoon.cudgets.data.FileRecord
 * @extends Ex.data.Record
 */
com.conjoon.cudgets.data.FileRecord = Ext.data.Record.create([

    /**
     * @type {Mixed} id
     * The id of this file, if available.
     */
    {name : 'id', type : 'int'},

    /**
     * @type {Mixed} folderId
     * The id of thie folder the file resists in, if available.
     */
    {name : 'fodlerId', type : 'int'},

    /**
     * @type {Mixed} key
     * The key of this file, if available.
     */
    {name : 'key'},

    /**
     * @type {String} name
     * The name of this file, excluding path components.
     */
    {name : 'name', type : 'string'},

    /**
     * @type {String} metaType
     * The metaType of this file, can be any of
     * com.conjoon.cudgets.data.FileRecord.META_TYPE_FILE
     * com.conjoon.cudgets.data.FileRecord.META_TYPE_EMAIL_ATTACHMENT
     * Depending on the metaType, folderId may be empty.
     */
    {name : 'metaType', type : 'string'},

    /**
     * @type {String} mimeType
     * The mimeType of this file.
     */
    {name : 'mimeType', type : 'string'},

    /**
     * @type {String} state
     *
     * The state of this file. Can be anything of
     * null/undefined
     * com.conjoon.cudgets.data.FileRecord.STATE_UPLOADING
     * com.conjoon.cudgets.data.FileRecord.STATE_DOWNLOADING
     * com.conjoon.cudgets.data.FileRecord.STATE_INVALID
     */
    {name : 'state'},

    /**
     * @type {String} location
     *
     * The location of this file. Can be anything of
     * com.conjoon.cudgets.data.FileRecord.LOCATION_LOCAL
     * com.conjoon.cudgets.data.FileRecord.LOCATION_REMOTE
     */
    {name : 'location'}

]);

com.conjoon.cudgets.data.FileRecord.META_TYPE_FILE             = 'file';
com.conjoon.cudgets.data.FileRecord.META_TYPE_EMAIL_ATTACHMENT
    = 'emailAttachment';

com.conjoon.cudgets.data.FileRecord.STATE_INVALID     = -1;
com.conjoon.cudgets.data.FileRecord.STATE_UPLOADING   = 1;
com.conjoon.cudgets.data.FileRecord.STATE_DOWNLOADING = 2;

com.conjoon.cudgets.data.FileRecord.LOCATION_LOCAL  = 1;
com.conjoon.cudgets.data.FileRecord.LOCATION_REMOTE = 2;