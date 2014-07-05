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
 * @see Zend_Filter_Input
 */
require_once 'Zend/Filter/Input.php';

/**
 * A custom filter implementation that allows for switching context - either
 * 'update' or 'create' - to autmoatically prepare and validate data before any
 * operation with it happens.
 *
 * @uses Zend_Filter_Input
 * @package    Conjoon_Filter
 * @category   Filter
 */
class Conjoon_Filter_Input extends Zend_Filter_Input {

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
     * @var array
     */
    protected $_dontRecurseFilter = array();

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

        $this->addFilterPrefixPath('Conjoon_Filter', 'Conjoon/Filter');
        $this->addValidatorPrefixPath('Conjoon_Validate', 'Conjoon/Validate');

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
                if (!isset($this->_validatorRules[$key]['presence'])) {
                    $this->_validatorRules[$key]['presence'] = 'required';
                }
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

    /**
     * Override to consider _dontRecurseFilter - where field names are stored
     * where filters should not be operated on array values.
     *
     * @param array $filterRule
     * @return void
     */
    protected function _filterRule(array $filterRule)
    {
        $field = $filterRule[self::FIELDS];
        if (!array_key_exists($field, $this->_data)) {
            return;
        }
        if (!in_array($field, $this->_dontRecurseFilter) && is_array($this->_data[$field])) {
            foreach ($this->_data[$field] as $key => $value) {
                $this->_data[$field][$key] = $filterRule[self::FILTER_CHAIN]->filter($value);
            }
        } else {
            $this->_data[$field] =
                $filterRule[self::FILTER_CHAIN]->filter($this->_data[$field]);
        }

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
