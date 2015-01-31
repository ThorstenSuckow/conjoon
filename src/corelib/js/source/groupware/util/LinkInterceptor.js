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

Ext.namespace('com.conjoon.groupware.util');

com.conjoon.groupware.util.LinkInterceptor = function(){

    var _listener = {
        'mousedown': function(e, t){
            e.stopEvent();
        },
        'click': function(e, t){
            e.stopEvent();

            var href = t.href, mInd;

            if (href == '#') {
                return;
            }

            if (href && (mInd = href.indexOf('mailto:')) == 0) {
                com.conjoon.groupware.email.EmailEditorManager.createEditor(-1, 'new', {
                    name    : t.firstChild.data,
                    address : href.substr(7)
                });
                return;
            } else if (href &&  (href.indexOf('http://www.youtube.com') == 0 ||
                       href.indexOf('http://youtube.com') == 0)) {

                if (href.indexOf('v=') != -1 || href.indexOf('/v') != -1) {
                    var res = com.conjoon.groupware.service.youtube.VideoDirector.loadVideo(href);

                    if (res) {
                        return;
                    }
                }
            }

            window.open(com.conjoon.groupware.util.LinkInterceptor.getRedirectLink(href));


        },
        options : {
            delegate:'a'
        }
    };

    return {

        handleLinkClick : function(item)
        {
            var address = item.href;

            if (!address || address == '#') {
                return;
            }

            window.open(com.conjoon.groupware.util.LinkInterceptor.getRedirectLink(address));
        },

        removeListener : function(p)
        {
            p.un('mousedown', _listener.mousedown, window, _listener.options);
            p.un('click',     _listener.click,     window, _listener.options);
        },

        addListener : function(p)
        {
            p.on('mousedown', _listener.mousedown, window, _listener.options);
            p.on('click',     _listener.click,     window, _listener.options);
        },

        getListener : function()
        {
            var m = this;
            return {
                render : function(p) {
                    var target = p.body ? p.body : (p.el ? p.el : p);
                    m.addListener(target);
                    p.on('destroy', function(){this.removeListener(target)}, m);
                }
            };
        },

        getRedirectLink : function(link)
        {
            return 'index/redirect/url/' +
                encodeURIComponent(encodeURIComponent(link));
        }
    };


}();