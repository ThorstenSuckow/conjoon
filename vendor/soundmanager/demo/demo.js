// DEMO/example code only - not needed for general use.

// Detect success/fail of SM2 loading, for warning purposes on demo page, and some utility functions

soundManager.consoleOnly = false; // for demo page, allow debug output to be written inline even when console is available (for help in troubleshooting)

soundManager.onload = function() {
  // success
  checkConsole();
  soundManager.defaultOptions.autoLoad = false; // autoload = true causes crash for IE, WTF!?
  soundManager.createSound('button0','demo/button-0.mp3');          // create sound (simple method)
  soundManager.createSound({id:'button1',url:'demo/button-1.mp3'}); // (more flexible object literal method)
  soundManager.createSound({id:'clickLow',url:'demo/click-low.mp3'});
  soundManager.createSound({id:'clickHigh',url:'demo/click-high.mp3'});
}

soundManager.onerror = function() {
  // failed to load
  var o = document.getElementById('sm2-support');
  o.innerHTML = '<p class="error"><strong>Warning: SoundManager failed to load/initialize.</strong> May be due to Flash security restrictions. Refer to debug output when viewing demos or <a href="#debug-output">live on this page</a> for troubleshooting.</p>';
  o.style.display = 'block';
  document.getElementById('demo-list').className = 'debug-only';
  checkConsole();
}

function checkConsole() {
  if (soundManager.useConsole && soundManager._hasConsole) {
    if (soundManager.useConsoleOnly) {
      document.getElementById('soundmanager-debug').innerHTML = '[ Firebug/console.log()-compatible debug console support detected - refer to that console for output ]';
    } else {
      soundManager._writeDebug('<strong>Note:</strong> Console support has been detected. Debug output is also being echoed to the console.',1);
    }
  }
}

// domain check fix for when viewing on local file system vs. web server (links point to open-ended directory, no index.html)

function checkDomain(oLink,useHash) {
  var o = oLink.toString();
  if ((!document.domain || !window.location.protocol.match(/http/i)) && o.indexOf('index.html')==-1) oLink.href=(useHash?o.substr(0,o.lastIndexOf('#')):o)+'index.html'+(useHash?(o.substr(o.lastIndexOf('#'))):'');
}

var lastLanguage = 'whatis-plain-english';

function chooseLanguage(oLink) {
  var o = oLink.hash.substr(1);
  if (lastLanguage != o) soundManager.play('click'+(o.match(/english/)?'High':'Low'));
  document.getElementById('lang-tabs').className = 'tabs '+o;
  document.getElementById('whatis').className = o;
  lastLanguage = o;
  return false;
}

function setStyle(n) {
  var isSafari = (navigator.appVersion.match(/safari/i));
  var css = (document.styleSheets && !isSafari)?document.styleSheets:document.getElementsByTagName('head')[0].getElementsByTagName('link');
  for (var i=css.length; i--;) {
    css[i].disabled = (i!=n?'disabled':'');
  }
}

var activeTheme = 1;

function toggleTheme(e) {
  activeTheme = !activeTheme;
  if (soundManager._didInit) soundManager.play('button'+(activeTheme?1:0));
  setStyle(activeTheme);
  if (e && e.preventDefault) {
    e.preventDefault();
  } else if (e && typeof e.cancelBubble != 'undefined') {
    e.cancelBubble = true;
  }
}

function timeCheck() {
  var hour = new Date().getHours();
  if (hour<8 || hour>17) toggleTheme(); // night time
}

timeCheck();

function doStats() {
  if (document.domain && document.domain.match(/schillmania.com/i)) re_('u8v2l-jvr8058c6n');
}

window.onload = function() {
  var web2Speak = '#whatis-web-20-speak';
  if (window.location.href.indexOf(web2Speak)+1) chooseLanguage(window.location);
}