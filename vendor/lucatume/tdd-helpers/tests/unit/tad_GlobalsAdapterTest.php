<?php
/**
 * GlobalsTest
 */
class GlobalsTest extends \PHPUnit_Framework_TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new tad_GlobalsAdapter();
    }
    public function testWillReturnTheGlobalsArray()
    {
        $r = $this->sut->globals();
        $this->assertSame($GLOBALS,$r);
    }
    public function testWillReturnTheServerArray()
    {
        $r = $this->sut->server();
        $this->assertSame($_SERVER, $r);
    }
    public function testWillReturnTheFooGlobalVariable()
    {
        $GLOBALS['foo'] = 'baz';
        $r = $this->sut->globals('foo');
        $this->assertEquals('baz',$r);
    }
    public function testItShouldAllowAcccessingScalarGlobalVarsDirectly()
    {
        global $someVar;
        $someVar = 'someValue';
        $this->assertEquals($this->sut->someVar(), 'someValue');
    }
    public function testItShouldAllowAccessingArrayGlobalVarDirectly()
    {
        global $someVar;
        $someVar = array('someValue');
        $this->assertEquals($this->sut->someVar(0), array('someValue'));
    }
    public function testItShouldAllowAccessingAssociativeArrayGlobalVarDirectly()
    {
        global $someVar;
        $someVar = array('foo' => 'someValue');
        $this->assertEquals($this->sut->someVar('foo'), 'someValue');
    }
}
