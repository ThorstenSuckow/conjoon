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
 
Ext.namespace('de.intrabuild.groupware.email');

/**
 * The Letterman is a singleton that's responsible for checking for new emails
 * in a given interval. It checks for new emails and delivers the data to the
 * registered listeners. After the Letterman has checked for new mails, it's store
 * will be wiped to make room for new data.
 *
 * If you want to receive messages from this component, you can subscribe to the
 * Ext.ux.util.MessageBus and listen for the following messages:
 * <ul>
 * <li><strong>de.intrabuild.groupware.email.Letterman.beforeload</strong> - sent
 * before this component's store sends a request to the server to receive new messages.
 * The subscriber is passed the following parameters: 
 *     <ul>
 *      <li>subject - the subject of the message</li>
 *      <li>message - an empty object</li>
 *     </ul>
 * </li>
 * <li><strong>de.intrabuild.groupware.email.Letterman.loadexcepion</strong> - sent when
 * a request made by this component's store resulted in a failure.
 * The subscriber is passed the following parameters: 
 *     <ul>
 *      <li>subject - the subject of the message</li>
 *      <li>message - an empty object</li>
 *     </ul>
 * </li>
 * <li><strong>de.intrabuild.groupware.email.Letterman.load</strong> - sent when a request made
 * by this component's store resulted in a successfull response. 
 * The subscriber is passed the following parameters: 
 *     <ul>
 *      <li>subject - the subject of the message</li>
 *      <li>message - an object containing details about the message, with the follwing properties:
 *        <ul>
 *         <li>total - the total number of new emails as received by the server</li>
 *        </ul>
 *       </li>
 *     </ul>
 * </li>
 * </ul>   
 *
 *
 */
de.intrabuild.groupware.email.Letterman = function(config) {
    
    /**
     * A shorthand for the {@see Ext.ux.util.MessageBus} which is used
     * to publish messages to and receive messages from
     */ 
    var _messageBroadcaster = Ext.ux.util.MessageBus;
    
    /**
     * The store in which new emails will be added. The store is wiped upon
     * each new call to load.
     */
    var store = new Ext.data.Store({
        autoLoad    : false,
        remoteSort  : true,
        reader      : new Ext.data.JsonReader({
                          root            : 'items',
                          totalProperty   : 'totalCount',
                          successProperty : 'success',
                          id              : 'id'
                      }, 
                      de.intrabuild.groupware.email.EmailItemRecord
                      ),
        sortInfo   : {field: 'date', direction: 'ASC'},      
        proxy      : new Ext.data.HttpProxy({
            url      : '/groupware/email/fetch.emails/format/json',
            timeout  : 20*60*1000
        })
    });
    
    
    /**
     * The interval in which the letterman should check for new mails, in minutes.
     * @param {Number}
     */
    var interval = 5;
    
    /**
     * The task of the letterman.
     */
    var task = null;
    
    /**
     * The TaskRunner.
     * @param {Ext.util.TaskRunner}
     */
    var letterman = null;
    
    /**
     * Propert to check if the Lettermans run method has been called for the 
     * first time.
     * Defaults to false.
     */
    var called = false;
    
    /**
     * Overrides proxy's loadResponse to check for error
     *
     */
    var proxyResponse = function(o, success, response)
    { 
        var json = de.intrabuild.util.Json;
    	if (json.isError(response.responseText)) {
    	    de.intrabuild.groupware.email.Letterman.onRequestFailure(this, null, response, null);
    	}	
    		
    	return Ext.data.HttpProxy.prototype.loadResponse.call(this, o, success, response);
    
    };
    
    
    return {
        
        init : function()
        {
            store.on('loadexception', this.onRequestFailure, this); 
            store.on('load',          this.onLoad, this); 
            store.proxy.loadResponse = proxyResponse;
            return this; 
        },
        
        on : function(eventType, callback, scope)
        {
            store.on(eventType, callback, scope);
        },
        
        un : function(eventType, callback, scope)
        {
            store.un(eventType, callback, scope);
        },        
        
        /**
         * If the task for periodically checking mails is not started yet, this
         * method will wake the letterman up and order him to check for new mails
         * in the given interval.
         *
         */
        wakeup : function()
        {
            if (task != null) {
                return;
            }
            
            task = {
                run      : this.run,
                scope    : this,
                interval : (interval*60*1000)
            };
            
            if (letterman == null) {
                letterman = new Ext.util.TaskRunner();
            }
            
            letterman.start(task);
        },
        
        /**
         * Tells the letterman to take a break.
         *
         */
        rest : function()
        {
            if (letterman == null || task == null) {
                return;
            }
            
            letterman.stop(task);
            task = null;
        },        
        
        /**
         * Since the taskrunner executes the given method as soon as the thread
         * starts, this method will check each call and skip if it was called 
         * for the first time.
         */
        run : function()
        {
            if (!called) {
                called = true;
                return;
            }
            
            this.peekIntoInbox();
        },
        
        /**
         * Method sends a request to the server to fetch new mails.
         *
         */
        peekIntoInbox : function()
        {
            if (store.proxy.activeRequest) {
                return;    
            }
            _messageBroadcaster.publish('de.intrabuild.groupware.email.Letterman.beforeload', {});
            store.reload();
        },
        
        /**
         *
         */
        callout : function(length)
        {
            var text = "You have <b>"+length+"</b> new email"+(length > 1 ? "s." : ".");  
        
            new Ext.ux.ToastWindow({    
                title   : "New email"+(length > 1 ? "s" : ""),    
                html    : text
            }).show(document); 
            
            if (de.intrabuild.groupware.SoundManager) {
                de.intrabuild.groupware.SoundManager.play('newemail');
            }
        },
        
        /**
        *
        *
        */
        onLoad : function(store, records, options)
        {
            store.removeAll();
            var length = records.length;
            _messageBroadcaster.publish('de.intrabuild.groupware.email.Letterman.load', {total : length});
            if (length > 0) {
                this.callout(length)
            }
        },
        
        /**
         *
         */
        onRequestFailure : function(proxy, request, response, event)
        {
            _messageBroadcaster.publish('de.intrabuild.groupware.email.Letterman.loadexception', {});
            
			de.intrabuild.groupware.ResponseInspector.handleFailure(response);
        }
        
        
        
    };

    
}().init(); 