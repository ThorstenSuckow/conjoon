/*
 * Ext JS Library 3.0 Pre-alpha
 * Copyright(c) 2006-2008, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */

/**
 * Ext.data.Api is a singleton designed to manage the data API including methods for validating
 * a developer's DataProxy API.  Defines CONSTANTS for CRUD-actions create, read, update and destroy.
 * @singleton
 */
Ext.data.Api = (function() {

    return {
        /**
         * @const Ext.data.Api.CREATE Text representing the remote-action "create"
         */
        CREATE  : 'create',
        /**
         * @const Ext.data.Api.READ Text representing the action for remotely reading/loading data from server.
         * The word "load" is important for maintaining backwards-compatibility with Ext-2.0, as well.  "read" would be preferred.
         * However, with the introduction of Ext.data.Api singleton, we my be able to use "read" now.
         */
        READ    : 'load',
        /**
         * @const Ext.data.Api.UPDATE Text representing the remote-action to rupdate records on server.
         * The word "update" would be preferred here, instead of "save" but "update" has already been used for events pre-Ext3.
         * However, since the introduction of the Ext.data.Api singleton, we may be able to use "udpate" now.
         */
        UPDATE  : 'save',
        /**
         * @const Ext.data.Api.DESTROY Text representing the remote-action to destroy records on server.
         */
        DESTROY : 'destroy',

        /**
         * Returns a list of names of all available CRUD actions
         * @return {String[]}
         */
        getVerbs : function(){
            return [this.CREATE, this.READ, this.UPDATE, this.DESTROY];
        },
        /**
         * Returns true if supplied action-name is a valid API action defined in CRUD constants
         * Ext.data.Api.CREATE, Ext.data.Api.READ, Ext.data.Api.UPDATE, Ext.data.Api.DESTROY
         * @param {String} action
         * @param {String[]}(Optional) List of availabe CRUD actions.  Pass in list when executing multiple times for efficiency.
         * @return {Boolean}
         */
        isVerb : function(action, crud) {
            var found = false;
            crud = crud || this.getVerbs();
            for (var n=0,len=crud.length;n<len;n++) {
                if (crud[n] == action) {
                   found = true;
                   break;
                }
            }
            return found;
        },
        /**
         * Returns true if the supplied API is valid; that is, that all keys match defined CRUD-actions,
         * Ext.data.Api.CREATE, Ext.data.Api.READ, Ext.data.Api.UPDATE, Ext.data.Api.DESTROY.  Otherwise returns an array of mistakes.
         * @return {String[]||true}
         */
        isValid : function(api){
            var invalid = [];
            var crud = this.getVerbs(); // <-- cache a copy of teh verbs.
            for (var action in api) {
                if (!this.isVerb(action, crud)) {   // <-- send cache of verbs into isVerb.  This call is only reason for isVerb to accept 2nd param.
                    invalid.push(action);
                }
            }
            return (!invalid.length) ? true : invalid;
        }
    }
})();


