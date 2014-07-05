<!--
 conjoon
 (c) 2007-2014 conjoon.org
 licensing@conjoon.org

 conjoon
 Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as
 published by the Free Software Foundation, either version 3 of the
 License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Affero General Public License for more details.

 You should have received a copy of the GNU Affero General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.

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

 <?php echo conjoon_configInfoSnippet('Document path/Base url', 'environment.base_url'); ?>

 <input type="hidden" name="doc_path_post" value="1" />
</p>