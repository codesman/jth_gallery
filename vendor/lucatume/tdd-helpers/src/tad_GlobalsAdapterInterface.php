<?php
/**
 * Global Variables Adatper interface
 * 
 * The interface is used in the class to allow simple mocking of the class
 * using its interface in tests. PHPUnit mocking, for example:
 * 
 *     $mockG = $this->getMock('tad_GlobalsAdapterInterface');
 */
interface tad_GlobalsAdapterInterface
{
    public function __call($name, $args);
}
