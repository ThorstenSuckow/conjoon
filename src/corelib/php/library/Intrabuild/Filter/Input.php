<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
 * @see Zend_Filter_Input
 */
require_once 'Zend/Filter/Input.php';

/**
 * A custom filter implementation that allows for switching context - either
 * 'update' or 'create' - to autmoatically prepare and validate data before any
 * operation with it happens.
 *
 * @uses Zend_Filter_Input
 * @package    Intrabuild_Filter
 * @category   Filter
 */
class Intrabuild_Filter_Input extends Zend_Filter_Input {

    /**
     * @var string
     */
    const CONTEXT_CREATE =  'create';

    /**
     * @var string
     */
    const CONTEXT_UPDATE =  'update';

    /**
     * @var string
     */
    const CONTEXT_DELETE =  'delete';

    /**
     * @var string
     */
    const CONTEXT_RESPONSE =  'response';

    /**
     * @var string
     */
    protected $_context = '';

    /**
     * @var array
     */
    protected $_validators = array();

    /**
     * @var array
     */
    protected $_filters = array();

    /**
     * An associative array containing all fields which need to be available
     * for each operation. Override this to adjust your filter bahavior.
     * Example:
     *
     * $_presence = array(
     *      'update' => array('field1', 'field2', 'field3'),
     *      'create' => array('field4')
     * );
     *
     * The above array would tell the filter that field1, field2 and field3 have to
     * be available (Meta command PRESENCE) when data is updated (i.e. edited),
     * while only field4 has to be present when data is created.
     *
     * @var array
     */
    protected $_presence = array();

    /**
     * Constructor.
     *
     * @param array $filterRules
     * @param array $validatorRules
     * @param array $data       OPTIONAL
     * @param array $options    OPTIONAL
     */
    public function __construct(array $data, $context, array $options = null)
    {
        if ($this->_defaultEscapeFilter != null) {
            $options[self::ESCAPE_FILTER] = $this->_defaultEscapeFilter;
            $this->_defaultEscapeFilter = null;
        }

        $this->_context = $context;

        if ($options) {
            $this->setOptions($options);
        }

        $this->addFilterPrefixPath('Intrabuild_Filter', 'Intrabuild/Filter');
        $this->addValidatorPrefixPath('Intrabuild_Validate', 'Intrabuild/Validate');

        if ($data) {
            $this->setData($data);
        }

        $this->_init();

        $this->_preProcess();
    }

    protected function _init()
    {

    }

    /**
     * Overrides parent implementation by adding a call to _preProcess
     * before the data gets processed.
     *
     * @return Zend_Filter_Input
     * @throws Zend_Filter_Exception
     */
    public function process()
    {
        if ($this->_processed === true) {
            return;
        }

        $this->_preProcess();
        return parent::process();
    }


    /**
     *
     */
    protected function _preProcess()
    {
        // presence => required
        $presence =& $this->_presence[$this->_context];
        $this->_validatorRules = (array)$this->_validators;
        $this->_filterRules    = (array)$this->_filters;

        $not = array();
        foreach ($this->_filterRules as $key => $rule) {
            if (!in_array($key, $presence)) {
                $not[] = $key;
            }
        }
        for ($i = 0, $len = count($not); $i < $len; $i++) {
            unset($this->_filterRules[$not[$i]]);
        }

        $not = array();
        foreach ($this->_validatorRules as $key => $rule) {
            if (!in_array($key, $presence)) {
                $not[] = $key;
            } else {
                $this->_validatorRules[$key]['presence'] = 'required';
            }
        }
        for ($i = 0, $len = count($not); $i < $len; $i++) {
            unset($this->_validatorRules[$not[$i]]);
        }
    }

    protected function _adjustValidators()
    {

    }


    protected function _filter()
    {
        parent::_filter();
        $this->_adjustValidators();
    }

    public function getProcessedData()
    {
        $this->process();

        $data = array();

        $presence =& $this->_presence[$this->_context];

        for ($i = 0, $len = count($presence); $i < $len; $i++) {
            $data[$presence[$i]] = $this->$presence[$i];
        }

        return $data;
    }


}
