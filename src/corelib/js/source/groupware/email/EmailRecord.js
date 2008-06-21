Ext.namespace('de.intrabuild.groupware.email');


de.intrabuild.groupware.email.EmailRecord = Ext.data.Record.create([
    {name : 'id', type : 'int'},
    {name : 'attachments'},
    {name : 'subject', type : 'string'},
    {name : 'from', type : 'string'},
    {name : 'date', type : 'date', dateFormat : 'Y-m-d H:i:s'},
    {name : 'isSpam', type : 'boolean'},
    {name : 'body', type : 'string'},
    {name : 'cc', type : 'string'},
    {name : 'to', type : 'string'},
    {name : 'groupwareEmailFoldersId', type : 'int'}
]);