<?php

use tad_StaticMocker as S;

class StaticMockerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        S::_reset();
    }

    public function testAddingAMethodReturnValueWillReturnTheValue()
    {
        S::_addMethod('someMethod', 'someValue');
        $this->assertEquals('someValue', S::someMethod());
    }
    public function testSettingAListenerWillCallMethodsOnTheListener()
    {
        $mock = $this->getMock('stdClass', array('someMethod'));
        $mock->expects($this->once())->method('someMethod')->with('someValue');
        S::_setListener($mock);
        S::_addMethod('someMethod');
        S::someMethod('someValue');
    }
    public function testSettingAListenerWillNotCallNotSpecifiedMethodsOnTheListener()
    {
        $mock = $this->getMock('stdClass', array('someMethod'));
        $mock->expects($this->once())->method('someMethod');
        $mock->expects($this->never())->method('someOtherMethod');
        S::_setListener($mock);
        S::_addMethod('someMethod');
        S::_addMethod('someOtherMethod');
        S::someMethod('someValue');
        S::someOtherMethod();
    }
    public function testSettindListenerWithNoArgumentWillSetToNull()
    {
        $w = new stdClass();
        S::_setListener($w);
        $this->assertSame($w, S::_getListener());
        S::_setListener();
        $this->assertNull(S::_getListener());
    }
    public function testResetWillResetListenerToo()
    {
        $w = new stdClass();
        S::_setListener($w);
        $this->assertSame($w, S::_getListener());
        S::_reset();
        $this->assertNull(S::_getListener());
    }
}