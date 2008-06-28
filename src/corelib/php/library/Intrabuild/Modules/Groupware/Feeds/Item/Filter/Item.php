<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: ItemFilter.php 2 2008-06-21 10:38:49Z T. Suckow $
 * $Date: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $Revision: 2 $
 * $LastChangedDate: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/php/library/Intrabuild/Modules/Groupware/Feeds/ItemFilter.php $
 */

/**
 * @see Intrabuild_Filter_Input
 */
require_once 'Intrabuild/Filter/Input.php';


/**
 * An input-filter class defining all validators and filters needed when
 * processing input data for mutating or creating feed items.
 *
 * @uses Intrabuild_Filter_Input
 * @package    Intrabuild_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Feeds_Item_Filter_Item extends Intrabuild_Filter_Input {

    const CONTEXT_READ = 'flag_item';

    protected $_defaultEscapeFilter = 'StringTrim';

    protected $_presence = array(
        'delete' =>
            array(
                'id'
            )
        ,
        'flag_item' =>
            array(
                'id',
                'isRead'
            ),
        'update' =>
            array(),
        'create' =>
            array(
                'title',
                'description',
                'content',
                'pubDate',
                'link',
                'guid',
                'author',
                'savedTimestamp',
                'groupwareFeedsAccountsId'
        )
    );

    protected $_filters = array(
        'id' => array(
            'Int'
         ),
         'groupwareFeedsAccountsId' => array(
            'Int'
         ),
        'title' => array(
            'StringTrim'
         ),
         'guid' => array(
            'StringTrim'
         ),
         'author' => array(
            'StringTrim'
         ),
        'description' => array(
            'StringTrim'
         ),
         'content' => array(
            array('StripTags',
                // allow all except object, script, embed etc.
                array(
                    'p','div','span','ul','li','table','tr','td','blockquote',
                    'strong','b','i','u','sup','sub','tt','h1','h2','h3','h4',
                    'h5','h6','small','big','br','nobr','center','ol','a','font'
                ),
                array(
                    'style', 'class','id', 'href', 'border', 'cellspacing', 'cellpadding'
                )
            ),
            array(
                'PregReplace',
                '/href="javascript:/',
                'href="/index/javascript/'
            ),
            array(
                'PregReplace',
                "/href='javascript:/",
                "href='/index/javascript/"
            ),
            'StringTrim'
         ),
        'pubDate' => array(
            'StringTrim'
         ),
        'link' => array(
            'StringTrim'
        ),
        'savedTimestamp' => array(
            'Int'
        ),
        'isRead' => array(
            'Int'
        ),
    );

    protected $_validators = array(
        'id' => array(
            'allowEmpty' => false,
            array('GreaterThan', 0)
         ),
         'groupwareFeedsAccountsId' => array(
            'allowEmpty' => false,
            array('GreaterThan', 0)
         ),
        'title' => array(
            'allowEmpty' => true
         ),
        'author' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
        'description' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
        'pubDate' => array(
            'allowEmpty' => false
         ),
         'content' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'guid' => array(
            'allowEmpty' => false
         ),
        'link' => array(
            'allowEmpty' => false
        ),
        'savedTimestamp' => array(
            'allowEmpty' => true,
            array('GreaterThan', 0)
        ),
        'isRead' => array(
           'allowEmpty' => true,
           'default'    => 0
        )
    );

    protected function _init()
    {
        $this->_validators['savedTimestamp']['default'] = time();
    }


}