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


namespace Conjoon\Data\Repository\Mail;

use Conjoon\Argument\ArgumentCheck;

/**
 * @see \Conjoon\Data\Repository\DefaultDataRepository
 */
require_once 'Conjoon/Data/Repository/DefaultDataRepository.php';

/**
 * @see \Conjoon\Data\Repository\Mail\MailFolderRepository
 */
require_once 'Conjoon/Data/Repository/Mail/MailFolderRepository.php';

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';


/**
 * The default implementation for the MailfolderRepository.
 * Uses Zend_Db_Table for backward compatibility.
 *
 * @category   Conjoon_Data
 * @package    Repository
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMailFolderRepository extends \Conjoon\Data\Repository\DefaultDataRepository
    implements MailFolderRepository {

    /**
     * @var Conjoon_Db_Table
     */
    protected $_table;


    /**
     * Creates a new instance of this class.
     *
     */
    public function __construct()
    {
        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Model_Folder
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/Folder.php';

        $this->_table = new \Conjoon_Modules_Groupware_Email_Folder_Model_Folder();
    }

    /**
     * @inheritdoc
     */
    public function _persist(\Conjoon\Data\Entity\DataEntity $entity)
    {
        if ($entity->getParent() !== null) {
            $this->_persist($entity->getParent());
        }

        $data = array(
            'id'               => (int)trim($entity->getId()),
            'name'             => (string)trim($entity->getName()),
            'is_child_allowed' => (int)(bool)$entity->getIsChildAllowed(),
            'is_locked'        => (int)(bool)$entity->getIsLocked(),
            'type'             => (string)trim($entity->getType()),
            'meta_info'        => (string)trim( $entity->getMetaInfo()),
            'parent_id'        => $entity->getParent()
                                  ? $entity->getParent()->getId()
                                  : 0,
            'is_deleted'       => (int)(bool)$entity->getIsDeleted()
        );

        $id = $data['id'];
        unset($data['id']);

        if ($id <= 0) {
            // update
            $where = $this->_table->getAdapter()->quoteInto('id = ?', $id);
            $this->_table->update($data, $where);
        } else {
            // insert
            $id = $this->_table->insert($data);
            if ($id > 0) {
                $entity->setId($id);
            } else {
                return null;
            }
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    protected function _remove(\Conjoon\Data\Entity\DataEntity $entity)
    {
        $data = array('id' => $entity->getId());

        ArgumentCheck::check(array(
            'id' => array(
                'type'        => 'int',
                'allowEmpty'  => false,
                'greaterThan' => 0
            )
        ), $data);

        $id = $data['id'];

        $where = $this->_table->getAdapter()->quoteInto('id = ?', $id);

        $ret = $this->_table->delete($where);

        $entity->setId($ret ? 0 : $id);

        return $ret;
    }


    /**
     * @inheritdoc
     */
    protected function _findById($id)
    {
        $rows = $this->_table->find($id);

        if (!$rows) {
            return null;
        }
        $row = $rows[0];

        $className = self::getEntityClassName();

        $entity = new $className;

        $entity->setId((int)$row->id);
        $entity->setName($row->name);
        $entity->setIsChildAllowed((bool)(int)$row->is_child_allowed);
        $entity->setIsLocked((bool)(int)$row->is_locked);
        $entity->setType($row->type);
        $entity->setMetaInfo($row->meta_info);

        if ($row->parent_id) {

            /**
             * @see \Conjoon\Data\Entity\Mail\Proxy\DefaultMailFolderEntityProxy
             */
            require_once 'Conjoon/Data/Entity/Mail/Proxy/DefaultMailFolderEntityProxy.php';

            $entity->setParent(
                new \Conjoon\Data\Entity\Mail\Proxy\DefaultMailFolderEntityProxy(
                    $this, (int)$row->parent_id
                )
            );
        }

        $entity->setIsDeleted((bool)(int)$row->is_deleted);

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public static function getEntityClassName()
    {
        return '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity';
    }
}