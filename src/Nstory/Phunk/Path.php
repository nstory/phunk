<?php

namespace Nstory\Phunk;

/**
 * Allows you to create a "path" through an object tree.
 */
class Path implements \ArrayAccess
{
    private $path = [];

    /**
     * @return Nstory\Phunk\Path
     */
    public static function path()
    {
        return new Path;
    }

    public function __call($name, $args)
    {
        $this->path[] = function($e) use ($name, $args) {
            $call = [$e, $name];
            return is_callable($call) ?
                call_user_func_array($call, $args) : null;
        };
        return $this;
    }

    public function __get($name)
    {
        $this->path[] = function($e) use($name) {
            return isset($e->{$name}) ? $e->{$name} : null;
        };
        return $this;
    }

    public function __invoke($e)
    {
        foreach ($this->path as $f) {
            if ($e === null) {
                return $e;
            }
            $e = $f($e);
        }
        return $e;
    }

    public function offsetGet($offset)
    {
        $this->path[] = function($e) use($offset) {
            return isset($e[$offset]) ? $e[$offset] : null;
        };
        return $this;
    }

    public function offsetExists($offset)
    {
        return true;
    }

    public function offsetSet($offset, $value)
    {
    }

    public function offsetUnset($offset)
    {
    }
}
