<!--
 conjoon
 (c) 2002-2012 siteartwork.de/conjoon.org
 licensing@conjoon.org

 $Author$
 $Id$
 $Date$
 $Revision$
 $LastChangedDate$
 $LastChangedBy$
 $URL$
-->

<h3>Specifying the document path</h3>
<p>
 conjoon does not necessarily need to be installed in the document root of your webserver,
 and can be accessed from any folder that is within your server's document root.<br />
 However, conjoon needs to know in which folder it was installed.
 <br />
 For example, if you set up your webserver so that conjoon is accessed over the address<br />
 <span style="font-family:Courier New">http://www.domain.tld/path/to/conjoon</span><br />
 the document path has to be set to <br />
 <span style="font-family:Courier New">path/to/conjoon</span><br />
 <br />
 If, however, conjoon is truly accessed in your document root,
 (<span style="font-family:Courier New">http://www.domain.tld/</span>)
 the document path has to be set to<br />
 <span style="font-family:Courier New">/</span><br />
 <br />
 The installation procedure will try to determine the correct path your your conjoon installation.<br />
 Only change this value if you know what you are doing, otherwise conjoon might not work.
 <br />
 <br />
 <b>NOTE:</b><br />
 If you leave this field empty, the installation procedure will assume that conjoon is
 accessed directly over the document root.
 <br />
 <br />
 <br />

 <input style="width:700px" type="text" name="doc_path" value="<?php echo $_SESSION['doc_path']; ?>" />
 <input type="hidden" name="doc_path_post" value="1" />
</p>