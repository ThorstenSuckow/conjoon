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

Ext.namespace('de.intrabuild.groupware.feeds');


de.intrabuild.groupware.feeds.AccountRecord = Ext.data.Record.create([

    {name: 'id', type : 'int'},
    {name: 'uri', type : 'string'},
    {name: 'link', type : 'string'},
    {name: 'description', type : 'string'},
    {name: 'title', type : 'string'},
    {name: 'name', type : 'string'},
    {name: 'updateInterval', type : 'int'},
    {name: 'deleteInterval', type : 'int'}


]);