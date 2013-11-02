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

/**
 * A queue for jobs to be processed as soon as as proxy nodes where synced with the backend.
 *
 * Queue is a FiFo implementation.
 *
 *
 * @class {conjoon.mail.comp.folderPanel.compModel.ProxySyncQueue}
 */
Ext.defineClass('conjoon.mail.comp.folderPanel.compModel.ProxySyncQueue', {

    /**
     * The queue holding the jobs.
     * @type {Object}
     */
    queue : null,

    /**
     * The proxy tree loader this queue listens to.
     * @type {cudgets.tree.data.ProxyTreeLoader}
     */
    proxyTreeLoader : null,

    /**
     * Creates a new instance of this class.
     *
     * @param config
     *
     * @throws {cudgets.base.MissingPropertyException}
     */
    constructor : function(config) {

        if (!config || !config.proxyTreeLoader) {
            throw new cudgets.base.MissingPropertyException("proxyTreeLoader missing");
        }

        var me = this;

        me.proxyTreeLoader = config.proxyTreeLoader;

        me.installListeners();


        me.queue = {};

        Ext.apply(this.config);
    },

    /**
     * Installs the listeners for the proxytreeloader.
     *
     */
    installListeners : function() {

        var me = this;

        me.proxyTreeLoader.on('proxynodeload',
            function(treeLoader, proxyNode, validState, synced) {

                me.processQueueForNode(
                    proxyNode.id, 'proxynodeload', arguments
                )

        }, me);

    },

    /**
     * Adds a job to the queue which should be processed once the event specified
     * in eventname gets triggered. jobs get removed from the queue once they were
     * called.
     *
     * @param targetNodeId
     * @param eventName
     * @param jobConfig A configuration for the job. If teh property id is specified,
     * the job will only be added if not anotehr job for the same target node, the same eventName
     * and the same id is not already in the queue
     *
     */
    addJobForNodeAndEvent : function(targetNodeId, eventName, jobConfig) {

        var me = this,
            queue;

        if (!me.queue[targetNodeId]) {
            me.queue[targetNodeId] = {};
        }

        if (!me.queue[targetNodeId][eventName]) {
            me.queue[targetNodeId][eventName] = [];
        }

        if (jobConfig.id) {
            queue = me.queue[targetNodeId][eventName];

            for (var i = 0, len = queue.length; i < len; i++) {
                if (queue[i] && queue[i].id === jobConfig.id) {
                    // id already available
                    return;
                }
            }
        }

        me.queue[targetNodeId][eventName].push(jobConfig);
    },

    /**
     * Gets called whenever the proxytreeloader fires an event this sync-queue
     * listens to. The added jobs for the specified node and the specified
     * event name will be processed using FiFo strategy.
     *
     * @param {Mixed} targetNodeId the node id
     * that triggered the event
     * @param {String} eventName the name of the event that was fired by the
     * proxytreeloader
     * @param {Array} params a list of parameter sent by the event
     */
    processQueueForNode : function(targetNodeId, eventName, params) {

        var me = this,
            queue,
            job;

        if (me.queue[targetNodeId] && me.queue[targetNodeId][eventName]) {

            queue = me.queue[targetNodeId][eventName];

            while (queue.length) {
                job = queue.shift();
                job.fn.apply(job.scope || window, params);
            }
        }

    }


});
