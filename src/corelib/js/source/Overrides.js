/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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

/**
 * Provides common overwrite functionality for Ext components to match
 * behavior as wished.
 */

    Ext.MessageBox.CRITICAL = 'ext-mb-error';

    /**
     * @ext-request beta1 Can we have a onHide method in MessageBox so "handleHide"
     *                    gets called when the dialog gets hidden? Because I have
     *                    changing custom cls for each type of message box (i.e.
     *                    question, info etc. and the cls property gets appended to the
     *                    cls that was already created when the first call to show()
     *                    was made.
     */
    var dlg = Ext.MessageBox.getDialog();
    dlg.on('hide', function(){
        dlg.el.removeClass([
            'com-conjoon-msgbox-warning',
            'com-conjoon-msgbox-prompt',
            'com-conjoon-msgbox-question',
            'com-conjoon-msgbox-info',
            'com-conjoon-msgbox-error',
            'com-conjoon-msgbox-critical'
        ]);
    });

    dlg.on('show', function(){
        var xy = dlg.el.getAlignToXY(document.body, 'c-c');
        var pos = dlg.el.translatePoints(xy[0], xy[1]);

        dlg.el.setLeftTop(pos.left, pos.top);
    });

/**
 * Overwrites ext-adapter behavior for allowing queuing of AJAX requests.
 *
 */


Ext.lib.Ajax._queue = [];
Ext.lib.Ajax._activeRequests = 0;
Ext.lib.Ajax._concurrentRequests = Ext.isGecko ? 4 : 2;

Ext.lib.Ajax.abort=function(o, callback, isTimeout)
{
    if (this.isCallInProgress(o)) {
        o.conn.abort();
        window.clearInterval(this.poll[o.tId]);
        delete this.poll[o.tId];
        if (isTimeout) {
            delete this.timeout[o.tId];
        }

        //aborted may be called with no callback-parameter, so no loadexception
        //or else would be generated in handleTransactionResponse.
        if (!callback) {
            Ext.ux.util.MessageBus.publish('ext.lib.ajax.abort', {
                requestObject : o
            });
        }

        this.handleTransactionResponse(o, callback, true);

        return true;
    }
    else {

        for (var i = 0, max_i = this._queue.length; i < max_i; i++) {
            if (this._queue[i].o.tId == o.tId) {
                this._queue.splice(i, 1);
                break;
            }
        }

        return false;
    }
};

Ext.lib.Ajax.asyncRequest = function(method, uri, callback, postData)
{


    var o = this.getConnectionObject();

    if (!o) {
        return null;
    }
    else {

        this._queue.push({
           o : o,
           method: method,
           uri: uri,
           callback: callback,
           postData : postData
        });

        this._processQueue();

        return o;
    }
};

Ext.lib.Ajax._processQueue = function()
{
    var to = this._queue[0];

    if (to && this._activeRequests < this._concurrentRequests) {
        to = this._queue.shift();
        this._activeRequests++;
        this._asyncRequest(to.o, to.method, to.uri, to.callback, to.postData);
    }


};

Ext.lib.Ajax._asyncRequest = function(o, method, uri, callback, postData)
{
    o.conn.open(method, uri, true);

    if (this.useDefaultXhrHeader) {
        if (!this.defaultHeaders['X-Requested-With']) {
            this.initHeader('X-Requested-With', this.defaultXhrHeader, true);
        }
    }

    if(postData && this.useDefaultHeader){
        this.initHeader('Content-Type', this.defaultPostHeader);
    }

     if (this.hasDefaultHeaders || this.hasHeaders) {
        this.setHeader(o);
    }

    this.handleReadyState(o, callback);
    o.conn.send(postData || null);

};

Ext.lib.Ajax.releaseObject = function(o)
{
    o.conn = null;
    o = null;

    this._activeRequests--;
    this._processQueue();
};

Ext.lib.Ajax.handleTransactionResponse = function(o, callback, isAbort) {

    if (!callback) {
        this.releaseObject(o);
        return;
    }

    var httpStatus, responseObject;

    try {
        if (o.conn.status !== undefined && o.conn.status != 0) {
            httpStatus = o.conn.status;
        } else {
            httpStatus = 13030;
        }
    } catch(e) {
        httpStatus = 13030;
    }

    if (httpStatus >= 200 && httpStatus < 300) {
        responseObject = this.createResponseObject(o, callback.argument);
        if (callback.success) {
            if (!callback.scope) {
                callback.success(responseObject);
            } else {
                callback.success.apply(callback.scope, [responseObject]);
            }
        }
    } else {
        switch (httpStatus) {
            case 12002:
            case 12029:
            case 12030:
            case 12031:
            case 12152:
            case 13030:
                responseObject = this.createExceptionObject(o.tId, callback.argument, (isAbort ? isAbort : false));
                if (callback.failure) {
                    if (!callback.scope) {
                        callback.failure(responseObject);
                    } else {
                        callback.failure.apply(callback.scope, [responseObject]);
                    }
                }
            break;
            case 401:
                Ext.ux.util.MessageBus.publish('ext.lib.ajax.authorizationRequired', {
                    requestObject : o,
                    rawResponse   : o.conn
                });
            default:
                responseObject = this.createResponseObject(o, callback.argument);
                if (callback.failure) {
                    if (!callback.scope) {
                        callback.failure(responseObject);
                    } else {
                        callback.failure.apply(callback.scope, [responseObject]);
                    }
                }
            break;
        }
    }

    this.releaseObject(o);
    responseObject = null;
};

