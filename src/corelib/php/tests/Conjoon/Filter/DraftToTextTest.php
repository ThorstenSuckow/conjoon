<?php
/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
 * licensing@conjoon.org
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
 * @see Conjoon_Filter_DraftToText
 */
require_once 'Conjoon/Filter/DraftToText.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Filter
 * @subpackage UnitTests
 * @group      Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_DraftToTextTest extends PHPUnit_Framework_TestCase {

    /**
     * Conjoon_Filter_DraftToText object
     *
     * @var Conjoon_Filter_DraftToText
     */
    protected $_filter;

    /**
     * @var string
     */
    protected $_result;

    /**
     * @var string
     */
    protected $_input;

    /**
     * Creates a new Conjoon_Filter_DraftToText object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_input = "<blockquote>&gt;this is a <br />" .
                        "&gt; test yo <span>whoah</span></blockquote>";

        $this->_result = ">this is a \n" .
                         "> test yo whoah";

        $this->_filter = new Conjoon_Filter_DraftToText();
    }


    /**
     * Ensures everything works as expected
     *
     * @return void
     */
    public function testFilter()
    {
        $this->assertEquals(
            $this->_result,
            $this->_filter->filter($this->_input)
        );
    }

}