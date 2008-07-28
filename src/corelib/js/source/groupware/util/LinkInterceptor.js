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

    return {
		
		addListener : function(p)
		{
            p.on({
                'mousedown': function(e, t){
					e.stopEvent();
                },
                'click': function(e, t){ 
                    e.stopEvent();
					window.open(de.intrabuild.groupware.util.LinkInterceptor.getRedirectLink(t.href));
                },
                delegate:'a'
            });			
		},
		
        getListener : function() 
        {
            return {
				
				render: function(p)
                {
					de.intrabuild.groupware.util.LinkInterceptor.addListener(p.body);
				}
            };
        },
    
        getRedirectLink : function(link)
        {
            return '/index/redirect/url/'+encodeURIComponent(link);    
        }
    };


}();