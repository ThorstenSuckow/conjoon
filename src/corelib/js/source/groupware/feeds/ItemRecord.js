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


de.intrabuild.groupware.feeds.ItemRecord = Ext.data.Record.create([

    {name: 'id', type : 'int'},
    {name: 'groupwareFeedsAccountsId', type : 'int'},
    {name: 'name', type : 'string'},
    {name: 'title', type : 'string'},
    {name: 'author', type : 'string'},
    {name: 'content', type : 'string'},
    {name: 'description', type : 'string'},
    {name: 'pubDate', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    {name: 'isRead', type : 'boolean'},
    {name: 'link', type : 'string'}


]);