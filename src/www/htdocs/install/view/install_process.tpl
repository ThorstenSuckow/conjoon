<!--
 conjoon
 (c) 2007-2015 conjoon.org
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

<script type="text/javascript">

    function updateProgressNote(txt) {
        if (txt) {
            document.getElementById('progress_info').innerHTML = txt;
        } else {
            document.getElementById('progress_info').innerHTML = "Please wait...";
        }

    }

</script>

<div class="info_box">
<span id="progress_info">Please wait...</span>
</div>

<iframe name="install_frame" style="border:0;width:100%;height:500px" src="./index.php?action=install_chunk_1"></iframe>