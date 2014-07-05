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
 * @see Conjoon_Filter_Input
 */
require_once 'Conjoon/Filter/Input.php';

/**
 * @see Conjoon_Filter_Raw
 */
require_once 'Conjoon/Filter/Raw.php';

/**
 * An input-filter class defining all validators and filters needed when
 * processing input data for mutating or creating Email-Accounts.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Service_Twitter_Account_Filter_Account extends Conjoon_Filter_Input {

    /**
     * @const string CONTEXT_UPDATE_REQUEST
     */
    const CONTEXT_UPDATE_REQUEST = 'update_request';

    protected $_presence = array(
        'create' => array(
            'name',
            'oauthToken',
            'oauthTokenSecret',
            'updateInterval',
            'twitterId'
        ),
        'delete' => array(
            'data'
        ),
        'update_request' => array(
            'data'
        ),
        'update' => array(
            'updateInterval',
            'id'
        )
    );

    protected $_filters = array(
        'name' => array(
            'StringTrim'
         ),
        'oauthToken' => array(
            'StringTrim'
         ),
         'oauthTokenSecret' => array(
            'StringTrim'
         ),
         'twitterId' => array(
            'StringTrim'
         ),
         'updateInterval' => array(
            'Int'
         ),
         'data' => array(
            array('ExtDirectWriterFilter', 'accounts')
            // additional filters actually set in _init depending on the context
         ),
         'id' => array(
            'Int'
         )
    );

    protected $_validators = array(
        'name' => array(
            'allowEmpty' => false
         ),
        'oauthToken' => array(
            'allowEmpty' => false
         ),
         'oauthTokenSecret' => array(
            'allowEmpty' => false
         ),
        'updateInterval' => array(
            'allowEmpty' => true,
            'default'    => 60000
         ),
         'twitterId' => array(
            'allowEmpty' => false
         ),
         'id' => array(
            'allowEmpty' => false,
            array('GreaterThan', 0)
         ),
         'data' => array(
            'allowEmpty' => false
         )
    );

    protected $_dontRecurseFilter = array(
        'data'
    );

    protected function _init()
    {
        $this->_defaultEscapeFilter = new Conjoon_Filter_Raw();

        if ($this->_context == self::CONTEXT_DELETE) {
            $this->_filters['data'][] = 'PositiveArrayValues';
        } else if ($this->_context == self::CONTEXT_UPDATE) {

            $this->_validators['updateInterval'] = array(
                'allowEmpty' => true,
                'presence'   => 'optional'
            );
        }
    }

    public function getProcessedData()
    {
        $data = parent::getProcessedData();

        if ($this->_context == self::CONTEXT_UPDATE) {
            if ($data['updateInterval'] === NULL) {
                unset($data['updateInterval']);
            }

            if (isset($data['updateInterval'])) {
                $v = $data['updateInterval'];
                $data['update_interval'] = $v;
            }
        }

        return $data;
    }

}