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

Ext.namespace('com.conjoon.groupware.email.view');

/**
 *
 * @class com.conjoon.groupware.email.view.EmailGridRowRenderer
 * @singleton
 */
com.conjoon.groupware.email.view.EmailGridRowRenderer = function()
{
    var _idCache = [];

    // shorthand to Ext.data.Record.COMMIT
    var _commit = Ext.data.Record.COMMIT;

    var _dateFormat = 'd.m.Y';
    var _timeFormat = 'H:i';


    /**
     * Called when a manual request for new emails from the Letterman has been made.
     * Will reset _idCache to an empty array.
     *
     * @param {String} subject
     * @param {Object}
     *
     */
    var _clearCache = function(subject, message)
    {
        Ext.ux.util.MessageBus.publish('com.conjoon.groupware.email.LatestEmailCache.clear', {
            itemIds : _idCache.splice(0, _idCache.length)
        });
        _idCache = [];
    };

    /**
     * Called when an item in the email grid has been removed or updated.
     * Will remove the id of the item found in the message out of
     * _idCache.
     *
     * @param {String} subject
     * @param {Object}
     *
     */
    var _onEmailItemChange = function(subject, message)
    {
        if (subject == 'com.conjoon.groupware.email.EmailGrid.store.update' && message.operation != _commit) {
            return;
        }
        if (message.item) {
            _idCache.remove(message.item.id);
        } else if (message.items) {
            var items = message.items;
            for (var i = 0, len = items.length; i < len; i++) {
                _idCache.remove(items[i].id);
            }
        }
    };

    /**
     * Called when the letterman has fetched new emails. Will store the
     * ids of the new items in a numeric array.
     *
     * @param {String} subject
     * @param {Object
     *
     */
    var _onLettermanLoad = function(subject, message)
    {
        if (message.total == 0) {
            return;
        }

        var recs = message.items;
        var id   = null;
        for (var i = 0, len = message.total; i < len; i++) {
            id = recs[i].id;
            if (_idCache.indexOf(recs[i].id) == -1) {
                _idCache.push(id);
            }
        }

    };

    /**
     * The renderer subscribes to the load/peekIntoInbox message published by the Letterman
     */
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.Letterman.load',
        _onLettermanLoad
    );
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.Letterman.peekIntoInbox',
        _clearCache
    );

    /**
     * The renderer subscribes to the remove/update message published by the email grid
     */
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.EmailGrid.store.remove',
        _onEmailItemChange
    );
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.EmailGrid.store.update',
        _onEmailItemChange
    );

    /**
     * The renderer subscribes to the remove/update message published by the latest emails grid
     */
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.LatestEmailsPanel.store.remove',
        _onEmailItemChange
    );
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.LatestEmailsPanel.store.update',
        _onEmailItemChange
    );

    return {

        /**
         * Renderer for the "subject" column of the email grid.
         *
         *
         * @param {Object} value The data value for the cell.
         * @param {Object} metadata An object in which you may set the following attributes:
         *                 - {String} css A CSS class name to add to the cell's TD element
         *                 - {String} attr An HTML attribute definition string to apply to
         *                                 the data container element within the table cell
         *                                 (e.g. 'style="color:red;"').
         * @param {Ext.data.Record} record The Ext.data.Record from which the data was extracted
         * @param {Number} rowIndex Row index
         * @param {Number} colIndex Column index
         * @param {Ext.data.Store} store
         *
         * @return {String}
         */
        renderSubjectColumn : function(value, metadata, record, rowIndex, colIndex, store)
        {
            if (_idCache.indexOf(record.id) != -1) {
                metadata.css = 'newItem';
            } else {
                var refTypes = record.get('referencedAsTypes').join(',');

                if (refTypes != '') {
                    var css = [];

                    if (refTypes.indexOf('reply') != -1) {
                        css.push('hasReply');
                    }

                    if (refTypes.indexOf('forward') != -1) {
                        css.push('hasForward');
                    }

                    metadata.css = css.join('');
                }
            }

            return value;
        },

        /**
         * Renderer for the "date" column of the email grid.
         *
         *
         * @param {Object} value The data value for the cell.
         * @param {Object} metadata An object in which you may set the following attributes:
         *                 - {String} css A CSS class name to add to the cell's TD element
         *                 - {String} attr An HTML attribute definition string to apply to
         *                                 the data container element within the table cell
         *                                 (e.g. 'style="color:red;"').
         * @param {Ext.data.Record} record The Ext.data.Record from which the data was extracted
         * @param {Number} rowIndex Row index
         * @param {Number} colIndex Column index
         * @param {Ext.data.Store} store
         *
         * @return {String}
         */
        renderDateColumn : function(value, metadata, record, rowIndex, colIndex, store)
        {
            if(!value){
                return "";
            }

            value = new Date(Date.parse(value));

            var dateParts = value.dateFormat(_dateFormat+' '+_timeFormat).split(' ');
            var today     = (new Date()).dateFormat(_dateFormat);

            if (dateParts[0] == today) {
                return dateParts[1];
            }

            return dateParts[0] + ' ' + dateParts[1];
        }

    };


}();