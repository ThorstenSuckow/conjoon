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
 * Interface for classes used in contexts where they act as "beans", following
 * the conventions of JavaBeans.
 *
 * A bean context is a programmatically environment, where objects can be
 * serialized for persistent storage, or where their properties can be
 * introspected in a convinient way.
 *
 * A class defines itself in bean context, when it adheres to the following
 * conventions:
 * <ul>
 *  <li>
 *    <strong>Class name</strong> <br/>
 *      No restrictions to the name of the class.
 *  </li>
 *  <li>
 *    <strong>Interface</strong> <br/>
 *      The class implementsthe <tt>Conjoon_BeanContext</tt>
 *      interface and the interface <tt>Serializable</tt>.
 *  </li>
 *  <li>
 *    <strong>Superclass</strong> <br/>
 *      Any class can be extended.
 *  </li>
 *  <li>
 *    <strong>Constructor</strong> <br/>
 *      A parameter-less constructor must be implemented. The body of the
 *      constructor may invoke any other action (such as setting default
 *      properties).
 *  </li>
 *  <li>
 *    <strong>Properties</strong> <br/>
 *      A bean defines a property <tt>x</tt> of the type <tt>Y</tt>, if it has
 *      accessors adhering the following conventions
 *      <ul>
 *         <li>
 *           <strong>Getter</strong><br />
 *            <tt>public function getX() : Y</tt>
 *         </li>
 *         <li>
 *           <strong>Boolean getter</strong><br />
 *            <tt>public function isX() : boolean</tt>
 *         </li>
 *         <li>
 *           <strong>Setter</strong><br />
 *            <tt>public function setX(a:Y) : void</tt>
 *         </li>
 *         <li>
 *           <strong>Exceptions</strong><br />
 *            Property accessors may throw any type of exception.
 *         </li>
 *      </ul>
 *  </li>
 *  <li>
 *    <strong>Indexed properties</strong> <br/>
 *      A bean defines an indexed property <tt>x</tt> of the type <tt>Array</tt>, if it has
 *      accessors adhering the following conventions
 *      <ul>
 *         <li>
 *           <strong>Array getter</strong><br />
 *            <tt>public function getX() : Array</tt>
 *         </li>
*         <li>
 *           <strong>Element getter</strong><br />
 *            <tt>public function getX(index:int) : Array[index]</tt>
 *         </li>
 *         <li>
 *           <strong>Array setter</strong><br />
 *            <tt>public function setX(Array) : void</tt>
 *         </li>
*         <li>
 *           <strong>Element setter</strong><br />
 *            <tt>public function setX(index:int, value:Y) : void</tt>
 *         </li>
 *         <li>
 *           <strong>Exceptions</strong><br />
 *            Indexed property accessor may throw any exception.
 *            However, they are advised to through exceptions,
 *            indicating errors related to the bounds of an indexed property.
 *         </li>
 *      </ul>
 *  </li>
 *
 *
 *
 * @uses       Serializable
 * @category   Conjoon
 * @package    Conjoon_BeanContext
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */
interface Conjoon_BeanContext {

    /**
     * Returns all persistent properties of this object as an associative
     * array.
     *
     * @return string
     */
    public function toArray();

    /**
     * Returns a textual representation of this object.
     *
     * @return string
     */
    public function __toString();

    /**
     * Returns a DTO for an instance of this class.
     *
     * @return Object
     */
    public function getDto();


}