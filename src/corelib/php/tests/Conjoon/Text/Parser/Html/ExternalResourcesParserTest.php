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


namespace Conjoon\Text\Parser\Html;

/**
 * @see \Conjoon\Text\Parser\Html\ExternalResourcesParser
 */
require_once 'Conjoon/Text/Parser/Html/ExternalResourcesParser.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ExternalResourcesParserTest extends \PHPUnit_Framework_TestCase {


    protected $parser = null;

    protected $input = null;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->parser = new ExternalResourcesParser;

        $this->input = array(
            array(
                'input' => "sdsd dsdsd <a href='bla'> test <img src='http://www.image.test' /></a>",
                'output' => true
            ),
            array(
                'input' => " ",
                'output' => false
            ),
            array(
                'input' => "<head><link rel='title' rel='previous' /></head><body> sdsd dsdsd <a href='bla'> test </a></body>",
                'output' => false
            ),
            array(
                'input' => "<head><link rel='stylesheet' href='previous' /><link rel='title' rel='previous' /></head><body> sdsd dsdsd <a href='bla'> test </a></body>",
                'output' => true
            ),
            array(
                'input' => "<head><link type='text/css' src='src' /><link rel='title' rel='previous' /></head><body> sdsd dsdsd <a href='bla'> test </a></body>",
                'output' => true
            ),
            array(
                'input' => "wegwgewgege <embed src='dsdsd' /> gwe ge",
                'output' => true
            ),
            array(
                'input' => " weggeg<iframe src='dsdsd' /> fwfqwfw",
                'output' => true
            ),
            array(
                'input' => " gewge g e <body> <object src='dsdsd' /> wge ge <tr></r>",
                'output' => true
            ),
            array(
                'input' => " gewge g e <body> <obhjject src='dsdsd' /> wge ge <tr></r>",
                'output' => false
            ),
            array(
                'input' => "<video src='dsdsd' />",
                'output' => true
            ),
            array(
                'input' => "<audio src='dsdsd' />",
                'output' => true
            ),
            array(
                'input' => "<source src='dsdsd' />",
                'output' => true
            ),
            array(
                'input' => "<track src='dsdsd' />",
                'output' => true
            )
        );

    }

    /**
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->parser);
    }

// +---------------------------------------------------------------------------
// | Tests
// +---------------------------------------------------------------------------

    /**
     * Ensure everything works as expected.
     */
    public function testParse()
    {
        foreach ($this->input as $test) {

            $result = $this->parser->parse($test['input']);

            $this->assertTrue(
                $result instanceof
                \Conjoon\Text\Parser\Html\Result\ExternalResourcesParseResult
            );
            $data = $result->getData();
            $this->assertTrue(array_key_exists('externalResources', $data));
            $this->assertSame($test['output'], $data['externalResources']);
        }
    }


}
