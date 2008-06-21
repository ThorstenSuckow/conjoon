/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author$
 * $Id$
 * $Date$ 
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$ 
 */
 
var deintrabuildgroupwareemailAccountStore = new Ext.data.Store({
	storeId	 : Ext.id(),
	url    	 : '/groupware/email/get.email.accounts/format/json',
	autoLoad : false,
	pruneModifiedRecords : true,
	reader 	 : new Ext.data.JsonReader({
	    root : 'accounts',
	    id   : 'id'
	}, de.intrabuild.groupware.email.AccountRecord)
});   

de.intrabuild.util.Registry.register('de.intrabuild.groupware.email.AccountStore', deintrabuildgroupwareemailAccountStore);