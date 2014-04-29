<?php

namespace Nstory\Phunk;

use \Mockery as m;
use Nstory\Phunk\Path as p;

class PathTest extends \PHPUnit_Framework_TestCase
{
    private $path;

    public function setUp()
    {
        $this->path = new Path();
    }

    public function test_empty_path()
    {
        $obj = new \stdClass;
        $p = p::path();
        $this->assertSame($obj, $p($obj));
    }

    public function test_property()
    {
        $p = p::path()->foo;
        $this->assertEquals(
            'xyzzy',
            $p((object)['foo' => 'xyzzy'])
        );
    }

    public function test_property_chaining()
    {
        $o = (object)[
            'foo' => (object)[
                'bar' => 'xyzzy'
            ]
        ];
        $p = p::path()->foo->bar;
        $this->assertEquals('xyzzy', $p($o));
    }

    public function test_property_null()
    {
        $p = p::path()->foo;
        $this->assertNull(null, $p(null));
    }

    public function test_property_non_existent()
    {
        $p = p::path()->foo;
        $this->assertNull($p(new \stdClass));
    }

    public function test_call()
    {
        $o = m::mock();
        $o->shouldReceive('foo')->andReturn('xyzzy');
        $p = p::path()->foo();
        $this->assertEquals('xyzzy', $p($o));
    }

    public function test_call_with_arguments()
    {
        $o = m::mock();
        $o->shouldReceive('foo')->with('blah')->andReturn('xyzzy');
        $p = p::path()->foo('blah');
        $this->assertEquals('xyzzy', $p($o));
    }

    public function test_call_chaining()
    {
        $o = m::mock();
        $o->shouldReceive('foo->bar')->andReturn('xyzzy');
        $p = p::path()->foo()->bar();
        $this->assertEquals('xyzzy', $p($o));
    }

    public function test_call_null()
    {
        $p = p::path()->foo();
        $this->assertNull($p(null));
    }

    public function test_call_non_existent()
    {
        $p = p::path()->foo();
        $this->assertNull($p(new \stdClass));
    }

    public function test_array_access()
    {
        $a = [
            'foo' => [
                'bar' => 'xyzzy'
            ]
        ];
        $p = p::path()['foo']['bar'];
        $this->assertEquals('xyzzy', $p($a));
    }

    public function test_array_access_null()
    {
        // $p = p::path()['foo'];
        $p = p::path()['foo'];
        $this->assertNull($p(null));
    }

    public function test_array_access_non_existent()
    {
        $p = p::path()['foo'];
        $this->assertNull($p([]));
    }
}
