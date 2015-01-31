/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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

Ext.namespace('com.conjoon.cudgets.data');

/**
 * This class represents an HTML upload.
 *
 * @class com.conjoon.cudgets.data.HtmlUpload
 * @extends com.conjoon.cudgets.data.Upload
 */
com.conjoon.cudgets.data.HtmlUpload = function(config) {

    com.conjoon.cudgets.data.HtmlUpload.superclass.constructor.call(
        this, config
    );

    if (config && config.fileRecords) {
        this.files = config.fileRecords;
    }

};

Ext.extend(com.conjoon.cudgets.data.HtmlUpload, com.conjoon.cudgets.data.Upload, {

    /**
     * @cfg {Array} fileRecords The files for which an upload was started
     */
    fileRecords : null,

    /**
     * @cfg {HtmlElement} form The form which holds the input elements with
     * type "file". Needed to properly post the upload to an IFRAME.
     */
    form : null,

    /**
     * @cfg {HtmlElement} iframe The iframe created for the upload process.
     */
    iframe : null,


// -------- com.conjoon.cudgets.data.Upload

    /**
     * Method should be implented for cleaning up remainings in the dom etc
     * when an instance is not usable anymore.
     *
     * @protected
     */
    cleanupImpl : function()
    {
        Ext.EventManager.un(this.iframe, 'load', this.onIframeLoadCallback, this);

        Ext.removeNode.defer(100, this, [this.iframe]);
    },

    /**
     * Returns true if arg represents an erroneous response after invoking
     * an upload, otherwise false.
     * The response has to send back the response property "success" as false
     * and or the "error" property as not null to indicate an erroneous
     * response is available.
     *
     * @return {Boolean}
     *
     * @protected
     */
    isErrorImpl : function(arg)
    {
        var dec;

        try {
            dec = Ext.decode(arg.response);

            if (dec.success !== true || dec.error != null) {
                return true;
            }
        } catch (e) {
            return true;
        }

        return false;
    },

    /**
     * Returns true if arg represents a successful response after invoking
     * an upload, otherwise false.
     *
     * @return {Boolean}
     *
     * @protected
     */
    isSuccessImpl : function(arg)
    {
        return this.isErrorImpl(arg) === false;
    },

    /**
     * Concrete implementation of cancelling the upload.
     * This method will be called by the Api.
     *
     * @return {Boolean} true if the upload was cancelled, otherwise false
     *
     * @protected
     */
    cancelImpl : function()
    {
        this.cleanup();
        return true;
    },

    /**
     * Concrete implementation of starting the upload.
     * This method will be called by the Api.
     *
     * @return {Boolean} true if the upload was invoked, otherwise false
     *
     * @protected
     */
    startImpl : function()
    {
        if (Ext.isGecko) {
            if (!com.conjoon.cudgets.data.HtmlUpload._tmpFrame) {
                var tmpiframe = document.createElement('iframe');
                tmpiframe.style.cssText = 'width:1px;height:1px;display:none';
                com.conjoon.cudgets.data.HtmlUpload._tmpFrame = tmpiframe;
            }
        }


        // build up the frame here
        var id       = Ext.id(),
            doc      = document,
            frame    = doc.createElement('iframe'),
            form     = this.form,
            encoding = 'multipart/form-data';

        frame.id            = id;
        frame.name          = id;
        frame.style.cssText = 'width:1px;height:1px;display:none';

        doc.body.appendChild(frame);

        Ext.fly(frame).set({
           src : Ext.SSL_SECURE_URL
        });

        if(Ext.isIE){
           document.frames[id].name = id;
        }

        Ext.fly(form).set({
            target   : id,
            action   : this.url
        });

        this.iframe = frame;

        Ext.EventManager.on(this.iframe, 'load', this.onIframeLoadCallback, this);

        form.submit();

        if (Ext.isGecko) {
            document.body.appendChild(com.conjoon.cudgets.data.HtmlUpload._tmpFrame);
            document.body.removeChild(com.conjoon.cudgets.data.HtmlUpload._tmpFrame);
        }

        return true;
    },

    /**
     * Tells whether the progress of this upload can be tracked.
     *
     * @return {Boolean} return true if the progress of the upload can be
     * determined while uploading, otherwise false.
     */
    isProgressAvailable : function()
    {
        return false;
    },

    /**
     * Callback for the load event of the iframe.
     *
     */
    onIframeLoadCallback : function()
    {
        // the callback tries to interprete the html response
        // from the frame and immediately removes it after response
        // has been interpreted
       var doc, responseText;

        try {
            doc = this.iframe.contentWindow.document
                  || this.iframe.contentDocument
                  || WINDOW.frames[id].document;

            if (doc && doc.body) {
                responseText = doc.body.innerHTML;
            }
        } catch(e) {}

        this.onFinish({response : responseText});
    }

});

// Mozilla hack, needed to prevent hourglass in tab
com.conjoon.cudgets.data.HtmlUpload._tmpFrame = null;