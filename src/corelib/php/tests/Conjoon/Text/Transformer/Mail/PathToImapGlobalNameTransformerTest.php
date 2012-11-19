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
 * @see Conjoon_Text_Transformer_Mail_PathToImapGlobalNameTransformer
 */
require_once 'Conjoon/Text/Transformer/Mail/PathToImapGlobalNameTransformer.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Transformer_Mail_PathToImapGlobalNameTransformerTest
    extends PHPUnit_Framework_TestCase {

    protected $_transformer = null;

    protected $_transformerNoTail = null;

    protected $_inputs = array();

    protected $_inputsNoTail = array();

    /**
     *
     * @return void
     */
    public function setUp()
    {
        $this->_transformer =
            new Conjoon_Text_Transformer_Mail_PathToImapGlobalNameTransformer(
                array('delimiter' => '.')
            );

        $this->_transformerNoTail =
            new Conjoon_Text_Transformer_Mail_PathToImapGlobalNameTransformer(
                array('delimiter' => '.', 'popTail' => true)
            );

        $this->_inputs = array(
            "/INBOX/[Merge] Test/Messages"
            => "INBOX.[Merge] Test.Messages"
        );

        $this->_inputsNoTail = array(
            "/INBOX/[Merge] Test/Messages"
            => "INBOX.[Merge] Test"

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
     * @expectedException Conjoon_Argument_Exception
     */
    public function testConstructException()
    {
        new Conjoon_Text_Transformer_Mail_PathToImapGlobalNameTransformer();
    }

    /**
     * @expectedException Conjoon_Text_Transformer_Exception
     */
    public function testPathEqualsSlash()
    {
        $this->_transformer->transform('/');
    }

    /**
     * @expectedException Conjoon_Text_Transformer_Exception
     */
    public function testPathEqualsDelimiter()
    {
        $this->_transformer->transform('.');
    }

    /**
     * Ensure everythign works as expected.
     *
     */
    public function testTransformWithTail()
    {

        foreach ($this->_inputs as $input => $output) {
            $this->assertEquals($output, $this->_transformer->transform($input));
        }
    }

    /**
     * Ensure everythign works as expected.
     *
     */
    public function testTransformWithoutTail()
    {

        foreach ($this->_inputsNoTail as $input => $output) {
            $this->assertEquals($output, $this->_transformerNoTail->transform($input));
        }
    }

}
