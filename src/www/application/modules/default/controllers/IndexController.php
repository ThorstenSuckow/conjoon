<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
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
 * Zend_Controller_Action
 */
require_once 'Zend/Controller/Action.php';

class IndexController extends Zend_Controller_Action {

    public function phpinfoAction()
    {
        echo phpinfo();
        die();
    }

    public function sandboxAction()
    {
        var_dump(md5(uniqid(rand(), true)));
        die();

        require_once 'Intrabuild/Filter/MimeDecodeHeader.php';

        $filter = new Intrabuild_Filter_MimeDecodeHeader();

        $subjects = array(
            "=?UTF-8?Q?Xing:_Jennifer_Suckow_m=c3=b6cht?= =?UTF-8?Q?e_Sie_als_Kontakt_hinzuf=c3=bcgen?=",
            "Xing: Steffen Hiller hat\r\n=?UTF-8?Q?Sie_als_Kontakt_best=c3=a4tigt?=",
            "=?UTF-8?Q?[GUI_0000741]:_OBELIXgui2_-_l=C3=A4sst_sich_im_IE_7.0_nicht_=C3?=\r\n=?UTF-8?Q?=B6ffnen?=",
            "=?utf-8?Q?iPersonic_News=3a_Mach_mehr_aus_deinem_Pers=c3=b6nlichkeitstyp=21?="
        );


        for ($i = 0, $len = count($subjects); $i < $len; $i++) {
            $filtered = $filter->filter($subjects[$i]);
            echo "<pre>";
            var_dump($filtered);
        }





        die();
    }

    public function indexAction()
    {

    }

    /**
     * Default action for href-attributes that contained a link in the pattern
     * of "href='javascript:...'". Every link from cross domains that gets intercepted
     * should be edited to link to this action. The view will notify the user
     * of the inproper link.
     */
    public function javascriptAction()
    {


    }

    /**
     * Default action for redirecting to links not part of the intrabuild application.
     *
     */
    public function redirectAction()
    {
        $link = $this->_request->getParam('url');
        $this->_redirect(urldecode($link));

        die();
    }
}