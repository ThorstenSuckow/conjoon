/*
 * Ext.ux.YoutubePlayer
 * Copyright(c) 2008-2009, Thorsten Suckow-Homberg <ts@siteartwork.de>.
 * 
 * This code is licensed under GNU LESSER GENERAL PUBLIC LICENSE.
 * 
 * This project is released as a sub project of conjoon <http://www.conjoon.org>
 */


Ext.namespace('Ext.ux.YoutubePlayer');Ext.ux.YoutubePlayer=Ext.extend(Ext.FlashComponent,{player:null,videoId:null,initComponent:function()
{this.addEvents('ready','stateChange','error');Ext.apply(this,{ratioMode:this.ratioMode||'normal',swfId:this.playerId,style:this.ratioMode=='strict'?'position:relative':'position:normal'});Ext.applyIf(this,{url:"http://gdata.youtube.com/apiplayer?key="+
this.developerKey+"&enablejsapi=1&playerapiid="+
this.playerId,start:false,controls:false,cls:'ext-ux-youtubeplayer '+this.ratioMode,scripting:'always',params:{wmode:'opaque',bgcolor:this.bgColor||"#cccccc"}});if(!Ext.ux.YoutubePlayer.Players){Ext.ux.YoutubePlayer.Players=[];}
Ext.ux.YoutubePlayer.Players[this.playerId]=this.id;},_setPlayer:function(player)
{this.player=player;},_delegateStateEvent:function(state)
{switch(state){case-1:state='unstarted';break;case 0:state='ended';break;case 1:state='playing';break;case 2:state='paused';break;case 3:state='buffering';break;case 5:state='video_cued';break;default:state='unknown';break;}
this.fireEvent('stateChange',state,this,this.player);},_delegateErrorEvent:function(errorCode)
{switch(errorCode){case 100:errorCode='video_not_found';break;default:errorCode='unknown';break;}
this.fireEvent('error',errorCode,this,this.player);},onResize:function(w,h,w1,h1)
{if(this.playerAvailable()){this.adjustRatio(this.getWidth(),this.getHeight());}},adjustRatio:function(width,height)
{var pStyle=this.player.style;switch(this.ratioMode){case'strict':if(width<400||height<320){var newHeight=Math.floor(width*0.8);if(newHeight>height){width=Math.floor(height/0.8);}else{height=newHeight;}}else{if(height>320){height=320;width=400;}}
pStyle.marginTop=-Math.floor(height/2)+'px';pStyle.marginLeft=-Math.floor(width/2)+'px';pStyle.height=height+'px';pStyle.width=width+'px';pStyle.top='50%';pStyle.left='50%';this.setPlayerSize(width,height);break;case'stretch':pStyle.margin='auto';pStyle.height=height+'px';pStyle.width=width+'px';this.setPlayerSize(width,height);break;}},playerAvailable:function()
{return(this.player&&this.player.getPlayerState)?true:false;},loadVideoById:function(videoId,startSeconds)
{this.player.loadVideoById(videoId,startSeconds);this.videoId=videoId;},cueVideoById:function(videoId,startSeconds)
{this.player.cueVideoById(videoId,startSeconds);this.videoId=videoId;},setPlayerSize:function(width,height)
{if(!this.playerAvailable()){return;}
this.player.setSize(width,height);},playVideo:function()
{if(!this.playerAvailable()){return;}
this.player.playVideo();},pauseVideo:function()
{if(!this.playerAvailable()){return;}
this.player.pauseVideo();},stopVideo:function()
{if(!this.playerAvailable()){return;}
this.player.stopVideo();},clearVideo:function()
{if(!this.playerAvailable()){return;}
this.videoId=null;this.player.clearVideo();},getVideoBytesLoaded:function()
{if(!this.playerAvailable()){return 0;}
return this.player.getVideoBytesLoaded();},getVideoBytesTotal:function()
{if(!this.playerAvailable()){return 0;}
return this.player.getVideoBytesTotal();},getVideoStartBytes:function()
{if(!this.playerAvailable()){return 0;}
return this.player.getVideoStartBytes();},mute:function(mute)
{if(!this.playerAvailable()){return;}
if(mute===false){this.player.unMute();this.setVolume(this.getVolume());}else{this.player.mute();}},isMuted:function(mute)
{if(!this.playerAvailable()){return true;}
return this.player.isMuted();},setVolume:function(volume)
{if(!this.playerAvailable()){return;}
this.player.setVolume(volume);},getVolume:function()
{if(!this.playerAvailable()){return 0;}
return this.player.getVolume();},seekTo:function(seconds,allowSeekAhead)
{if(!this.playerAvailable()){return;}
this.player.seekTo(seconds,allowSeekAhead);},getPlayerState:function()
{var state=-9999;if(!this.playerAvailable()){return;}else{state=this.player.getPlayerState();}
switch(state){case-1:state='unstarted';break;case 0:state='ended';break;case 1:state='playing';break;case 2:state='paused';break;case 3:state='buffering';break;case 5:state='video_cued';break;default:state='unknown';break;}
return state;},getCurrentTime:function()
{if(!this.playerAvailable()){return 0;}
return this.player.getCurrentTime();},getDuration:function()
{if(!this.playerAvailable()){return 0;}
return this.player.getDuration();},getVideoUrl:function()
{if(!this.playerAvailable()){return"";}
return this.player.getVideoUrl();},getVideoEmbedCode:function()
{if(!this.playerAvailable()){return"";}
return this.player.getVideoEmbedCode();}});var _onYouTubePlayerReady=function(playerId){var cmpId=Ext.ux.YoutubePlayer.Players[playerId];if(cmpId){var panel=Ext.getCmp(cmpId);var player=document.getElementById(playerId);panel._setPlayer(player);player.addEventListener('onStateChange',"Ext.getCmp('"+panel.id+"')._delegateStateEvent");player.addEventListener('onError',"Ext.getCmp('"+panel.id+"')._delegateErrorEvent");panel.adjustRatio(panel.getWidth(),panel.getHeight());panel.fireEvent('ready',panel,player);}};if(!window.onYouTubePlayerReady){window.onYouTubePlayerReady=_onYouTubePlayerReady;}else{throw("\"onYouTubePlayerReady\" is already defined. Cannot use Ext.ux.XoutubePlayer.")}
