/*

  SoundManager 2 Demo: "Page as playlist"
  ----------------------------------------------

  http://schillmania.com/projects/soundmanager2/

  An example of a Muxtape.com-style UI, where an
  unordered list of MP3 links becomes a playlist

  Requires SoundManager 2 Javascript API.

*/

function PagePlayer() {
  var self = this;
  var pl = this;
  var sm = soundManager;   // soundManager instance
  var isIE = navigator.userAgent.match(/msie/i);

  this.config = {
    allowRightClick:true,  // let users right-click MP3 links ("save as...", etc.) or discourage (can't prevent.)
    useThrottling: false,  // try to rate-limit potentially-expensive calls (eg. dragging position around)
    playNext: true,        // stop after one sound, or play through list until end
    updatePageTitle: true, // change the page title while playing sounds
    emptyTime: '-:--'      // null/undefined timer values (before data is available)
  }

  this.css = {             // CSS class names appended to link during various states
    sDefault: 'sm2_link',  // default state
    sLoading: 'sm2_loading',
    sPlaying: 'sm2_playing',
    sPaused: 'sm2_paused'
  }

  this.links = [];
  this.sounds = [];
  this.soundsByURL = [];
  this.lastSound = null;
  this.soundCount = 0;
  this.strings = [];
  this.dragActive = false;
  this.dragExec = new Date();
  this.dragTimer = null;
  this.pageTitle = document.title;
  this.lastWPExec = new Date();

  this.oControls = document.getElementById('control-template').cloneNode(true);
  this.oControls.id = '';

  this.addEventHandler = function(o,evtName,evtHandler) {
    typeof(attachEvent)=='undefined'?o.addEventListener(evtName,evtHandler,false):o.attachEvent('on'+evtName,evtHandler);
  }

  this.removeEventHandler = function(o,evtName,evtHandler) {
    typeof(attachEvent)=='undefined'?o.removeEventListener(evtName,evtHandler,false):o.detachEvent('on'+evtName,evtHandler);
  }

  this.hasClass = function(o,cStr) {
    return (typeof(o.className)!='undefined'?o.className.indexOf(cStr)+1:false);
  }

  this.addClass = function(o,cStr) {
    if (!o || !cStr) return false; // safety net
    if (self.hasClass(o,cStr)) return false;
    o.className = (o.className?o.className+' ':'')+cStr;
  }

  this.removeClass = function(o,cStr) {
    if (!o || !cStr) return false; // safety net
    if (!self.hasClass(o,cStr)) return false;
    o.className = o.className.replace(new RegExp('( '+cStr+')|('+cStr+')','g'),'');
  }

  this.getElementsByClassName = function(className,tagNames,oParent) {
    var doc = (oParent||document);
    var matches = [];
    var i,j;
    var nodes = [];
    if (typeof(tagNames)!='undefined' && typeof(tagNames)!='string') {
      for (i=tagNames.length; i--;) {
        if (!nodes || !nodes[tagNames[i]]) {
          nodes[tagNames[i]] = doc.getElementsByTagName(tagNames[i]);
        }
      }
    } else if (tagNames) {
      nodes = doc.getElementsByTagName(tagNames);
    } else {
      nodes = doc.all||doc.getElementsByTagName('*');
    }
    if (typeof(tagNames)!='string') {
      for (i=tagNames.length; i--;) {
        for (j=nodes[tagNames[i]].length; j--;) {
          if (self.hasClass(nodes[tagNames[i]][j],className)) {
            matches[matches.length] = nodes[tagNames[i]][j];
          }
        }
      }
    } else {
      for (i=0; i<nodes.length; i++) {
        if (self.hasClass(nodes[i],className)) {
          matches[matches.length] = nodes[i];
        }
      }
    }
    return matches;
  }
  
  this.getOffX = function(o) {
    // http://www.xs4all.nl/~ppk/js/findpos.html
    var curleft = 0;
    if (o.offsetParent) {
      while (o.offsetParent) {
        curleft += o.offsetLeft;
        o = o.offsetParent;
      }
    }
    else if (o.x) curleft += o.x;
    return curleft;
  }

  this.isChildOfClass = function(oChild,oClass) {
    if (!oChild || !oClass) return false;
    while (oChild.parentNode && !self.hasClass(oChild,oClass)) {
      oChild = oChild.parentNode;
    }
    return (self.hasClass(oChild,oClass));
  }

  this.getParentByNodeName = function(oChild,sParentNodeName) {
    if (!oChild || !sParentNodeName) return false;
    sParentNodeName = sParentNodeName.toLowerCase();
    while (oChild.parentNode && sParentNodeName != oChild.parentNode.nodeName.toLowerCase()) {
      oChild = oChild.parentNode;
    }
    return (oChild.parentNode && sParentNodeName == oChild.parentNode.nodeName.toLowerCase()?oChild.parentNode:null);
  }
  
  this.getTime = function(nMSec,bAsString) {
    // convert milliseconds to mm:ss, return as object literal or string
    var nSec = Math.floor(nMSec/1000);
    var min = Math.floor(nSec/60);
    var sec = nSec-(min*60);
    // if (min == 0 && sec == 0) return null; // return 0:00 as null
    return (bAsString?(min+':'+(sec<10?'0'+sec:sec)):{'min':min,'sec':sec});
  }

  this.getSoundByURL = function(sURL) {
    return (typeof self.soundsByURL[sURL] != 'undefined'?self.soundsByURL[sURL]:null);
  }

  this.getSoundIndex = function(sURL) {
    for (var i=self.links.length; i--;) {
      if (self.links[i].href == sURL) return i;
    }
    return -1;
  }

  this.setPageTitle = function(sTitle) {
    if (!self.config.updatePageTitle) return false;
    try {
      document.title = (sTitle?sTitle+' - ':'')+self.pageTitle;
    } catch(e) {
      // oh well
      self.setPageTitle = function() {return false;}
    }
  }

  this.events = {

    // handlers for sound events as they're started/stopped/played

    play: function() {
      pl.removeClass(this._data.oLI,this._data.className);
      this._data.className = pl.css.sPlaying;
      pl.addClass(this._data.oLI,this._data.className);
      self.setPageTitle(this._data.originalTitle);
    },

    stop: function() {
      pl.removeClass(this._data.oLI,this._data.className);
      this._data.className = '';
      this._data.oPosition.style.width = '0px';
      self.setPageTitle();
    },

    pause: function() {
      if (pl.dragActive) return false;
      pl.removeClass(this._data.oLI,this._data.className);
      this._data.className = pl.css.sPaused;
      pl.addClass(this._data.oLI,this._data.className);
      self.setPageTitle();
    },

    resume: function() {
      if (pl.dragActive) return false;
      pl.removeClass(this._data.oLI,this._data.className);
      this._data.className = pl.css.sPlaying;
      pl.addClass(this._data.oLI,this._data.className);
    },

    finish: function() {
      pl.removeClass(this._data.oLI,this._data.className);
      this._data.className = '';
      this._data.oPosition.style.width = '0px';
      // play next if applicable
      if (self.config.playNext && this._data.nIndex<pl.links.length-1) {
        pl.handleClick({target:pl.links[this._data.nIndex+1]}); // fake a click event - aren't we sneaky. ;)
      } else {
        self.setPageTitle();
      }
    },

    whileloading: function() {
      this._data.oLoading.style.width = (((this.bytesLoaded/this.bytesTotal)*100)+'%'); // theoretically, this should work.
      if (!this._data.didRefresh && this._data.metadata) {
        this._data.didRefresh = true;
        this._data.metadata.refresh();
      }
    },

    onload: function() {
      if (!this.loaded) {
		var oTemp = this._data.oLI.getElementsByTagName('a')[0];
		var oString = oTemp.innerHTML;
		var oThis = this;
		oTemp.innerHTML = oString+' <span style="font-size:0.5em"> | Load failed, d\'oh! '+(sm.sandbox.noRemote?' Possible cause: Flash sandbox is denying remote URL access.':(sm.sandbox.noLocal?'Flash denying local filesystem access':'404?'))+'</span>';
		setTimeout(function(){
		  oTemp.innerHTML = oString;
		  // pl.events.finish.apply(oThis); // load next
		},5000);
	  } else {
        if (this._data.metadata) {
          this._data.metadata.refresh();
        }
      }
    },

    whileplaying: function() {
      var d = null;
      if (pl.dragActive || !pl.config.useThrottling) {
        self.updateTime.apply(this);
        this._data.oPosition.style.width = (((this.position/self.getDurationEstimate(this))*100)+'%');
        if (this._data.metadata) {
          d = new Date();
          if (d && d-self.lastWPExec>500) {
            self.refreshMetadata(this);
            self.lastWPExec = d;
          }
        }
      } else {
        d = new Date();
        if (d-self.lastWPExec>500) {
          self.updateTime.apply(this);
          if (this._data.metadata) self.refreshMetadata(this);
          this._data.oPosition.style.width = (((this.position/self.getDurationEstimate(this))*100)+'%');
          self.lastWPExec = d;
        }
      }
    }
	
  } // events{}

  this.refreshMetadata = function(oSound) {
    // Display info as appropriate
    // console.log('refreshMetaData()');
    var index = null;
    var now = oSound.position;
    var metadata = oSound._data.metadata.data;
    for (var i=0, j=metadata.length; i<j; i++) {
      if (now >= metadata[i].startTimeMS && now <= metadata[i].endTimeMS) {
        index = i;
        break;
      }
    }
    if (index != metadata.currentItem) {
      // update
      oSound._data.oLink.innerHTML = metadata.mainTitle+' <span class="metadata"><span class="sm2_divider"> | </span><span class="sm2_metadata">'+metadata[index].title+'</span></span>';
      self.setPageTitle(metadata[index].title+' | '+metadata.mainTitle);
      metadata.currentItem = index;
    }
  }
  
  this.updateTime = function() {
    var str = self.strings['timing'].replace('%s1',self.getTime(this.position,true));
    str = str.replace('%s2',self.getTime(self.getDurationEstimate(this),true));
    this._data.oTiming.innerHTML = str;
  }

  this.getTheDamnTarget = function(e) {
    return (e.target||e.srcElement||window.event.srcElement);
  }
  
  this.withinStatusBar = function(o) {
    return (self.isChildOfClass(o,'controls')); // (self.hasClass(o,'statusbar')||self.hasClass(o,'loading')||self.hasClass(o,'position')||self.hasClass(o,'sub-track')));
  }

  this.handleClick = function(e) {
    // a sound (or something) was clicked - determine what and handle appropriately
    if (e.button == 2) {
      if (!pl.config.allowRightClick) pl.stopEvent(e);
      return (pl.config.allowRightClick); // ignore right-clicks
    }
    var o = self.getTheDamnTarget(e);
    if (self.dragActive) self.stopDrag(); // to be safe
    if (self.withinStatusBar(o)) {
      // self.handleStatusClick(e);
      return false;
    }
    if (o.nodeName.toLowerCase() != 'a') {
      o = self.getParentByNodeName(o,'a');
    }
    if (!o) {
      // not a link
      return true;
    }
    var sURL = o.getAttribute('href');
    if (!o.href || !o.href.match(/.mp3$/i)) {
      if (isIE && o.onclick) {
        return false; // IE will run this handler before .onclick(), everyone else is cool?
      }
      return true; // pass-thru for non-MP3/non-links
    }
    // sm._writeDebug('handleClick()');
    var soundURL = (o.href);
    var thisSound = self.getSoundByURL(soundURL);
    // sm._writeDebug('click: thisSound:'+thisSound);
    if (thisSound) {
      // sound already exists
      // sm._writeDebug('sound exists');
      self.setPageTitle(thisSound._data.originalTitle);
      if (thisSound == self.lastSound) {
        // ..and was playing (or paused) and isn't in an error state
		if (thisSound.readyState != 2) {
          thisSound.togglePause();
		} else {
		  sm._writeDebug('Warning: sound failed to load (security restrictions or 404)',2);
		}
      } else {
        // ..different sound
        // sm._writeDebug('sound different than last sound: '+self.lastSound.sID);
        if (self.lastSound) self.stopSound(self.lastSound);
        thisSound.togglePause(); // start playing current
      }
    } else {
      // create sound
      thisSound = sm.createSound({
        id:'mp3Sound'+(self.soundCount++),
        url:soundURL,
        onplay:self.events.play,
        onstop:self.events.stop,
        onpause:self.events.pause,
        onresume:self.events.resume,
        onfinish:self.events.finish,
        whileloading:self.events.whileloading,
        whileplaying:self.events.whileplaying,
        onload:self.events.onload
      });
      // append control template
      var oControls = self.oControls.cloneNode(true);
      o.parentNode.appendChild(oControls);
      self.soundsByURL[soundURL] = thisSound;
      // tack on some custom data
      thisSound._data = {
        oLink: o, // DOM reference within SM2 object event handlers
        oLI: o.parentNode,
        oControls: self.getElementsByClassName('controls','div',o.parentNode)[0],
        oStatus: self.getElementsByClassName('statusbar','div',o.parentNode)[0],
        oLoading: self.getElementsByClassName('loading','div',o.parentNode)[0],
        oPosition: self.getElementsByClassName('position','div',o.parentNode)[0],
        oTiming: self.getElementsByClassName('timing','div',o.parentNode)[0].getElementsByTagName('div')[0],
        nIndex: self.getSoundIndex(soundURL),
        className: self.css.sPlaying,
        originalTitle: o.innerHTML,
        metadata: null
      };
      // "Metadata"
      // if (self.getElementsByClassName('tracks',thisSound._data.oLI,'div').length) {
      if (thisSound._data.oLI.getElementsByTagName('ul').length) {
        thisSound._data.metadata = new Metadata(thisSound);
      }
      // set initial timer stuff (before loading)
      var str = self.strings['timing'].replace('%s1',self.config.emptyTime);
      str = str.replace('%s2',self.config.emptyTime);
      thisSound._data.oTiming.innerHTML = str;
      self.sounds.push(thisSound);
      if (self.lastSound) self.stopSound(self.lastSound);
      thisSound.play();
    }
    self.lastSound = thisSound; // reference for next call
    return self.stopEvent(e);
  }
  
  this.handleMouseDown = function(e) {
    // a sound link was clicked
    if (e.button == 2) {
      if (!pl.config.allowRightClick) pl.stopEvent(e);
      return (pl.config.allowRightClick); // ignore right-clicks
    }
    var o = self.getTheDamnTarget(e);
    if (!self.withinStatusBar(o)) return true;
    self.dragActive = true;
    self.lastSound.pause();
    self.setPosition(e);
    self.addEventHandler(document,'mousemove',self.handleMouseMove);
    self.addClass(self.lastSound._data.oControls,'dragging');
    // self.addEventHandler(document,'mouseup',self.stopDrag);
    self.stopEvent(e);
    return false;
  }
  
  this.handleMouseMove = function(e) {
    // set position accordingly
    if (self.dragActive) {
      if (self.config.useThrottling) {
        // be nice to CPU/externalInterface
        var d = new Date();
        if (d-self.dragExec>20) {
          self.setPosition(e);
        } else {
          window.clearTimeout(self.dragTimer);
          self.dragTimer = window.setTimeout(function(){self.setPosition(e)},20);
        }
        self.dragExec = d;
      } else {
        // oh the hell with it
        self.setPosition(e);
      }
    } else {
      self.stopDrag();
    }
	return false;
  }
  
  this.stopDrag = function(e) {
    if (self.dragActive) {
      self.removeClass(self.lastSound._data.oControls,'dragging');
      self.removeEventHandler(document,'mousemove',self.handleMouseMove);
      // self.removeEventHandler(document,'mouseup',self.stopDrag);
      if (!pl.hasClass(self.lastSound._data.oLI,self.css.sPaused)) {
        self.lastSound.resume();
      }
      self.dragActive = false;
      self.stopEvent(e);
      return false;
    }
  }
  
  this.handleStatusClick = function(e) {
    self.setPosition(e);
    if (!pl.hasClass(self.lastSound._data.oLI,self.css.sPaused)) self.resume();
    return self.stopEvent(e);
  }
  
  this.stopEvent = function(e) {
   if (typeof e != 'undefined' && typeof e.preventDefault != 'undefined') {
      e.preventDefault();
    } else if (typeof event != 'undefined' && typeof event.returnValue != 'undefined') {
      event.returnValue = false;
    }
    return false;
  }
 
  this.setPosition = function(e) {
    // called from slider control
    var oThis = self.getTheDamnTarget(e);
    var oControl = oThis;
    while (!self.hasClass(oControl,'controls') && oControl.parentNode) {
      oControl = oControl.parentNode;
    }
    var oSound = self.lastSound;
    var x = parseInt(e.clientX);
    // play sound at this position
    var nMsecOffset = Math.floor((x-self.getOffX(oControl)-4)/(oControl.offsetWidth)*self.getDurationEstimate(oSound));
    if (!isNaN(nMsecOffset)) nMsecOffset = Math.min(nMsecOffset,oSound.duration);
    if (!isNaN(nMsecOffset)) oSound.setPosition(nMsecOffset);
  }

  this.stopSound = function(oSound) {
    sm._writeDebug('stopping sound: '+oSound.sID);
    soundManager.stop(oSound.sID);
    soundManager.unload(oSound.sID);
  }

  this.getDurationEstimate = function(oSound) {
    return (!oSound._data.metadata || !oSound._data.metadata.data.givenDuration?oSound.durationEstimate:oSound._data.metadata.data.givenDuration);
  }

  this.init = function() {
    sm._writeDebug('pagePlayer.init()');
    var oLinks = document.getElementsByTagName('a');
    // grab all links, look for .mp3
    var foundItems = 0;
    for (var i=0; i<oLinks.length; i++) {
      if (oLinks[i].href.match(/.mp3$/i)) {
        self.links[self.links.length] = oLinks[i];
        self.addClass(oLinks[i],self.css.sDefault); // add default CSS decoration
        foundItems++;
      }
    }
    if (foundItems>0) {
      var oTiming = document.getElementById('sm2_timing');
      self.strings['timing'] = oTiming.innerHTML;
      oTiming.innerHTML = '';
      oTiming.id = '';
      self.addEventHandler(document,'click',self.handleClick);
      self.addEventHandler(document,'mousedown',self.handleMouseDown);
      self.addEventHandler(document,'mouseup',self.stopDrag);
    }
    sm._writeDebug('pagePlayer.init(): Found '+foundItems+' relevant items.');
  }

var Metadata = function(oSound) { // self.sounds[]
  var self = this;
  var oLI = oSound._data.oLI;
  var o = oLI.getElementsByTagName('ul')[0];
  var oItems = o.getElementsByTagName('li');
  var oTemplate = document.createElement('div');
  oTemplate.innerHTML = '<span>&nbsp;</span>';
  oTemplate.className = 'annotation';
  var oTemplate2 = document.createElement('div');
  oTemplate2.innerHTML = '<span>&nbsp;</span>';
  oTemplate2.className = 'annotation alt';

  var oTemplate3 = document.createElement('div');
  oTemplate3.className = 'note';

  this.totalTime = 0;
  this.strToTime = function(sTime) {
    var segments = sTime.split(':');
    var seconds = 0;
    for (var i=segments.length; i--;) {
      seconds += parseInt(segments[i])*Math.pow(60,segments.length-1-i,10); // hours, minutes
    }
    return seconds;
  }
  this.data = [];
  this.data.givenDuration = null;
  this.data.currentItem = null;
  this.data.mainTitle = oSound._data.oLink.innerHTML;
  for (var i=0; i<oItems.length; i++) {
    this.data[i] = {
      o: null,
      title: oItems[i].getElementsByTagName('p')[0].innerHTML,
      startTime: oItems[i].getElementsByTagName('span')[0].innerHTML,
      startSeconds: self.strToTime(oItems[i].getElementsByTagName('span')[0].innerHTML.replace(/[()]/g,'')),
      duration: 0,
      durationMS: null,
      startTimeMS: null,
      endTimeMS: null,
      oNote: null
    }
  }
  var oDuration = pl.getElementsByClassName('duration','div',oLI);
  this.data.givenDuration = (oDuration.length?self.strToTime(oDuration[0].innerHTML)*1000:0);
  for (i=0; i<this.data.length; i++) {
    this.data[i].duration = parseInt(this.data[i+1]?this.data[i+1].startSeconds:(self.data.givenDuration?self.data.givenDuration:oSound.durationEstimate)/1000)-this.data[i].startSeconds;
    this.data[i].startTimeMS = this.data[i].startSeconds*1000;
    this.data[i].durationMS = this.data[i].duration*1000;
    this.data[i].endTimeMS = this.data[i].startTimeMS+this.data[i].durationMS;
    // console.log('start/end MS: '+this.data[i].startTimeMS+'/'+this.data[i].endTimeMS);
    this.totalTime += this.data[i].duration;
  }
  // make stuff
  this.createElements = function() {
    var oFrag = document.createDocumentFragment();
    var oNode = null;
    var oNodeSpan = null;
    var oNode2 = null;
    for (var i=0; i<self.data.length; i++) {
      oNode = (i%2==0?oTemplate:oTemplate2).cloneNode(true);
      oNodeSpan = oNode.getElementsByTagName('span')[0];
      oNode.rel = i;
      self.data[i].o = oNode;
      oNode2 = oTemplate3.cloneNode(true);
      if (i%2==0) oNode2.className = 'note alt';
      oNode2.innerHTML = this.data[i].title;
      // pl.addEventHandler(oNode,'mouseover',self.mouseover);
      // pl.addEventHandler(oNode,'mouseout',self.mouseout);
      // evil old-skool event handlers
      oNode.onmouseover = self.mouseover;
      oNode.onmouseout = self.mouseout;
      this.data[i].oNote = oNode2;
      oSound._data.oControls.appendChild(oNode2);
      oFrag.appendChild(oNode);
      // oFrag.appendChild(oNode2);
    }
    self.refresh();
    oSound._data.oStatus.appendChild(oFrag);
  }

  this.refresh = function() {
    // console.log('refresh()');
    var offset = 0;
    var relWidth = null;
    var duration = (self.data.givenDuration?self.data.givenDuration:oSound.durationEstimate);
    for (var i=0; i<self.data.length; i++) {
      if (duration) {
        relWidth = (((self.data[i].duration*1000)/duration)*100);
        // self.data[i].o.style.width = (Math.max(0,relWidth)+'%');
        self.data[i].o.style.left = (offset?offset+'%':'-2px'); // (offset?(offset/self.totalTime)+'%':'0px');
        self.data[i].oNote.style.left = (offset?offset+'%':'0px');
        offset += relWidth; // parseInt(self.data[i].duration);
      }
    }
  }

  this.mouseover = function(e) {
    self.data[this.rel].oNote.style.visibility = 'hidden';
    self.data[this.rel].oNote.style.display = 'inline-block';
    self.data[this.rel].oNote.style.marginLeft = -parseInt(self.data[this.rel].oNote.offsetWidth/2)+'px';
    self.data[this.rel].oNote.style.visibility = 'visible';
  }

  this.mouseout = function() { 
    // console.log('mouseout()');
    self.data[this.rel].oNote.style.display = 'none';
  }

  this.createElements();
  this.refresh();
  
}

  this.init();
}


var pagePlayer = null;

soundManager.debugMode = (window.location.href.toString().match(/debug=1/i)?true:false); // enable with #debug=1 for example

soundManager.url = '../../soundmanager2.swf'; // path to movie

soundManager.onload = function() {
  // soundManager.createSound() etc. may now be called
  pagePlayer = new PagePlayer();
}
