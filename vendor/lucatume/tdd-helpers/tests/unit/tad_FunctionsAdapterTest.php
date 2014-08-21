<?php

class tad_FunctionsAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

        /**
         * @test
         * it should be instantiatable using autoload
         */
        public function it_should_be_instantiatable_using_autoload()
        {
            $class = 'tad_FunctionsAdapter';
            $this->assertInstanceOf($class, new $class);
        }
}