<?php

/**
 * Allows for a late static binding workaroung in PHP 5.2
 *
 * PHP 5.3 and above supplies developers with the `static` keyword
 *
 *     class ParentClass
 *     {
 *         public static function call()
 *         {
 *             static::someMethod($arg1, $arg2);
 *         }
 *
 *         protected static function someMethod()
 *         {
 *             echo "from class ParentClass";
 *         }
 *     }
 *
 *     class ChildClass extends ParentClass
 *     {
 *         protected static function someMethod()
 *         {
 *             echo "from class ChildClass";
 *         }
 *     }
 *
 * Calling `ChildClass::call()` will return `from class ChildClass`.
 * The same can be obtained with the workaround provided by this class like
 *
 *     class ParentClass
 *     {
 *         public static function call()
 *         {
 *             if($class = tad_Static::getClassExtending(__CLASS__)){
 *                 return call_user_func(array($class, 'someMethod'));
 *             }
 *             return self::someMethod();
 *         }
 *
 *         protected static function someMethod()
 *         {
 *             echo "from class ParentClass";
 *         }
 *     }
 *
 *     class ChildClass extends ParentClass
 *     {
 *         protected static function init()
 *         {
 *             tad_Static::setClassExtending('ParentClass', __CLASS__);
 *         }
 *         protected static function someMethod()
 *         {
 *             echo "from class ChildClass";
 *         }
 *     }
 * 
 * Calling 
 *     ChildClass::init();
 *     ChildClass::call();
 * 
 * will now return `from class ChildClass`
 */
class tad_Static
{
    
    /**
     * An associative array of parent to child classes.
     *
     * The name of the parent class is the key, the name of the chil class is the value.
     * Will contain values like
     *
     *     array('parentClass' => 'childClass');
     *
     * @var array
     */
    protected static $classes = array();
    
    /**
     * Gets the child class extending a specific parent class.
     *
     * Given a `$classes` array like
     *
     *     array('parentClass' => 'childClass');
     *
     * then
     *
     *     tad_Static::getClassExtending('parentClass')
     *
     * will return 'childClass'.
     *
     * @param  string $parentClass The parent class to get the child class for.
     *
     * @return string/null         The name of the child class or null if the parent class has not been registered.
     */
    public static function getClassExtending($parentClass)
    {
        if (!isset(self::$classes[$parentClass])) {
            return null;
        }
        return self::$classes[$parentClass];
    }
    
    /**
     * Sets the child class extending a specified client class.
     *
     * Please note that no check is made to establish if a child class is
     * an actual extension of the parent class.
     *
     * @param string $parentClass The name of the parent class to register the child class for.
     * @param string $childClass  The name of the child class extending the parent class.
     */
    public static function setClassExtending($parentClass, $childClass)
    {
        self::$classes[$parentClass] = $childClass;
    }
    public static function reset($parentClass = null)
    {
        if (!$parentClass) {
            self::$classes = array();
        } else {
            self::$classes[$parentClass] = null;
        }
    }
    public static function call($parentClass, $method)
    {
        $args = func_get_args();
        array_shift($args);
        array_shift($args);
        $args = count($args) > 0 ? $args : array();
        $class = false;
        // if there is not a child class registered for the parent class
        // call the method on the parent class
        if (!array_key_exists($parentClass, self::$classes) and method_exists($parentClass, $method)) {
                $class = $parentClass;
            }
        else {
            // if there is a child class registered for the parent class
            // then call that class implementation of the method
            $childClass = self::$classes[$parentClass];
            if(method_exists($childClass, $method)){
                $class = $childClass;
            }
        }
        if(!$class){
            throw new BadMethodCallException("$parentClass (and its extending classes) do not define a $method method.", 1);
            
        }
        return call_user_func_array(array($class, $method), $args);
                 
    }
}
