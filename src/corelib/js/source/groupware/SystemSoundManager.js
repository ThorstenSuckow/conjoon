/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

Ext.namespace('com.conjoon.groupware');


/**
 * Make sure you load this class before the soundmanager2 scripts
 * are loaded.
 *
 * The API of this singleton is right now only to use with SoundManager2.
 *
 * @singleton
 * @class com.conjoon.groupware.SystemSoundManager
 */
com.conjoon.groupware.SystemSoundManager = function(){

    /**
     * @type {Boolean} _isInit Whether this singleton's init() method
     * was already called.
     */
    var _isInit = false;

    /**
     * @type {SoundManager} _driver The driver to play any sound.
     */
    var _driver = null;

    /**
     * @type {Boolean} _driverLoaded indicates whether the driver is fully
     * loaded and ready to play sounds
     */
    var _driverLoaded = false;

    /**
     * @type {Array} _loadListener an array of functions serving as callbacks
     * for the load-event of the sound driver
     */
    _loadListener = [];

    /**
     * Loads the various sounds into memory,
     *
     */
    var _createSounds = function()
    {
        _driver.createSound({
            id  : 'startup',
            url : './js/conjoon/resources/sfx/startup.mp3'
          });
        _driver.createSound({
            id  : 'new_email',
            url : './js/conjoon/resources/sfx/new_email.mp3'
        });
        _driver.createSound({
            id  : 'email_sent',
            url : './js/conjoon/resources/sfx/email_sent.mp3'
        });
        _driver.createSound({
            id  : 'question',
            url : './js/conjoon/resources/sfx/question.mp3'
        });
        _driver.createSound({
            id  : 'error',
            url : './js/conjoon/resources/sfx/error.mp3'
        });
        _driver.createSound({
            id  : 'notify',
            url : './js/conjoon/resources/sfx/notify.mp3'
        });
        _driver.createSound({
            id  : 'shutdown',
            url : './js/conjoon/resources/sfx/shutdown.mp3'
        });
        _driver.createSound({
            id  : 'new_tweet',
            url : './js/conjoon/resources/sfx/new_tweet.mp3'
        });
        _driver.createSound({
            id  : 'new_feed',
            url : './js/conjoon/resources/sfx/new_feed.mp3'
        });
    };

    /**
     * Inits the events the _driver should listen to.
     */
    var _initEvents = function()
    {
        _createSounds();

        var mb = Ext.ux.util.MessageBus;

        Ext.MessageBox.getDialog().on('show', function(dialog) {
            var cn = dialog.el.dom.className;

            switch (true) {
                case cn.indexOf('com-conjoon-msgbox-warning') != -1:
                case cn.indexOf('com-conjoon-msgbox-info') != -1:
                    _driver.play('notify');
                break;
                case cn.indexOf('com-conjoon-msgbox-error') != -1:
                case cn.indexOf('com-conjoon-msgbox-critical') != -1:
                    _driver.play('error');
                break;
                case cn.indexOf('com-conjoon-msgbox-prompt') != -1:
                case cn.indexOf('com-conjoon-msgbox-question') != -1:
                    _driver.play('question');
                break;
            }
        });

        com.conjoon.groupware.Reception.onBeforeLogout(function(){
            _driver.play('shutdown');
        });

        mb.subscribe('com.conjoon.service.twitter.newTweets', function() {
            _driver.play('new_tweet');
        });

        mb.subscribe('com.conjoon.groupware.feeds.FeedRunner.newFeeds', function() {
            _driver.play('new_feed');
        });

        mb.subscribe('com.conjoon.groupware.email.Smtp.emailSent', function() {
            _driver.play('email_sent');
        });
        mb.subscribe('com.conjoon.groupware.email.Smtp.bulkSent', function() {
            _driver.play('email_sent');
        });
        mb.subscribe('com.conjoon.groupware.email.Letterman.load', function(subject, message) {
            if (message.total > 0) {
                _driver.play('new_email');
            }
        });

        _driverLoaded = true;

        for (var i = 0, len = _loadListener.length; i < len; i++) {
            _loadListener[i].fn.call((_loadListener[i].scope ? _loadListener[i].scope : window))
        }

    };

    return {

        /**
         * Returns true if the driver is ready to play sounds.
         *
         * @return {Boolean}
         */
        isDriverReady : function()
        {
            return _driverLoaded;
        },

        /**
         * Inits the driver to play any sound and calls _initEvents()
         * right after the driver was instantiated.
         *
         * This method sould be called once the Ext Application has been
         * fully loaded.
         *
         * @param {String} type The type of driver to use. Defaults
         * to SoundManager2.
         *
         * @return this
         */
        initDriver : function(type)
        {
            if (_driver != null) {
                throw("com.conjoon.groupware.SystemSoundManager.initDriver(): Driver already set");
                return;
            }

            switch (type) {
                default:
                    // apparently, SoundManager2 flash relies on
                    // the existence of a global SoundManager2 variable
                    // in the window scope
                    window.soundManager = new SoundManager();
                    _driver = soundManager
                    _driver.url = './js/soundmanager/swf/';
                    _driver.debugMode   = false;
                    _driver.consoleOnly = true;
                    _driver.onload = _initEvents;
                    _driver.go();
                break;
            }

            return this;
        },

        /**
         * Returns the driver responsible to play any sound.
         *
         * @return {SoundManager}
         */
        getDriver : function()
        {
            return _driver;
        },

        /**
         * Adds a onload listener for the driver that will be notified as soon
         * as the driver is ready to play.
         * Note:
         * register your listeners before you make a call to "initDriver()",
         * otherwise an exception is thrown.
         *
         * @param {Object} config A configuration object, specifying following
         * properties:
         *  fn - The function to call if the driver was loaded
         *  scope - The scope to call the function in - defaults to "window"
         *
         * @throws {Exception} if the driver has already been initialized.
         */
        onLoad : function(config)
        {
            if (_driver != null) {
                throw(
                    "\"com.conjoon.groupware.SystemSoundManager\" "+
                    "is already initialized"
                );
            }

            _loadListener.push(config);
        },

        /**
         * Inits the SystemSoundManager with some needed environment information.
         * See initDriver() to init the driver to play any sound.
         * This method will be called automatically when this singleton has
         * been loaded.
         */
        init : function()
        {
            if (_isInit) {
                return;
            }
            /**
             * soundmanager2 relies heavily on this setting. Set this to "false" and an
             * instance of soundmanager2 will be created automatically, which will result
             * in problems for Ext JS driven applications where the viewport has not yet
             * been rendered (some attributes for "body" will be altered which will make
             * any previously rendered flash-movie invalid in some browsers) .
             *
             * This variable has to be set before soundmanager2 was loaded.
             */
            window.SM2_DEFER = true;
            _isInit = true;
            return this;
        }

    };


}().init();