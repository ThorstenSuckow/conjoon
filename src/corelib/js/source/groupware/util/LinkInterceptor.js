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
            } else if (href &&  (href.indexOf('http://www.youtube.com') == 0 ||
                       href.indexOf('http://youtube.com') == 0)) {
                com.conjoon.groupware.QuickEditPanel.loadYoutubeVideo(href);

            } else {
                window.open(com.conjoon.groupware.util.LinkInterceptor.getRedirectLink(href));
            }

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
                    var target = p.body ? b.pody : (p.el ? p.el : p);
                    m.addListener(target);
                    p.on('destroy', function(){this.removeListener(target)}, m);
                }
            };
        },

        getRedirectLink : function(link)
        {
            return 'index/redirect/url/'+encodeURIComponent(link);
        }
    };


}();