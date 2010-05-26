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

if (!Ext.Version == '3.2.1') {
    throw("Look up Ext.data.DataReader.realize() for com.conjoon.cudgets.data.FileSelectionMediator.onUploadSuccess()");
}

/**
 * Instances of this class mediate between FileChooserButtons and FilePanels.
 *
 * @class com.conjoon.cudgets.data.FileSelectionMediator
 * @extends Ext.util.Observable
 *
 * @abstract
 */
com.conjoon.cudgets.data.FileSelectionMediator = function(config) {

    var config = config || {};

    Ext.apply(this, config);

    this.fileChooser.on('fileselected', this.onFileSelected, this);

    this.filePanel.on('uploadcancel',    this.onFilePanelUploadCancel,    this);
    this.filePanel.on('downloadcancel',  this.onFilePanelDownloadCancel,  this);
    this.filePanel.on('downloadrequest', this.onFilePanelDownloadRequest, this);
    this.filePanel.on('recordremove',    this.onFilePanelRecordRemove,    this);

    this.addEvents(
        /**
         * @event cancel
         * @param {com.conjoon.cudgets.data.FileSelectionMediator} this
         * @param {Array} files
         */
        'cancel',
        /**
         * @event request
         * @param {com.conjoon.cudgets.data.FileSelectionMediator} this
         * @param {Array} files
         */
        'request',
        /**
         * @event success
         * @param {com.conjoon.cudgets.data.FileSelectionMediator} this
         * @param {Array} files
         * @param {Object} arg
         */
        'success',
        /**
         * @event failure
         * @param {com.conjoon.cudgets.data.FileSelectionMediator} this
         * @param {Array} files
         * @param {Object} arg
         */
        'failure'
    );

    com.conjoon.cudgets.data.FileSelectionMediator.superclass.constructor.call(this);
};


Ext.extend(com.conjoon.cudgets.data.FileSelectionMediator, Ext.util.Observable, {

    /**
     * @cfg {com.conjoon.cudgets.form.FileChooserButton} fileChooser
     */
    fileChooser : null,

    /**
     * @cfg {com.conjoon.cudgets.grid.FlePanel} filePanel
     */
    filePanel : null,

    /**
     * @cfg {com.conjoon.cudgets.grid.FilePanel} filePanel
     */
    files : null,

    /**
     * @cfg {Boolean} uploadOnFileSelected Whether or not the upload should
     * be triggered imemdiately after a single file has been selected, i.e.
     * the FileChooser's "fileselected" event fired. This should be set to
     * "true" if the FileChooserButton does not allow for selecting multiple
     * files at once.
     */
    uploadOnFileSelected : false,

    /**
     * @type {Array} managedFiles
     * An array of files managed by this mediator.
     */
    managedFiles : null,

    /**
     * Listens to the FileChooser's "fileselected" event.
     *
     * @param {com.conjoon.cudgets.form.FileChooserButton} fileChooserButton
     * @param {String} fileName
     * @param {Boolean} isMultipleFilesSupported
     * @param {Mixed} id
     */
    onFileSelected : function(fileChooserButton, fileName, isMultipleFilesSupported, id)
    {
        var fileRec    = null;
        var FileRecord = com.conjoon.cudgets.data.FileRecord;

        if (fileChooserButton.getSupportedSelectionType() ==
            com.conjoon.cudgets.form.FileChooserButton.MODE_LOCAL) {
            fileRec = new FileRecord({
                name     : fileName,
                location : FileRecord.LOCATION_LOCAL
            }, id);
        } else {
            fileRec = new FileRecord({
                name     : fileName,
                location : FileRecord.LOCATION_REMOTE
            }, id);
        }

        if (!this.managedFiles) {
            this.managedFiles = [];
        }

        var obj = {
            record : fileRec,
            upload : null
        };

        this.managedFiles.push(obj);
        this.filePanel.addFile(fileRec);

        if (this.uploadOnFileSelected === true) {
            var upload = fileChooserButton.createUpload([obj.record]);
            obj.upload = upload;

            upload.on('request', this.onUploadRequest, this);
            upload.on('success', this.onUploadSuccess, this);
            upload.on('failure', this.onUploadFailure, this);
            upload.on('cancel',  this.onUploadCancel, this);

            fileRec.set('state', FileRecord.STATE_UPLOADING);
            upload.start();
        }
    },

    /**
     * Returns true if there is currently a file being uploaded, i.e.
     * if there is any of teh files managed currently being uploaded.
     *
     * @return {Boolean}
     */
    isUploadPending : function()
    {
        var uploadState = com.conjoon.cudgets.data.FileRecord.STATE_UPLOADING,
            mf          = this.managedFiles;
        for (var i = 0, len = mf.length; i < len; i++) {
            if (mf[i].record.get('state') == uploadState) {
                return true;
            }
        }

        return false;
    },

    /**
     * Returns the managed files bis this mediator.
     *
     * @return {Array}
     */
    getManagedFiles : function()
    {
        if (!this.managedFiles) {
            this.managedFiles = [];
        }

        return this.managedFiles;
    },

    /**
     * Destroys all related resources with this mediator, except for
     * components registered via constructor.
     */
    destroy : function()
    {
        this.purgeListeners();

        var tmp = [];
        for (var i = 0, len = this.managedFiles.length; i < len; i++) {
            tmp[i] = this.managedFiles[i];
        }
        for (var i = 0, len = tmp.length; i < len; i++) {
            tmp[i].upload.cancel();
        }
    },

    /**
     * Removes the record from the list of managed files.
     *
     * @param {com.conjoon.cudgets.data.FileRecord} record
     */
    removeFromManagedFiles : function(record)
    {
        for (var a = 0, lena = this.managedFiles.length; a < lena; a++) {
            if (record.id == this.managedFiles[a].record.id) {
                this.managedFiles.splice(a, 1);
                return;
            }
        }
    },

    /**
     * Listener for the filePanel's "recordremove" event.
     *
     * @param {com.conjoon.cudgets.grid.FilePanel} filePanel
     * @param {Array} records
     */
    onFilePanelRecordRemove : function(filePanel, records)
    {
        for (var i = 0, len = records.length; i < len; i++) {
            this.removeFromManagedFiles(records[i]);
        }
    },

    /**
     * Listener for the attached filePanel's "uploadcancel" event.
     *
     * @param {com.conjoon.cudgets.grid.FilePanel} filePanel
     * @param {Array} records
     */
    onFilePanelUploadCancel : function(filePanel, records)
    {
        var id = "";
        for (var i = 0, len = records.length; i < len; i++) {
            id = records[i].id;
            for (var a = 0, lena = this.managedFiles.length; a < lena; a++) {
                if (id == this.managedFiles[a].record.id) {
                    this.managedFiles[a].upload.cancel();
                    break;
                }
            }
        }
    },

    /**
     * Listener for the file panel's download request event.
     *
     * @param {com.conjoon.cudgets.grid.FilePanel} filePanel
     * @param {com.conjoon.cudgets.data.FielRecord} record
     */
    onFilePanelDownloadRequest : function(filePanel, record)
    {
        com.conjoon.groupware.DownloadManager.downloadFile(
            record.id, record.get('key'), record.get('name')
        );
    },

    /**
     * Listener for the attached filePanel's "downloadcancel" event.
     *
     * @param {com.conjoon.cudgets.grid.FilePanel} filePanel
     * @param {Array} records
     */
    onFilePanelDownloadCancel : function(filePanel, records)
    {
        var DownloadManager = com.conjoon.groupware.DownloadManager,
            type = null, metaType = null,
            FileRecord = com.conjoon.cudgets.data.FileRecord;

        for (var i = 0, len = records.length; i < len; i++) {
            metaType = records[i].get('metaType');

            type = metaType === FileRecord.META_TYPE_FILE
                   ? DownloadManager.TYPE_FILE
                    : metaType === FileRecord.META_TYPE_EMAIL_ATTACHMENT
                      ? DownloadManager.TYPE_EMAIL_ATTACHMENT
                      : false;

            if (type === false) {
                continue;
            }
            DownloadManager.cancelDownloadForIdAndKey(
                records[i].id, records[i].get('key'), type
            );
        }
    },

// -------- Upload listener

    /**
     * Fired when the upload is initiated.
     * Resets the FileChooser by Default.
     *
     * @param {com.conjoon.cudgets.data.Upload} upload
     * @param {Array} files
     */
    onUploadRequest : function(upload, files)
    {
        this.fileChooser.reset();

        this.fireEvent('request', this, files);
    },

    /**
     * Fired when the upload was successfull.
     *
     * @param {com.conjoon.cudgets.data.Upload} upload
     * @param {Array} files
     * @param {Object} arg
     */
    onUploadSuccess : function(upload, files, arg)
    {
        var response      = Ext.decode(arg.response);
        var responseFiles = response.files,
            finFiles      = [],
            file          = null,
            lr            = com.conjoon.cudgets.data.FileRecord.LOCATION_REMOTE,
            data          = null,
            i, len;

        for (i = 0, len = responseFiles.length; i < len; i++) {
            file = responseFiles[i];
            file.state    = '';
            file.location = lr;
            finFiles[file.oldId] = file;
            delete finFiles[file.oldId].oldId;
        }


        for (i = 0, len = files.length; i < len; i++) {
            data = finFiles[files[i].id];

            files[i].phantom = false;
            files[i]._phid   = files[i].id;
            files[i].id      = data.id;
            Ext.apply(files[i].data, data);
            files[i].commit();
        }

        this.filePanel.getStore().reMap(files);

        this.fireEvent('success', this, files, arg);
    },

    /**
     * Fired when the upload failed.
     *
     * @param {com.conjoon.cudgets.data.Upload} upload
     * @param {Array} files
     * @param {Object} arg
     */
    onUploadFailure : function(upload, files, arg)
    {
        for (var i = 0, len = files.length; i < len; i++) {
            files[i].set('state', com.conjoon.cudgets.data.FileRecord.STATE_INVALID);
            files[i].set('location', '');
        }

        this.fireEvent('failure', this, files, arg);
    },

    /**
     * Fired when the upload was cancelled.
     *
     * @param {com.conjoon.cudgets.data.Upload} upload
     * @param {Array} files
     */
    onUploadCancel : function(upload, files)
    {
        this.filePanel.getStore().remove(files);
        for (var i = 0, len = files.length; i < len; i++) {
            this.removeFromManagedFiles(files[i]);
        }

        this.fireEvent('cancel', this, files);
    }

});