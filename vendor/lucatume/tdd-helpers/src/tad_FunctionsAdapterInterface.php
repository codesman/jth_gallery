<?php
/**
 * Functions Adatper interface
 * 
 * The interface is used in the class to allow simple mocking of the class
 * using its interface in tests. PHPUnit mocking, for example:
 * 
 *     $mockF = $this->getMock('TAD_FunctionsAdapterInterface');
 */
interface tad_FunctionsAdapterInterface
{
    public function __call($function, $arguments);
}
