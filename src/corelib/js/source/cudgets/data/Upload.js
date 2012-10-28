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

Ext.namespace('com.conjoon.cudgets.data');

/**
 * This class represents an upload.
 * Its an abstract class and therefore has to be implemented according to the
 * type the upload represents (Flash, HTML-POST etc.).
 *
 * @class com.conjoon.cudgets.data.Upload
 * @extends Ext.util.Observable
 *
 * @abstract
 */
com.conjoon.cudgets.data.Upload = function(config) {

    var config = config || {};

    Ext.apply(this, config);

    this.addEvents(
        /**
         * Fired before the upload starts. Return false
         * to cancel this event.
         * @event request
         * @param this
         * @param {Array} files
         */
        'beforerequest',
        /**
         * Fired when the upload has been started.
         * @event request
         * @param this
         * @param {Array} files
         */
        'request',
        /**
         * Fired when the upload  has been cancelled.
         * @event request
         * @param this
         * @param {Array} files
         */
        'cancel',
        /**
         * Fired when the upload has failed.
         * @event request
         * @param this
         * @param {Array} files
         * @param {Object} arg An object with additional information.
         * At least the property "responseText" must be available, holding the
         * undecoded response from the process.
         */
        'failure',
        /**
         * Fired when the upload has finished successfully.
         * @event download
         * @param this
         * @param {Array} files
         * @param {Object} arg An object with additional information.
         * At least the property "responseText" must be available, holding the
         * undecoded response from the process.
         */
        'success',
        /**
         * Fired when the progress of the upload changes. This event
         * will only fire if "isProgressAvailable" returns true.
         * Subclasses should properly implement this event.
         * @event progress
         * @param this
         * @param {Array} files
         * @param {Number} currentBytes
         * @param {Number} totalBytes
         */
        'progress'
    );

    this.files = [];

    com.conjoon.cudgets.data.Upload.superclass.constructor.call(this);
};


Ext.extend(com.conjoon.cudgets.data.Upload, Ext.util.Observable, {

    /**
     * @cfg {String} url The url to upload the file(s) to
     */
    url : null,

    /**
     * @type {Array} an array of files. Building array is up to subclasses
     * as long as this property is treated as an array.
     */
    files : null,

    /**
     * @type {Boolean} isUploadStarted True if the upload was started, otherwise
     * false. Api reserved. Check this value in your subclasses if you want to
     * determine whether the upload was already started. If the upload started,
     * an instance of this class should not be reused.
     * @protected
     */
    isUploadStarted : false,

    /**
     * Invokes the upload of the file(s) by calling "startImpl()" which has
     * to be overriden in subclasses. startImpl will only be called if the
     * beforerequest listeners do not return false.
     * The method will do nothing if the "files" array is empty
     *
     * @return {Boolean}
     */
    start : function()
    {
        if (!this.isUploadStarted && this.files.length > 0
            && this.fireEvent('beforerequest', this, this.files) !== false) {
            this.isUploadStarted = this.startImpl();

            if (this.isUploadStarted) {
                this.fireEvent('request', this, this.files)
                return true;
            }
        }

        return false;
    },

    /**
     * Cancels the upload.
     * Fires the cancel event if cancelling the upload was successfull as returned
     * by cancelImpl.
     *
     * Calls cancelImpl which has to be implemented by subclasses.
     */
    cancel : function()
    {
        if (this.isUploadStarted && this.files.length > 0) {
            if (this.cancelImpl()) {
                this.fireEvent('cancel', this, this.files);
                this.cleanup();
            }
        }
    },

    /**
     * Fires the progress event by setting the progress of this upload.
     * Its up to the subclasses to properly call this method.
     *
     * @param {Number} currentBytes
     * @param {Number} totalBytes
     */
    setProgress : function()
    {
        if (!this.isUploadStarted || !this.isProgressAvailable()) {
            return;
        }

        this.fireEvent('progress', this, this.files, currentBytes, totalBytes);
    },

    /**
     * This method should be called when it can be safely determined that the
     * request finished. The method will check whether the the passed argument
     * denotes an erroneous or successfull download by callling the methods
     * "isErrorImpl()" and "isSuccessImpl()", based on its return value either
     * the "failure" or "success" event will be fired. If uploading was
     * successfull, the method "cleanup()" will be called to properly shut
     * this instance down.
     *
     *
     * @param {Object} arg
     */
    onFinish : function(arg)
    {
        if (!this.isUploadStarted) {
            return;
        }

        if (this.isErrorImpl(arg)) {
            this.fireEvent('failure', this, this.files, arg);
            this.cleanup();
            return;
        }

        if (this.isSuccessImpl(arg)) {
            this.fireEvent('success', this, this.files, arg);
            this.cleanup();
            return;
        }

        // if neither success or error, trigger error in any case
        this.fireEvent('failure', this, this.files, arg);
    },

    /**
     * Delegates to cancelImpl. Method is only here to be able to
     * manually invoke the cleanup process.
     *
     */
    cleanup : function()
    {
        this.cleanupImpl();
    },

    /**
     * Method should be implented for cleaning up remainings in the dom etc
     * when an instance is not usable anymore.
     *
     * @abstract
     * @protected
     */
    cleanupImpl : Ext.emptyFn,

    /**
     * Returns true if arg represents an erroneous response after invoking
     * an upload, otherwise false.
     *
     * @return {Boolean}
     *
     * @abstract
     * @protected
     */
    isErrorImpl : Ext.emptyFn,

    /**
     * Returns true if arg represents a successful response after invoking
     * an upload, otherwise false.
     *
     * @return {Boolean}
     *
     * @abstract
     * @protected
     */
    isSuccessImpl : Ext.emptyFn,

    /**
     * Concrete implementation of cancelling the upload.
     * This method will be called by the Api.
     *
     * @return {Boolean} true if the upload was cancelled, otherwise false
     *
     * @abstract
     * @protected
     */
    cancelImpl : Ext.emptyFn,

    /**
     * Concrete implementation of starting the upload.
     * This method will be called by the Api.
     *
     * @return {Boolean} true if the upload was invoked, otherwise false
     *
     * @abstract
     * @protected
     */
    startImpl : Ext.emptyFn,

    /**
     * Tells whether the progress of this upload can be tracked.
     *
     * @return {Boolean} return true if the progress of the upload can be
     * determined while uploading, otherwise false.
     *
     * @abstract
     */
    isProgressAvailable : Ext.emptyFn

});