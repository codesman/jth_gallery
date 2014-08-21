<?php
/**
 * A test helper to mock (in a way and with limits) static method calls.
 *
 * Tested class should allow for class name injection like
 *
 *     public function __construct($var1, $var2, $util = '\StaticClass')
 *     {
 *         $this->util = $util;
 *
 *         $var = $this->util::doSomething();
 *     }
 *
 * and then in the test file
 *
 *     class StaticClass extends tad_StaticMocker
 *     {}
 *
 *
 *     class ClassNameTest extends \PHPUnit_Framework_TestCase
 *     {
 *         public function test_calls_static_class()
 *         {
 *             // Create a stub for the SomeClass class.
 *             $stub = $this->getMock('SomeClass');
 *
 *             // Configure the stub.
 *             $stub->expects($this->any())
 *                 ->method('doSomething')
 *                 ->will($this->returnValue('foo'));
 *
 *             StaticClass::_setListener($stub);
 *
 *             $sut = new ClassName('some', 'var', '\StaticClass');
 *         }
 *     }
 *
 */
class tad_StaticMocker
{
    protected static $methods;
    protected static $listener;
    
    /**
     * Magic methods for static calls.
     *
     * Will call methods on listener object if any.
     *
     * @param  string $func The name of the method to call.
     * @param  array $args  An array of arguments for the method.
     *
     * @return mixed        Either a return value from the listener or a return value set using _addMethod.
     */
    public static function __callStatic($func, $args)
    {
        if (!is_null(self::$listener) and method_exists(self::$listener, $func)) {
            return call_user_method_array($func, self::$listener, $args);
        }
        return self::$methods[$func];
    }
    
    /**
     * Adds a method to the static methods available in the class.
     *
     * Calling the method a second time with a new return value will
     * override the first one.
     *
     * @param string $func        The method name.
     * @param mixed $returnValue  The return value for the method call.
     */
    public static function _addMethod($func, $returnValue = null)
    {
        self::$methods[$func] = $returnValue;
    }
    
    /**
     * Removes all methods and listeners from the class.
     *
     * Better used after each test.
     *
     */
    public static function _reset()
    {
        self::$methods = array();
        self::_setListener();
    }
    
    /**
     * Sets a listener object to call in the class.
     *
     * Used to set expectations and complex return values.
     *
     * @param object $listener The object that will listen and respond to method calls.
     */
    public static function _setListener($listener = null)
    {
        self::$listener = $listener;
    }
    
    /**
     * Returns the listener object if any.
     *
     * @return object The set listener.
     */
    public static function _getListener()
    {
        return self::$listener;
    }
}
