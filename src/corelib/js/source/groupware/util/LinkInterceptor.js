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

Ext.namespace('de.intrabuild.groupware.util');

de.intrabuild.groupware.util.LinkInterceptor = function(){

    var _listener = {
        'mousedown': function(e, t){
            e.stopEvent();
        },
        'click': function(e, t){
            e.stopEvent();
            window.open(de.intrabuild.groupware.util.LinkInterceptor.getRedirectLink(t.href));
        },
        delegate:'a'
    };

    return {

        removeListener : function(p)
        {
            p.un('mousedown', _listener.mousedown);
			p.un('click',     _listener.click);
        },

		addListener : function(p)
		{
            p.on(_listener);
		},

        getListener : function()
        {
            var m = this;
			return {
				render: function(p)
                {
					de.intrabuild.groupware.util.LinkInterceptor.addListener(p.body);
					p.on('destroy', function(){this.removeListener(p.body)}, m);
				}
            };
        },

        getRedirectLink : function(link)
        {
            return 'index/redirect/url/'+encodeURIComponent(link);
        }
    };


}();