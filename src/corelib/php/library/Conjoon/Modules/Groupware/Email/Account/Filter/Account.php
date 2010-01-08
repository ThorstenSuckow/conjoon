<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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
 * @see Conjoon_Filter_Input
 */
require_once 'Conjoon/Filter/Input.php';

/**
 * @see Conjoon_Filter_FormBoolToInt
 */
require_once 'Conjoon/Filter/FormBoolToInt.php';

/**
 * @see Zend_Validate_Hostname
 */
require_once 'Zend/Validate/Hostname.php';

/**
 * An input-filter class defining all validators and filters needed when
 * processing input data for mutating or creating Email-Accounts.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Account_Filter_Account extends Conjoon_Filter_Input {

    protected $_defaultEscapeFilter = 'StringTrim';

    protected $_presence = array(
        'delete' =>
            array(
                'id'
            )
        ,
        'update' =>
            array(
                'id',
                'name',
                'address',
                'replyAddress',
                'isStandard',
                'protocol',
                'serverInbox',
                'serverOutbox',
                'usernameInbox',
                'usernameOutbox',
                'userName',
                'isOutboxAuth',
                'passwordInbox',
                'passwordOutbox',
                'signature',
                'isSignatureUsed',
                'portInbox',
                'portOutbox',
                'isCopyLeftOnServer',
                'inboxConnectionType',
                'outboxConnectionType',

        ),
        'create' =>
            array(
                'name',
                'protocol',
                'address',
                'serverInbox',
                'serverOutbox',
                'usernameInbox',
                'usernameOutbox',
                'userName',
                'isOutboxAuth',
                'passwordInbox',
                'passwordOutbox',
                'inboxConnectionType',
                'outboxConnectionType'
        )
    );

    protected $_filters = array(
        'id' => array(
            'Int'
         ),
        'name' => array(
            'StringTrim'
         ),
        'address' => array(
            'StringTrim'
         ),
        'replyAddress' => array(
            'StringTrim'
         ),
        'isStandard' => array(
            'FormBoolToInt'
        ),
        'protocol' => array(
            'StringTrim',
            'StringToUpper'
        ),
        'serverInbox' => array(
            'StringTrim'
         ),
        'serverOutbox' => array(
            'StringTrim'
         ),
        'usernameInbox' => array(
            'StringTrim'
         ),
        'usernameOutbox' => array(
            'StringTrim'
         ),
        'userName' => array(
            'StringTrim'
        ),
        'isOutboxAuth' => array(
            'FormBoolToInt'
        ),
        'passwordInbox' => array(
            'StringTrim'
         ),
        'passwordOutbox' => array(
            'StringTrim'
        ),
        'signature' => array(
            'StringTrim'
        ),
        'isSignatureUsed' => array(
            'FormBoolToInt'
        ),
        'portInbox' => array(
            'Int'
        ),
        'portOutbox' => array(
            'Int'
        ),
        'inboxConnectionType' => array(
            'StringTrim'
        ),
        'outboxConnectionType' => array(
            'StringTrim'
        ),
        'isCopyLeftOnServer' => array(
            'FormBoolToInt'
        )
    );

    protected $_validators = array(
        'id' => array(
            'allowEmpty' => false,
            array('GreaterThan', 0)
         ),
        'name' => array(
            'allowEmpty' => false
         ),
        'address' => array(
            'EmailAddress',
            'allowEmpty' => false,
            array('EmailAddress', Zend_Validate_Hostname::ALLOW_ALL)
         ),
        'replyAddress' => array(
            'EmailAddress',
            'allowEmpty' => true,
            array('EmailAddress', Zend_Validate_Hostname::ALLOW_ALL)
         ),
        'isStandard' => array(
            'allowEmpty' => true,
            'default'    => 0
        ),
        'protocol' => array(
            'allowEmpty' => true,
            'default'    => 'POP3'
        ),
        'serverInbox' => array(
            'allowEmpty' => false,
            array('Hostname', Zend_Validate_Hostname::ALLOW_ALL)
         ),
        'serverOutbox' => array(
            'allowEmpty' => false,
            array('Hostname', Zend_Validate_Hostname::ALLOW_ALL)
         ),
        'usernameInbox' => array(
            'allowEmpty' => false
         ),
        'usernameOutbox' => array(
            'allowEmpty' => false
         ),
        'userName'           => array(
            'allowEmpty' => false
         ),
        'isOutboxAuth' => array(
            'allowEmpty' => true,
            'default'    => 0
        ),
        'passwordInbox'      => array(
            'allowEmpty' => false
        ),
        'passwordOutbox'     => array(
            'allowEmpty' => false
        ),
        'signature'          => array(
            'allowEmpty' => true,
            array('StringLength', 0, 255)
        ),
        'isSignatureUsed'    => array(
            'allowEmpty' => true,
            'default'    => 0
         ),
        'inboxConnectionType' => array(
            'allowEmpty' => true,
            'default'    => null
         ),
        'outboxConnectionType' => array(
            'allowEmpty' => true,
            'default'    => null
         ),
        'portInbox'          => array(
            'allowEmpty' => true,
            'default'    => 110,
            array('Between', 0, 65535)
        ),
        'portOutbox'         => array(
            'allowEmpty' => true,
            'default'    => 25,
            array('Between', 0, 65535)
         ),
        'isCopyLeftOnServer' => array(
            'allowEmpty' => true,
            'default'    => 0
        )
    );


    /**
     * Adjusts validators based on submitted data.
     * outbox-password and -username are only needed if isOutBoxAuth was set
     * to true.
     *
     */
    protected function _adjustValidators()
    {
        switch ($this->_context) {
            case self::CONTEXT_UPDATE:
                if (!$this->_data['isOutboxAuth']) {
                   $this->_validatorRules['passwordOutbox']['allowEmpty'] = true;
                   $this->_validatorRules['usernameOutbox']['allowEmpty'] = true;
                } else {
                   $this->_validatorRules['passwordOutbox']['allowEmpty'] = false;
                   $this->_validatorRules['usernameOutbox']['allowEmpty'] = false;
                }
            break;

            case self::CONTEXT_CREATE:
               if (!$this->_data['isOutboxAuth']) {
                   $this->_validatorRules['passwordOutbox']['allowEmpty'] = true;
                   $this->_validatorRules['usernameOutbox']['allowEmpty'] = true;
               } else {
                   $this->_validatorRules['passwordOutbox']['allowEmpty'] = false;
                   $this->_validatorRules['usernameOutbox']['allowEmpty'] = false;
               }
            break;

            case self::CONTEXT_DELETE:

            break;
        }
    }

    /**
     * Returns an associative array with all filtered and validated
     * fields that where found in $_presence.
     *
     *
     * @throws Zend_Filter_Exception
     *
     * @see $_presence
     */
    public function getProcessedData()
    {
        $data = parent::getProcessedData();

        if (empty($data)) {
            return $data;
        }

        if (str_replace("*", "", $data['passwordInbox']) == "") {
            unset($data['passwordInbox']);
        }

        if ($data['isOutboxAuth']) {
            if (str_replace("*", "", $data['passwordOutbox']) == "") {
                unset($data['passwordOutbox']);
            }
        } else if (!$data['isOutboxAuth']) {
            $data['usernameOutbox'] = null;
            $data['passwordOutbox'] = null;
        }

        return $data;
    }

}