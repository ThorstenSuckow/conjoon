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
 *
 * @package Conjoon_BeanContext
 * @subpackage BeanContext
 * @category BeanContext
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
interface Conjoon_BeanContext_Decoratable {

   /**
    * Returns the class-name of the entity the class represents
    *
    * @return string
    */
   public function getRepresentedEntity();

   /**
    * Returns an array with all the methods of this class which can be decorated
    * by Conjoon_BeanContext_ModelDecorator
    *
    * @return array
    */
   public function getDecoratableMethods();

}