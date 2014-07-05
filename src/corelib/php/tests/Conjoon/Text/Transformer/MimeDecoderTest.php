<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */


/**
 * @see Conjoon_Text_Transformer_MimeDecoder
 */
require_once 'Conjoon/Text/Transformer/MimeDecoder.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Transformer_MimeDecoderTest extends PHPUnit_Framework_TestCase {

    protected $_transformer = null;

    protected $_inputs = array();

    /**
     *
     * @return void
     */
    public function setUp()
    {
        $this->_transformer = new Conjoon_Text_Transformer_MimeDecoder();

        $this->_inputs = array(
            "=?windows-1252?Q?Fwd=3A_Exklusiv_f=FCr_Analytics=2DNutzer=3A_=80_100=2C=2D_Start?=\n\t"
            . "=?windows-1252?Q?guthaben_f=FCr_Google_AdWords?="
            => "Fwd: Exklusiv für Analytics-Nutzer: € 100,- Startguthaben für Google AdWords",
            "=?windows-1252?Q?1Aktualisierte_Einladung=3A_Employment_Options_at_conjoo?=\n\t"
            . "=?windows-1252?Q?n_=40_Di_8=2E_Sep=2E_17=3A00_=96_18=3A00_=28Thorsten_Suckow=2DHomberg=29?="
            => "1Aktualisierte Einladung: Employment Options at conjoon @ Di 8. Sep. 17:00 – 18:00 (Thorsten Suckow-Homberg)",
            "2Aktualisierte Einladung: Employment Options at conj=?windows-1252?Q?oon_=40_Di_8=2E_Sep=2E_17=3A00_=96_18=3A00_=28Thorsten_Suckow=2DHomberg=29?="
            => "2Aktualisierte Einladung: Employment Options at conjoon @ Di 8. Sep. 17:00 – 18:00 (Thorsten Suckow-Homberg)",
            "=?utf-8?q?Exklusiv_f=C3=BCr_Analytics-Nutzer=3A_=E2=82=AC_100=2C-_Startgu?=\n\t"
            . "=?utf-8?q?thaben_f=C3=BCr_Google_AdWords?="
            => "Exklusiv für Analytics-Nutzer: € 100,- Startguthaben für Google AdWords",
            "=?utf-8?q?Exklusiv_f=C3=BCr_Analytics-Nutzer=3A_=E2=82=AC_100=2C-_Startgu?= thaben f&uuml;r Google AdWords"
            => "Exklusiv für Analytics-Nutzer: € 100,- Startguthaben f&uuml;r Google AdWords",
            "=?windows-1252?Q?Fwd=3A_Aktualisierte_Einladung=3A_Employment_Options_at_?=\n\t"
            . "=?windows-1252?Q?conjoon_=40_Di_8=2E_Sep=2E_17=3A00_=96_18=3A00_=28Thorsten_Suckow=2DHomberg?=\n\t"
            . "=?windows-1252?Q?=29?="
            => "Fwd: Aktualisierte Einladung: Employment Options at conjoon @ Di 8. Sep. 17:00 – 18:00 (Thorsten Suckow-Homberg)",
            "=?ISO-8859-15?Q?=DC=DCPIJM=D6N=DF=DF=DF?="
            => "ÜÜPIJMÖNßßß",
            ""
            => ""

        );

    }

    /**
     *
     * @return void
     */
    public function tearDown()
    {

    }

// +---------------------------------------------------------------------------
// | Tests
// +---------------------------------------------------------------------------

    /**
     * Ensure everythign works as expected.
     *
     */
    public function testTransform()
    {

        foreach ($this->_inputs as $input => $output) {
            $this->assertEquals($output, $this->_transformer->transform($input));
        }
    }

    /**
     * @expectedException Conjoon_Argument_Exception
     */
    public function testInvalidArgument()
    {
        $this->_transformer->transform(array());
    }


}
