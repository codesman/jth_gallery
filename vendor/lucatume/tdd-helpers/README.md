# Test-Driven Development Helpers and Adapters

The packages provides two adapters for globally defined functions and variables and a static mocker class.  
The adapters answer to my need for a way to apply TDD techiques to WordPress plugin and theme development. 

## Adapters

### Functions adapter
Wraps call to globally defined functions in a method call. If <code>some_function</code> is a function defined in the global scope then a call to it could be made using the adapter like

    $adapter = new tad_FunctionsAdapter();
    $var = $adapter->some_function();

the adapter uses an interface for more flexible mocking in tests like

    $mockF = $this->getMock('tad_FunctionsAdapterInterface');

### Globals adapter
Allows superglobals to be accessed via an object method fashion.  
Usage example to access <code>$GLOBALS['foo']</code> is

    $g = new tad_GlobalsAdapter();
    $foo = $g->globals('foo');

To get the superglobal array call the function with no arguments, i.e.
to get the <code>$_SERVER</code> array

    $g = new tad_GlobalsAdapter();
    $g->server();

## Static Mocker
A test helper to mock (in a way and with limits) static method calls.  
Tested class should allow for class name injection like

    public function __construct($var1, $var2, $util = 'StaticClass')
    {
        $this->util = $util;

        $var = $this->util::doSomething();
    }

and then in the test file

    class StaticClass extends tad_StaticMocker
    {}


    class ClassNameTest extends \PHPUnit_Framework_TestCase
    {
        public function test_construct_calls_static_class_doSomething()
        {
            // Create a stub for the SomeClass class.
            $stub = $this->getMock('SomeClass');

            // Configure the stub.
            $stub->expects($this->any())
                ->method('doSomething')
                ->will($this->returnValue('foo'));

            StaticClass::_setListener($stub);

            $sut = new ClassName('some', 'var', 'StaticClass');
        }
    }

## Static (<code>tad_Static</code>)
Allows for a kind of late static binding missing from PHP <code>5.2</code>. 
PHP 5.3 and above supplies developers with the `static` keyword

    class ParentClass
    {
        public static function call()
        {
            static::someMethod();
        }

        protected static function someMethod()
        {
            echo "from class ParentClass";
        }
    }
    class ChildClass extends ParentClass
    {
        protected static function someMethod()
        {
            echo "from class ChildClass";
        }
    }

Calling `ChildClass::call()` will return `from class ChildClass`.
The same can be obtained with the workaround provided by this class like

    class ParentClass
    {
        public static function call()
        {
            if($class = tad_Static::getClassExtending(__CLASS__)){
                return call_user_func(array($class, 'someMethod'));
            }
            return self::someMethod();
        }

        protected static function someMethod()
        {
            echo "from class ParentClass";
        }
    }

    class ChildClass extends ParentClass
    {
        protected static function init()
        {
            tad_Static::setClassExtending('ParentClass', __CLASS__);
        }

        protected static function someMethod()
        {
            echo "from class ChildClass";
        }
    }

Calling 

    ChildClass::init();
    ChildClass::call();

will now return `from class ChildClass`.

## Changelog
* 2.2.0 - added the <code>tad_Static</code> class to the package
* 2.1.0 - added the <code>tad_TestCase</code> class to the package
* 2.0.0 - "updated" the package to be PHP <code>5.2</code> compatible with WordPress minimum requirements
* 1.1.0 - first public release