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


Ext.namespace('com.conjoon.groupware.service.youtube');

/**
 * A singleton for managing playing videos-
 *
 *
 * @class com.conjoon.groupware.service.youtube.VideoDirector
 * @singleton
 */
com.conjoon.groupware.service.youtube.VideoDirector = function() {

    /**
     * @type {Boolean} onReadyAttached
     */
    var onReadyAttached = false;

    /**
     * @type {String} videoQueue
     */
    var videoQueue = null;

    /**
     * @type {String} last
     */
    var last = null;

    /**
     * @type {com.conjoon.groupware.service.youtube.ViewBaton}
     */
    var viewBaton = com.conjoon.groupware.service.youtube.ViewBaton;


    /**
     * Attempts to play the video id found in videoQueue.
     *
     */
    var playQueue = function()
    {
        var player = viewBaton.getPlayer();

        if (player.videoId) {
            player.stopVideo();
            player.clearVideo();
        }

        // this is needed in case the user double clicks a link.
        // the flash movie obviously seems some defer time to init itself,
        // otherwise an empty video_id will be send to the youtube servers which
        // cannot be influenced by the server
        if (last == videoQueue) {
            return;
        }

        last = videoQueue;

        (function(){
            player.loadVideoById(videoQueue);
            last = null;
        }).defer(1000);
    };

    return {

        /**
         * Attempts to load the specified video url.
         * For playing a video, the control must be available.
         * If the basePanels owner ct is either a west or east panel
         * of the workbench and if its currently hidden, a new tab will be added
         * to the workbench's content panel, and the video will be loaded.
         * Otherwise, if the player sits in the basePanel and if no owner is
         * hidden, it will play rigth away.
         *
         */
        loadVideo : function(url)
        {
            if (!viewBaton.playerAvailable()) {
                return;
            }

            var control = viewBaton.getControl();
            var player  = viewBaton.getPlayer();

            //if (_panel.ownerCt.hidden) {
                //_panel.ownerCt.setVisible(true);
            //}

            //_panel.setActiveTab(_playerContainer);

            var id = control._parseVideoId(url);
            if (id) {
                videoQueue = id;
                if (!player.playerAvailable()) {
                    if (!onReadyAttached) {
                        player.on('ready', function() {
                            playQueue();
                        });
                        onReadyAttached = true;
                    }
                } else {
                    playQueue();
                }
            }
        }

    };

}();