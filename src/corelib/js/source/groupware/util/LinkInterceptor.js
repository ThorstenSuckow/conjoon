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
        getListener : function() 
        {
            return {
                render: function(p)
                {
                    p.body.on({
                        'mousedown': function(e, t){ 
                            e.stopEvent();
                            //window.open(de.intrabuild.groupware.util.LinkInterceptor.getRedirectLink(t.href));
                        },
                        'click': function(e, t){ 
                            //if(String(t.target).toLowerCase() != '_blank'){
                                e.stopEvent();
                                window.open(de.intrabuild.groupware.util.LinkInterceptor.getRedirectLink(t.href));
                            //}
                        },
                        delegate:'a'
                });}
            };
        },
    
        getRedirectLink : function(link)
        {
            return '/index/redirect/url/'+encodeURIComponent(link);    
        }
    };


}();