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
 * @see Conjoon_Filter_EmailRecipients
 */
require_once 'Conjoon/Filter/EmailRecipients.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Filter
 * @subpackage UnitTests
 * @group      Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_EmailRecipientsTest extends PHPUnit_Framework_TestCase {

    protected $filter;

    protected $input;

    public function setUp()
    {
        parent::setUp();

        $this->input = array(array(
            'input' => array(
                "\"Thorsten Suckow-Homberg\" <tsuckow@conjoon.org>, yo@mtv.com",
                "\"Pit Bull\" <pit@doggydog.com>"
            ),
            'output' => array(
                array("tsuckow@conjoon.org", "Thorsten Suckow-Homberg"),
                array("yo@mtv.com"),
                array("pit@doggydog.com", "Pit Bull"),
            )
        ));

        $this->filter = new Conjoon_Filter_EmailRecipients();
    }

    /**
     * Ensure everything works as expected.
     */
    public function testOk()
    {
        foreach ($this->input as $values) {

            $this->assertEquals(
                $values['output'],
                $this->filter->filter($values['input'])
            );

        }
    }

}
