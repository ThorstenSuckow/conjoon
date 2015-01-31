<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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

 /**
 * A collection of constants defining keys for registry- and session-entries.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface Conjoon_Keys {

// -------- registry
    const REGISTRY_AUTH_OBJECT = 'com.conjoon.registry.authObject';

// -------- ext request object
    const EXT_REQUEST_OBJECT = 'com.conjoon.registry.extRequestObject';

// -------- ext request object
    const DOCTRINE_ENTITY_MANAGER = 'com.conjoon.registry.doctrine.entityManager';

// -------- app config in registry
    const REGISTRY_CONFIG_OBJECT = 'com.conjoon.registry.config';

// -------- session auth namespace
    const SESSION_AUTH_NAMESPACE = 'com.conjoon.session.authNamespace';

// -------- session reception controller
    const SESSION_CONTROLLER_RECEPTION = 'com.conjoon.session.receptionController';

// -------- session application cache helper
    const SESSION_APPLICATION_CACHE = 'com.conjoon.session.applicationCache';

// -------- session twitter oauth
    const SESSION_SERVICE_TWITTER_OAUTH = 'com.conjoon.service.twitter.oauth';

// -------- cache key emails
    const CACHE_EMAIL_MESSAGE = 'com.conjoon.cache.email.message';

// -------- cache key email accounts
    const CACHE_EMAIL_ACCOUNTS = 'com.conjoon.cache.email.accounts';

// -------- cache email folder root types
    const CACHE_EMAIL_FOLDERS_ROOT_TYPE = 'com.conjoon.cache.email.folders.root_type';

// -------- cache key feed items
    const CACHE_FEED_ITEM = 'com.conjoon.cache.feed.item';

// -------- cache key feed item lists
    const CACHE_FEED_ITEMLIST = 'com.conjoon.cache.feed.itemlist';

// -------- cache key feed reader
    const CACHE_FEED_READER = 'com.conjoon.cache.feed.reader';

// -------- cache key feed account
    const CACHE_FEED_ACCOUNT = 'com.conjoon.cache.feed.account';

// -------- cache key feed account list
    const CACHE_FEED_ACCOUNTLIST = 'com.conjoon.cache.feed.accountlist';

// -------- cache db metadata
    const CACHE_DB_METADATA = 'com.conjoon.cache.db.metadata';

// -------- cache twitter accounts
    const CACHE_TWITTER_ACCOUNTS = 'com.conjoon.cache.twitter.accounts';

// -------- cookies

    // -- auto login username
    const COOKIE_REMEMBERME_UNAME = 'cn_cookie_uname';

    // -- auto login remember me token
    const COOKIE_REMEMBERME_TOKEN = 'cn_cookie_rmToken';

}
