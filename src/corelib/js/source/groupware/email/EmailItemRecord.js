Ext.namespace('de.intrabuild.groupware.email');


de.intrabuild.groupware.email.EmailItemRecord = Ext.data.Record.create([
    {name : 'id', type : 'int'},
    {name : 'isAttachment',       type : 'boolean'},
    {name : 'isRead',             type : 'boolean'},
    {name : 'recipients',         type : 'string'},
    {name : 'referencedAsTypes'},
    {name : 'subject',            type : 'string'},
    {name : 'sender',             type : 'string'},
    {name : 'date',               type : 'date', dateFormat : 'Y-m-d H:i:s'},
    {name : 'isSpam',             type : 'boolean'},
    {name : 'isDraft',            type : 'boolean'},
    {name : 'isOutboxPending',    type : 'boolean'},
    {name : 'groupwareEmailFoldersId',  type : 'int'}
]);