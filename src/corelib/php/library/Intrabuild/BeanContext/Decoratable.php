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
 * 
 * @package Intrabuild_BeanContext
 * @subpackage BeanContext
 * @category BeanContext
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */    
interface Intrabuild_BeanContext_Decoratable {    

   /**
    * Returns the class-name of the entity the class represents
    * 
    * @return string
    */
   public function getRepresentedEntity();

   /**
    * Returns an array with all the methods of this class which can be decorated
    * by Intrabuild_BeanContext_ModelDecorator
    * 
    * @return array
    */
   public function getDecoratableMethods();

}