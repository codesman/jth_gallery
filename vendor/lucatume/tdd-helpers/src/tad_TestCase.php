<?php
/**
 * An extension of PHPUnit TestCase class to allow for more streamlined adapter based testing.
 */
class tad_TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Returns a instance of a mock tad_FunctionsAdapterInterface implementing specified methods.
     * 
     * By default the `__construct` and the `__call` methods are mocked.
     *
     * @param  array  $methods A list of the methods to mock.
     *
     * @return object A mock object implementing the specified methods.
     */
    protected function getMockFunctions(array $methods = array())
    {
        $base = array(
            '__construct',
            '__call'
        );
        $mergedMethods = array_merge($base, $methods);
        return $this->getMockBuilder('tad_FunctionsAdapterInterface')->setMethods($mergedMethods)->disableOriginalConstructor()->getMock();
    }
}
