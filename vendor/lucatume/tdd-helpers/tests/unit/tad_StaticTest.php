<?php
class ParentClass
{
    public static function aMethod()
    {
        if ($class = tad_Static::getClassExtending(__CLASS__)) {
            return call_user_func(array(
                $class,
                'someMethod'
            ) , 1, 2);
        }
        return self::someMethod();
    }
    protected static function someMethod($arg1, $arg2)
    {
        return sprintf('ParentClass: %s %s', $arg1, $arg2);
    }
    public static function callAnotherMethod($arg1, $arg2)
    {
        return tad_Static::call(__CLASS__, 'anotherMethod', $arg1, $arg2);
    }
}

class ChildClass extends ParentClass
{
    public static function init()
    {
        tad_Static::setClassExtending('ParentClass', __CLASS__);
    }
    protected static function someMethod($arg1, $arg2)
    {
        return sprintf('ChildClass: %s %s', $arg1, $arg2);
    }
    public static function anotherMethod($arg1, $arg2)
    {
        return sprintf('foo %s %s', $arg1, $arg2);
    }
}

class tad_StaticTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        tad_Static::reset();
    }
    
    protected function tearDown()
    {
    }
    
    /**
     * @test
     * it should be instantiatable
     */
    public function it_should_be_instantiatable()
    {
        $this->assertInstanceOf('tad_Static', new tad_Static());
    }
    
    /**
     * @test
     * it should allow setting and getting a child class for a specified parent
     */
    public function it_should_allow_setting_and_getting_a_child_class_for_a_specified_parent()
    {
        $parentClass = __CLASS__;
        $childClass = 'SomeChildClass';
        tad_Static::setClassExtending($parentClass, $childClass);
        $this->assertEquals($childClass, tad_Static::getClassExtending($parentClass));
    }
    
    /**
     * @test
     * it should allow resetting the classes using the reset method
     */
    public function it_should_allow_resetting_the_classes_using_the_reset_method()
    {
        $parentClass = __CLASS__;
        $childClass = 'SomeChildClass';
        tad_Static::setClassExtending($parentClass, $childClass);
        $this->assertEquals($childClass, tad_Static::getClassExtending($parentClass));
        tad_Static::reset();
        $this->assertNull(tad_Static::getClassExtending(__CLASS__));
    }
    
    /**
     * @test
     * it should return null if a parent class is not registered
     */
    public function it_should_return_null_if_a_parent_class_is_not_registered()
    {
        $this->assertNull(tad_Static::getClassExtending(__CLASS__));
    }
    
    /**
     * @test
     * it should allow resetting single key value pairs using the class name in the reset method
     */
    public function it_should_allow_resetting_single_key_value_pairs_using_the_class_name_in_the_reset_method()
    {
        $parentClass1 = __CLASS__;
        $childClass1 = 'SomeChildClass';
        $parentClass2 = 'someParentClass';
        $childClass2 = 'SomeOtherChildClass';
        tad_Static::setClassExtending($parentClass1, $childClass1);
        tad_Static::setClassExtending($parentClass2, $childClass2);
        $this->assertEquals($childClass1, tad_Static::getClassExtending($parentClass1));
        $this->assertEquals($childClass2, tad_Static::getClassExtending($parentClass2));
        tad_Static::reset($parentClass1);
        $this->assertNull(tad_Static::getClassExtending($parentClass1));
        $this->assertEquals($childClass2, tad_Static::getClassExtending($parentClass2));
    }
    
    /**
     * @test
     * it should allow calling public method in the child class using call static method
     */
    public function it_should_allow_calling_public_method_in_the_child_class_using_call_static_method()
    {
        ChildClass::init();
        $out = ParentClass::callAnotherMethod('baz', 'bar');
        $this->assertEquals('foo baz bar', $out);
    }
}
