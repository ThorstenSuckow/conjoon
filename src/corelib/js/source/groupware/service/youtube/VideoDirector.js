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
            //last = null;
        }).defer(1000);
    };

    return {

        /**
         * Attempts to load the specified video url.
         * For playing a video, the basePanel must be available.
         *
         * @return {Boolean} false if the video could not be loaded due to the
         * basePanel not available, otherwise true
         */
        loadVideo : function(url)
        {
            if (!viewBaton.showPlayer()) {
                return false;
            }

            var control = viewBaton.getControl();
            var player  = viewBaton.getPlayer();

            var id = control._parseVideoId(url);

            if (id == last) {
                return true;
            }

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
            } else {

                if (Ext.form.VTypes.url(url)) {
                    com.conjoon.SystemMessageManager.confirm(new com.conjoon.SystemMessage({
                        title : com.conjoon.Gettext.gettext("Youtube id error"),
                        text  : String.format(
                            com.conjoon.Gettext.gettext("\"{0}\" does not seem to be a valid Youtube video id. Do you want to try to load the specified url in another window instead?"),
                            url
                        )
                    }), {
                        fn : function(button) {
                            if (button == 'yes') {
                                window.open(com.conjoon.groupware.util.LinkInterceptor.getRedirectLink(url));
                            }
                        }
                    });
                } else {
                    com.conjoon.SystemMessageManager.prompt(new com.conjoon.SystemMessage({
                        title : com.conjoon.Gettext.gettext("Youtube id error"),
                        text  : String.format(
                            com.conjoon.Gettext.gettext("\"{0}\" does not seem to be a valid Youtube video id. Please try again."),
                            url
                        )
                    }), {
                        fn : function(button, text) {
                            if (button == 'ok') {
                                com.conjoon.groupware.service.youtube.VideoDirector.loadVideo(text);
                            }
                            return;
                        }
                    });
                }
            }

            return true;
        }

    };

}();